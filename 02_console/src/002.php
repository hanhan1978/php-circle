<?php

// ブロック文字で塗りつぶし円を描く
$size = 41;
$center = $size / 2;
$radius = $size / 2 - 1;

for ($y = 0; $y < $size; $y++) {
    $hasCircle = false;
    $line = '';

    for ($x = 0; $x < $size; $x++) {
        // 文字が縦長なので、Y軸に補正係数をかける
        $distance = sqrt(pow($x - $center, 2) + pow(($y - $center) * 2.0, 2));

        if ($distance <= $radius) {
            $line .= '█';
            $hasCircle = true;
        } else {
            $line .= ' ';
        }
    }

    // 円が含まれる行のみ出力
    if ($hasCircle) {
        echo $line . "\n";
    }
}
