<?php

// アスキーアートで円を描く
$size = 82; // グリッドのサイズ（奇数）
$center = $size / 2;
$radius = $size / 2 - 1;

echo '<pre style="line-height: 0.5; font-size: 12px; font-family: monospace;">';
for ($y = 0; $y < $size; $y++) {
    for ($x = 0; $x < $size; $x++) {
        // 中心からの距離を計算
        $distance = sqrt(pow($x - $center, 2) + pow($y - $center, 2));
        
        // 円の境界付近かどうか判定
        if (abs($distance - $radius) < 1.5) {
            echo '*';
        } else {
            echo ' ';
        }
    }
    echo "\n";
}
echo '</pre>';

echo '<hr>';
highlight_file(__FILE__);
