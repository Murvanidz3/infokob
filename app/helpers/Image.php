<?php

declare(strict_types=1);

require_once dirname(__DIR__, 2) . '/config/config.php';

class Image
{
    private const ALLOWED_EXT = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * @return array{base:string, ext:string}|null
     */
    public static function validateUpload(array $file): ?array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        if (($file['size'] ?? 0) > IMAGE_MAX_BYTES) {
            return null;
        }
        $tmp = $file['tmp_name'] ?? '';
        if (!is_uploaded_file($tmp)) {
            return null;
        }
        $info = @getimagesize($tmp);
        if ($info === false) {
            return null;
        }
        $mime = $info['mime'] ?? '';
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/webp' => 'webp',
        ];
        if (!isset($map[$mime])) {
            return null;
        }
        $ext = $map[$mime];
        if (function_exists('finfo_open')) {
            $f = finfo_open(FILEINFO_MIME_TYPE);
            $detected = $f ? finfo_file($f, $tmp) : false;
            if ($f) {
                finfo_close($f);
            }
            if (is_string($detected) && !isset($map[$detected])) {
                return null;
            }
        }
        $base = uniqid('img_', true) . '_' . bin2hex(random_bytes(4));
        return ['base' => $base, 'ext' => $ext];
    }

    /**
     * Save uploaded file to original/medium/thumb. Returns final filename stem + extension or null.
     */
    public static function processAndSave(string $tmpPath, string $baseName, string $ext): ?string
    {
        $ext = strtolower($ext);
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            return null;
        }
        $filename = $baseName . '.' . $ext;
        $dirO = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'properties' . DIRECTORY_SEPARATOR . 'original';
        $dirM = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'properties' . DIRECTORY_SEPARATOR . 'medium';
        $dirT = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'properties' . DIRECTORY_SEPARATOR . 'thumb';
        foreach ([$dirO, $dirM, $dirT] as $d) {
            if (!is_dir($d) && !mkdir($d, 0755, true) && !is_dir($d)) {
                return null;
            }
        }

        $src = self::createImageResource($tmpPath, $ext);
        if ($src === null) {
            return null;
        }
        $w = imagesx($src);
        $h = imagesy($src);

        self::saveResized($src, $w, $h, $dirO . DIRECTORY_SEPARATOR . $filename, $ext, IMAGE_ORIGINAL_MAX_W, IMAGE_ORIGINAL_MAX_H, 85);
        self::saveResized($src, $w, $h, $dirM . DIRECTORY_SEPARATOR . $filename, $ext, IMAGE_MEDIUM_W, IMAGE_MEDIUM_H, 80);
        self::saveResized($src, $w, $h, $dirT . DIRECTORY_SEPARATOR . $filename, $ext, IMAGE_THUMB_W, IMAGE_THUMB_H, 75);
        imagedestroy($src);

        if (function_exists('imagewebp') && $ext !== 'webp') {
            self::trySaveWebpCopies($baseName, $ext, $filename);
        }

        return $filename;
    }

    private static function trySaveWebpCopies(string $baseName, string $ext, string $filename): void
    {
        foreach (['original', 'medium', 'thumb'] as $sub) {
            $path = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'properties' . DIRECTORY_SEPARATOR . $sub . DIRECTORY_SEPARATOR . $filename;
            if (!is_readable($path)) {
                continue;
            }
            $im = self::createImageResource($path, $ext);
            if ($im === null) {
                continue;
            }
            $webpName = $baseName . '.webp';
            $webpPath = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'properties' . DIRECTORY_SEPARATOR . $sub . DIRECTORY_SEPARATOR . $webpName;
            imagewebp($im, $webpPath, 82);
            imagedestroy($im);
        }
    }

    /**
     * @return resource|\GdImage|null
     */
    private static function createImageResource(string $path, string $ext)
    {
        return match ($ext) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($path),
            'png' => @imagecreatefrompng($path),
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null,
            default => null,
        };
    }

    /**
     * @param resource|\GdImage $src
     */
    private static function saveResized($src, int $w, int $h, string $destPath, string $ext, int $maxW, int $maxH, int $quality): void
    {
        $scale = min($maxW / $w, $maxH / $h, 1.0);
        $nw = max(1, (int) round($w * $scale));
        $nh = max(1, (int) round($h * $scale));
        $dst = imagecreatetruecolor($nw, $nh);
        if ($ext === 'png' || $ext === 'webp') {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
        }
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
        self::writeImage($dst, $destPath, $ext, $quality);
        imagedestroy($dst);
    }

    /**
     * @param resource|\GdImage $im
     */
    private static function writeImage($im, string $path, string $ext, int $quality): void
    {
        match ($ext) {
            'jpg', 'jpeg' => imagejpeg($im, $path, $quality),
            'png' => imagepng($im, $path, (int) round(9 - ($quality / 100) * 9)),
            'webp' => imagewebp($im, $path, $quality),
            default => imagejpeg($im, $path, $quality),
        };
    }

    public static function deleteFiles(?string $filename): void
    {
        if ($filename === null || $filename === '' || str_contains($filename, '..')) {
            return;
        }
        foreach (['original', 'medium', 'thumb'] as $sub) {
            $dir = UPLOAD_PATH . DIRECTORY_SEPARATOR . 'properties' . DIRECTORY_SEPARATOR . $sub;
            $path = $dir . DIRECTORY_SEPARATOR . $filename;
            if (is_file($path)) {
                @unlink($path);
            }
            $stem = pathinfo($filename, PATHINFO_FILENAME);
            $webp = $dir . DIRECTORY_SEPARATOR . $stem . '.webp';
            if (is_file($webp)) {
                @unlink($webp);
            }
        }
    }

    public static function getImageUrl(string $filename, string $size = 'medium'): string
    {
        $size = in_array($size, ['original', 'medium', 'thumb'], true) ? $size : 'medium';
        return rtrim(UPLOAD_URL, '/') . '/properties/' . $size . '/' . rawurlencode($filename);
    }
}
