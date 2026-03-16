<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>PHP Circle — Bouncing</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #1a1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            overflow: hidden;
        }
        canvas { display: block; }
    </style>
</head>
<body>
    <canvas id="canvas" width="500" height="500"></canvas>
    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');

        const W = canvas.width;
        const H = canvas.height;
        const R = 60;
        const GRAVITY = 0.5;
        const MIN_VY = 12; // 跳ね返り最低速度（減衰しても維持）

        const x = W / 2;  // x は固定
        let y = H / 2;
        let vy = 0;

        // スクイッシュ（着地時の変形）
        let squishX = 1;
        let squishY = 1;
        const SQUISH_RECOVER = 0.15;

        function draw() {
            ctx.clearRect(0, 0, W, H);

            // スクイッシュ復元
            squishX += (1 - squishX) * SQUISH_RECOVER;
            squishY += (1 - squishY) * SQUISH_RECOVER;

            // 物理演算 (y のみ)
            vy += GRAVITY;
            y += vy;

            // 床バウンス
            if (y + R >= H) {
                y = H - R;
                vy = -Math.max(Math.abs(vy), MIN_VY); // 最低速度を保証
                // 着地でポヨン
                squishX = 1 + Math.abs(vy) * 0.02;
                squishY = 1 - Math.abs(vy) * 0.02;
            }

            // 描画: スクイッシュを scale で表現
            ctx.save();
            ctx.translate(x, y);
            ctx.scale(squishX, squishY);

            ctx.beginPath();
            ctx.arc(0, 0, R, 0, Math.PI * 2);
            ctx.fillStyle = 'rgba(79, 195, 247, 0.25)';
            ctx.fill();
            ctx.strokeStyle = '#4fc3f7';
            ctx.lineWidth = 4;
            ctx.stroke();

            ctx.restore();

            requestAnimationFrame(draw);
        }

        draw();
    </script>
</body>
</html>
