<?php

declare(strict_types=1);

/**
 * Generates test JPEGs for seed data (property_images: sample-1-main.jpg … sample-8-main.jpg).
 * Run from project root: php install/generate-sample-property-images.php
 * Requires PHP GD extension.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once dirname(__DIR__) . '/bootstrap.php';

if (!extension_loaded('gd')) {
    fwrite(STDERR, "PHP GD extension is required.\n");
    exit(1);
}

$palettes = [
    [44, 97, 242],
    [0, 166, 153],
    [34, 139, 34],
    [212, 165, 116],
    [124, 58, 237],
    [120, 83, 46],
    [234, 88, 12],
    [71, 85, 105],
];

for ($i = 1; $i <= 8; $i++) {
    $tmp = buildSampleJpeg($i, $palettes[$i - 1]);
    if ($tmp === null) {
        fwrite(STDERR, "Failed to build temp image for #$i\n");
        exit(1);
    }
    $base = 'sample-' . $i . '-main';
    $saved = Image::processAndSave($tmp, $base, 'jpg');
    @unlink($tmp);
    if ($saved === null) {
        fwrite(STDERR, "Image::processAndSave failed for $base\n");
        exit(1);
    }
    echo "OK: uploads/properties/{{original,medium,thumb}}/$saved\n";
}

echo "Done. 8 listings × 3 sizes (+ optional .webp copies).\n";

/**
 * @param array{0:int,1:int,2:int} $rgb
 */
function buildSampleJpeg(int $index, array $rgb): ?string
{
    $w = 1600;
    $h = 1066;
    $im = imagecreatetruecolor($w, $h);
    if ($im === false) {
        return null;
    }

    $r = $rgb[0];
    $g = $rgb[1];
    $b = $rgb[2];
    for ($y = 0; $y < $h; $y++) {
        $t = $y / max(1, $h - 1);
        $r2 = (int) round($r + (255 - $r) * $t * 0.35);
        $g2 = (int) round($g + (255 - $g) * $t * 0.35);
        $b2 = (int) round($b + (255 - $b) * $t * 0.35);
        $line = imagecolorallocate($im, min(255, $r2), min(255, $g2), min(255, $b2));
        imageline($im, 0, $y, $w, $y, $line);
    }

    $cx = (int) ($w * 0.5);
    $cy = (int) ($h * 0.42);
    $ell = imagecolorallocate($im, min(255, $r + 50), min(255, $g + 50), min(255, $b + 40));
    imagefilledellipse($im, $cx, $cy, (int) ($w * 0.72), (int) ($h * 0.42), $ell);

    $white = imagecolorallocate($im, 255, 255, 255);
    $shadow = imagecolorallocate($im, 20, 30, 45);
    $label = 'InfoKobuleti #' . $index;
    imagestring($im, 5, 51, 51, $label, $shadow);
    imagestring($im, 5, 50, 50, $label, $white);

    $tmp = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'ik_sample_' . $index . '_' . bin2hex(random_bytes(4)) . '.jpg';
    if (!imagejpeg($im, $tmp, 92)) {
        imagedestroy($im);
        return null;
    }
    imagedestroy($im);

    return $tmp;
}
