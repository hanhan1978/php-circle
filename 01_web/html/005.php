<?php

// 画像を作成
$img = imagecreatetruecolor(400, 400);

// 色を割り当て
$white = imagecolorallocate($img, 255, 255, 255);
$blue = imagecolorallocate($img, 0, 0, 255);

// 背景を白で塗りつぶし
imagefilledrectangle($img, 0, 0, 400, 400, $white);

// 円を描画
imagefilledellipse($img, 200, 200, 300, 300, $blue);

// バッファリングして画像データを取得
ob_start();
imagepng($img);
$image_data = ob_get_clean();

// メモリ解放
imagedestroy($img);

// base64エンコードしてimg要素で表示
echo '<img src="data:image/png;base64,' . base64_encode($image_data) . '">';

echo '<hr>';
highlight_file(__FILE__);
