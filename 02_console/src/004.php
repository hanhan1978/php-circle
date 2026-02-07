<?php

require __DIR__ . '/../vendor/autoload.php';

use PhpTui\Tui\DisplayBuilder;
use PhpTui\Tui\Extension\Core\Widget\CanvasWidget;
use PhpTui\Tui\Extension\Core\Shape\CircleShape;
use PhpTui\Tui\Color\AnsiColor;
use PhpTui\Tui\Canvas\Marker;

// Inlineビューポート（高さ22行）
$display = DisplayBuilder::default()
    ->inline(22)
    ->build();

// 文字は縦長なので、boundsで補正
$display->draw(
    CanvasWidget::fromIntBounds(0, 88, 0, 44)
        ->marker(Marker::Dot)
        ->draw(
            CircleShape::fromScalars(44, 22, 20)
                ->color(AnsiColor::Blue)
        )
);

echo "\n";
