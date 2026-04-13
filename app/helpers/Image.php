<?php
/**
 * Image Upload & Resize Helper
 * Uses PHP GD library to handle property images
 */

class Image {
    
    /**
     * Upload and process a single image
     * Creates 3 sizes: original, medium, thumb
     * 
     * @param array $file $_FILES['field'] array
     * @return string|false Filename on success, false on failure
     */
    public static function upload($file) {
        // Validate file
        if (!self::validate($file)) {
            return false;
        }
        
        // Generate unique filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = uniqid('prop_', true) . '.' . $ext;
        
        // Ensure upload directories exist
        self::ensureDirectories();
        
        // Process each size
        $sizes = [
            'original' => [IMG_ORIGINAL_WIDTH, IMG_ORIGINAL_HEIGHT, IMG_QUALITY_ORIGINAL],
            'medium'   => [IMG_MEDIUM_WIDTH, IMG_MEDIUM_HEIGHT, IMG_QUALITY_MEDIUM],
            'thumb'    => [IMG_THUMB_WIDTH, IMG_THUMB_HEIGHT, IMG_QUALITY_THUMB],
        ];
        
        foreach ($sizes as $sizeName => $dims) {
            $destPath = UPLOAD_PATH . '/properties/' . $sizeName . '/' . $filename;
            if (!self::resize($file['tmp_name'], $destPath, $dims[0], $dims[1], $dims[2])) {
                // Clean up any already created files
                self::delete($filename);
                return false;
            }
        }
        
        return $filename;
    }
    
    /**
     * Upload multiple images
     * 
     * @param array $files Restructured $_FILES array
     * @return array Array of uploaded filenames
     */
    public static function uploadMultiple($files) {
        $uploaded = [];
        
        foreach ($files as $file) {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $filename = self::upload($file);
                if ($filename) {
                    $uploaded[] = $filename;
                }
            }
        }
        
        return $uploaded;
    }
    
    /**
     * Validate an uploaded file
     */
    public static function validate($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        // Check file size
        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return false;
        }
        
        // Check extension
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ALLOWED_EXTENSIONS)) {
            return false;
        }
        
        // Check MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, ALLOWED_MIME_TYPES)) {
            return false;
        }
        
        // Verify it's actually an image
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Resize an image to fit within max dimensions while maintaining aspect ratio
     */
    public static function resize($source, $destination, $maxWidth, $maxHeight, $quality = 85) {
        $imageInfo = @getimagesize($source);
        if ($imageInfo === false) return false;
        
        $srcWidth = $imageInfo[0];
        $srcHeight = $imageInfo[1];
        $type = $imageInfo[2];
        
        // Create source image resource
        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = @imagecreatefromjpeg($source);
                break;
            case IMAGETYPE_PNG:
                $srcImage = @imagecreatefrompng($source);
                break;
            case IMAGETYPE_WEBP:
                $srcImage = @imagecreatefromwebp($source);
                break;
            default:
                return false;
        }
        
        if (!$srcImage) return false;
        
        // Calculate new dimensions maintaining aspect ratio
        $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight);
        
        // Don't upscale
        if ($ratio >= 1) {
            $newWidth = $srcWidth;
            $newHeight = $srcHeight;
        } else {
            $newWidth = (int)round($srcWidth * $ratio);
            $newHeight = (int)round($srcHeight * $ratio);
        }
        
        // Create destination image
        $dstImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($type === IMAGETYPE_PNG) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 0, 0, 0, 127);
            imagefill($dstImage, 0, 0, $transparent);
        }
        
        // Resize
        imagecopyresampled(
            $dstImage, $srcImage,
            0, 0, 0, 0,
            $newWidth, $newHeight,
            $srcWidth, $srcHeight
        );
        
        // Save based on output format
        $ext = strtolower(pathinfo($destination, PATHINFO_EXTENSION));
        $result = false;
        
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $result = imagejpeg($dstImage, $destination, $quality);
                break;
            case 'png':
                $pngQuality = (int)(9 - (9 * $quality / 100));
                $result = imagepng($dstImage, $destination, $pngQuality);
                break;
            case 'webp':
                $result = imagewebp($dstImage, $destination, $quality);
                break;
        }
        
        // Free memory
        imagedestroy($srcImage);
        imagedestroy($dstImage);
        
        return $result;
    }
    
    /**
     * Delete all size versions of an image
     */
    public static function delete($filename) {
        $sizes = ['original', 'medium', 'thumb'];
        foreach ($sizes as $size) {
            $path = UPLOAD_PATH . '/properties/' . $size . '/' . $filename;
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }
    
    /**
     * Get image URL for a specific size
     */
    public static function getUrl($filename, $size = 'thumb') {
        if (empty($filename)) {
            return BASE_URL . '/public/assets/img/no-image.svg';
        }
        return UPLOAD_URL . '/properties/' . $size . '/' . $filename;
    }
    
    /**
     * Ensure upload directories exist
     */
    private static function ensureDirectories() {
        $dirs = [
            UPLOAD_PATH . '/properties/original',
            UPLOAD_PATH . '/properties/medium',
            UPLOAD_PATH . '/properties/thumb',
        ];
        
        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
        }
    }
    
    /**
     * Restructure $_FILES array for multiple uploads
     * Transforms the PHP multi-file structure into an array of individual file arrays
     */
    public static function restructureFiles($files) {
        $result = [];
        if (!isset($files['name']) || !is_array($files['name'])) {
            return [$files];
        }
        
        $count = count($files['name']);
        for ($i = 0; $i < $count; $i++) {
            $result[] = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];
        }
        
        return $result;
    }
}
