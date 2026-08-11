<?php
/**
 * 產生測試資料用的房屋示意圖
 * 使用方式：php !SQL/generate_images.php
 * 產生的檔案位於 uploads/sample-{房屋編號}-{序號}.png
 */

declare(strict_types=1);

$moduleRoot      = dirname(__DIR__);
$uploadDirectory = $moduleRoot . DIRECTORY_SEPARATOR . 'uploads';

if (!is_dir($uploadDirectory)) {
    mkdir($uploadDirectory, 0777, true);
}

// 每張圖的底色，讓不同房屋的圖片易於分辨
$palette = [
    [0x2F, 0x4F, 0x6F], [0x4F, 0x6F, 0x4F], [0x6F, 0x4F, 0x4F],
    [0x5A, 0x4F, 0x6F], [0x6F, 0x5F, 0x3F], [0x3F, 0x5F, 0x6F],
];

$houseCount = 24;
$perHouse   = 3;
$created    = 0;

for ($houseId = 1; $houseId <= $houseCount; $houseId++) {
    for ($index = 1; $index <= $perHouse; $index++) {
        $image = imagecreatetruecolor(800, 600);

        [$red, $green, $blue] = $palette[($houseId + $index) % count($palette)];
        $background = imagecolorallocate($image, $red, $green, $blue);
        $foreground = imagecolorallocate($image, 0xFF, 0xFF, 0xFF);
        $accent     = imagecolorallocate($image, min($red + 40, 255), min($green + 40, 255), min($blue + 40, 255));

        imagefilledrectangle($image, 0, 0, 800, 600, $background);

        // 簡單的房子圖形
        imagefilledrectangle($image, 250, 280, 550, 500, $accent);
        imagefilledpolygon($image, [400, 180, 220, 300, 580, 300], $accent);
        imagefilledrectangle($image, 370, 400, 430, 500, $background);

        $label = sprintf('HOUSE %02d - %d', $houseId, $index);
        imagestring($image, 5, 320, 530, $label, $foreground);

        $path = $uploadDirectory . DIRECTORY_SEPARATOR . sprintf('sample-%d-%d.png', $houseId, $index);
        imagepng($image, $path);
        imagedestroy($image);

        $created++;
    }
}

echo "已產生 {$created} 張示意圖於 {$uploadDirectory}" . PHP_EOL;
