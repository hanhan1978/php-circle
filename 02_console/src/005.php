<?php

require __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Tui\Render\RenderContext;
use Symfony\Component\Tui\Tui;
use Symfony\Component\Tui\Widget\AbstractWidget;
use Symfony\Component\Tui\Widget\FocusableInterface;
use Symfony\Component\Tui\Widget\FocusableTrait;
use Symfony\Component\Tui\Widget\KeybindingsTrait;
use Symfony\Component\Tui\Widget\QuitableTrait;

/**
 * ターミナル全体に円を描くウィジェット
 *
 * - 画面サイズに合わせて円のサイズを自動調整
 * - 文字の縦横比（約 2:1）を補正して真円に見えるよう調整
 * - 'q' または Ctrl+C で終了
 */
class CircleWidget extends AbstractWidget implements FocusableInterface
{
    use FocusableTrait;
    use KeybindingsTrait;
    use QuitableTrait;

    public function handleInput(string $data): void
    {
        if ($data === 'q' || $data === "\x03") {
            $this->dispatchQuit();
        }
    }

    public function render(RenderContext $context): array
    {
        $width  = $context->getColumns();
        $height = $context->getRows();

        $cx = ($width - 1) / 2.0;
        $cy = ($height - 1) / 2.0;

        // 文字は縦長（高さ約2倍）なので、横方向半径と縦方向半径を分けて扱う
        $rx = min($cx, $cy * 2) - 1; // 横半径（列単位）
        $ry = $rx / 2.0;             // 縦半径（行単位）

        $grid = array_fill(0, $height, array_fill(0, $width, ' '));

        // 行スキャン: 各行で楕円と交わる左右の x を打つ（左右の側面をカバー）
        for ($y = 0; $y < $height; $y++) {
            $dy = ($y - $cy) / $ry;
            if (abs($dy) > 1.0) {
                continue;
            }
            $dx = sqrt(1.0 - $dy * $dy) * $rx;
            $xl = (int)round($cx - $dx);
            $xr = (int)round($cx + $dx);
            if ($xl >= 0 && $xl < $width) {
                $grid[$y][$xl] = '*';
            }
            if ($xr >= 0 && $xr < $width) {
                $grid[$y][$xr] = '*';
            }
        }

        // 列スキャン: 各列で楕円と交わる上下の y を打つ（上下の弧をカバー）
        for ($x = 0; $x < $width; $x++) {
            $dx = ($x - $cx) / $rx;
            if (abs($dx) > 1.0) {
                continue;
            }
            $dy = sqrt(1.0 - $dx * $dx) * $ry;
            $yt = (int)round($cy - $dy);
            $yb = (int)round($cy + $dy);
            if ($yt >= 0 && $yt < $height) {
                $grid[$yt][$x] = '*';
            }
            if ($yb >= 0 && $yb < $height) {
                $grid[$yb][$x] = '*';
            }
        }

        return array_map(fn(array $row) => implode('', $row), $grid);
    }
}

$tui    = new Tui();
$circle = new CircleWidget();

// フォーカスをセットしてキー入力を受け付ける
$tui->setFocus($circle);
$tui->add($circle);
$tui->run();
