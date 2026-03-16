<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>PHP Circle — NativePHP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            background: #1a1a2e;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        canvas { display: block; }
    </style>
</head>
<body>
    <canvas id="canvas" width="500" height="500"></canvas>
    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');

        // 円を描く
        ctx.beginPath();
        ctx.arc(250, 250, 150, 0, Math.PI * 2);
        ctx.strokeStyle = '#4fc3f7';
        ctx.lineWidth = 4;
        ctx.stroke();
    </script>
</body>
</html>
