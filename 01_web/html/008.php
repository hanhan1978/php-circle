<?php

// ピクセルアート風の円を描く（滑らか版）
$size = 80; // グリッドのサイズを大きく
$center = $size / 2;
$radius = $size / 2 - 4;

echo '<div style="display: inline-block;">';
for ($y = 0; $y < $size; $y++) {
    echo '<div style="height: 5px; line-height: 0;">';
    for ($x = 0; $x < $size; $x++) {
        // 中心からの距離を計算
        $distance = sqrt(pow($x - $center, 2) + pow($y - $center, 2));

        // 円の内側かどうか判定（アンチエイリアシング付き）
        if ($distance <= $radius) {
            echo '<span style="display: inline-block; width: 5px; height: 5px; background: blue;"></span>';
        } else if ($distance <= $radius + 1) {
            // 境界を半透明にして滑らかに
            echo '<span style="display: inline-block; width: 5px; height: 5px; background: rgba(0, 0, 255, 0.5);"></span>';
        } else {
            echo '<span style="display: inline-block; width: 5px; height: 5px;"></span>';
        }
    }
    echo '</div>';
}
echo '</div>';

echo '<hr>';
highlight_file(__FILE__);
