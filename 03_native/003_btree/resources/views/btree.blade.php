<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>B-Tree Visualization — NativePHP</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #1a1a2e; overflow: hidden; }
        canvas { display: block; }
    </style>
</head>
<body>
<canvas id="canvas"></canvas>
<script>
const canvas = document.getElementById('canvas');
const ctx    = canvas.getContext('2d');
canvas.width  = window.innerWidth;
canvas.height = window.innerHeight;
const W = canvas.width, H = canvas.height;

// ── B-Tree params ───────────────────────────────────────────────────
const ORDER    = 3;          // 最大子ノード数
const MAX_KEYS = ORDER - 1;  // 1ノードの最大キー数 = 2

// ── Layout ─────────────────────────────────────────────────────────
const KEY_W   = 50;   // キーセル幅
const NODE_H  = 44;   // ノード高さ
const H_PAD   = 12;   // ノード内の左右パディング
const LEVEL_H = 120;  // レベル間の縦間隔
const TOP_Y   = 100;  // ルートのY座標
const SIDE_M  = 50;   // 左右マージン

// ── Animation ──────────────────────────────────────────────────────
const LERP       = 0.07;  // 位置補間係数
const INS_FRAMES = 150;   // 挿入間隔(フレーム数) ≈ 2.5秒

// ── Colors ─────────────────────────────────────────────────────────
const NODE_BG = '#0f3460';
const BORDER  = '#4fc3f7';
const TXT     = '#e0f7fa';
const NEW_CLR = '#ffd54f';  // 新規挿入キーの色
const SPLIT_C = '#ff7043';  // 分裂メッセージの色
const LINE_C  = 'rgba(79,195,247,0.4)';
const DIM_C   = 'rgba(255,255,255,0.3)';

// ── 挿入シーケンス ─────────────────────────────────────────────────
const VALUES = [1, 3, 5, 7, 9, 2, 4, 6, 8, 10, 11, 12, 13, 14, 15];

// ── B-Tree ─────────────────────────────────────────────────────────
class BNode {
    constructor(leaf = true) {
        this.keys     = [];
        this.children = [];
        this.leaf     = leaf;
        this.x  = W / 2;  this.tx = W / 2;
        this.y  = TOP_Y;  this.ty = TOP_Y;
        this.newIdx = -1;  // 新規挿入キーのインデックス（ハイライト用）
    }
    get w() { return this.keys.length * KEY_W + H_PAD * 2; }
}

let root = new BNode(true);
let qIdx = 0, frame = 0, nextIns = 60;
let statusTxt = 'B-Tree 次数3 — 自動挿入中';
let splitMsg = '', splitTimer = 0;

// ── 挿入 ───────────────────────────────────────────────────────────
function insert(key) {
    const path = [];
    let n = root;

    // リーフまで降りる
    while (!n.leaf) {
        let i = n.keys.findIndex(k => key < k);
        if (i === -1) i = n.keys.length;
        path.push({ n, i });
        n = n.children[i];
    }

    // リーフにキーを挿入
    let i = n.keys.findIndex(k => key < k);
    if (i === -1) i = n.keys.length;
    n.keys.splice(i, 0, key);
    n.newIdx = i;

    fixOverflow(n, path);
    layout();
}

// ── オーバーフロー修正（ボトムアップ分裂） ─────────────────────────
function fixOverflow(n, path) {
    if (n.keys.length <= MAX_KEYS) return;

    const mid    = Math.floor(n.keys.length / 2);
    const median = n.keys[mid];

    // 左ノード
    const L = new BNode(n.leaf);
    L.keys     = n.keys.slice(0, mid);
    L.children = n.leaf ? [] : n.children.slice(0, mid + 1);
    L.x = n.x; L.y = n.y; L.tx = n.x; L.ty = n.y;
    if (n.newIdx !== -1 && n.newIdx < mid) L.newIdx = n.newIdx;

    // 右ノード
    const R = new BNode(n.leaf);
    R.keys     = n.keys.slice(mid + 1);
    R.children = n.leaf ? [] : n.children.slice(mid + 1);
    R.x = n.x; R.y = n.y; R.tx = n.x; R.ty = n.y;
    if (n.newIdx !== -1 && n.newIdx > mid) R.newIdx = n.newIdx - mid - 1;

    splitMsg   = `Split!  中央値 [${median}] を親へ昇格`;
    splitTimer = 140;

    if (path.length === 0) {
        // ルート分裂 → 新しいルートを作成
        const nr = new BNode(false);
        nr.keys     = [median];
        nr.children = [L, R];
        nr.x = n.x; nr.y = n.y; nr.tx = n.tx; nr.ty = n.ty;
        root = nr;
    } else {
        // 中央値を親に押し上げ
        const { n: p, i } = path.pop();
        p.keys.splice(i, 0, median);
        p.children.splice(i, 1, L, R);
        fixOverflow(p, path);
    }
}

// ── レイアウト計算 ─────────────────────────────────────────────────
function leafCount(n) {
    if (n.leaf) return 1;
    return n.children.reduce((s, c) => s + leafCount(c), 0);
}

function setTargets(n, x1, x2, y) {
    n.tx = (x1 + x2) / 2;
    n.ty = y;
    if (!n.leaf) {
        const tot = leafCount(n);
        let cx = x1;
        for (const c of n.children) {
            const lc = leafCount(c);
            const cw = (x2 - x1) * lc / tot;
            setTargets(c, cx, cx + cw, y + LEVEL_H);
            cx += cw;
        }
    }
}

function layout() { setTargets(root, SIDE_M, W - SIDE_M, TOP_Y); }

// ── ユーティリティ ─────────────────────────────────────────────────
function clearHL(n) { n.newIdx = -1; n.children.forEach(clearHL); }

function settled(n, eps = 2) {
    if (Math.abs(n.x - n.tx) > eps || Math.abs(n.y - n.ty) > eps) return false;
    return n.children.every(c => settled(c, eps));
}

function lerpAll(n) {
    n.x += (n.tx - n.x) * LERP;
    n.y += (n.ty - n.y) * LERP;
    n.children.forEach(lerpAll);
}

// ── 描画：エッジ ───────────────────────────────────────────────────
function drawLines(n) {
    if (n.leaf) return;
    const cnt  = n.children.length;
    const segW = n.w / cnt;

    for (let i = 0; i < cnt; i++) {
        const c  = n.children[i];
        const fx = n.x - n.w / 2 + segW * i + segW / 2;
        const fy = n.y + NODE_H;

        ctx.beginPath();
        ctx.moveTo(fx, fy);
        ctx.bezierCurveTo(fx, fy + 35, c.x, c.y - 35, c.x, c.y);
        ctx.strokeStyle = LINE_C;
        ctx.lineWidth   = 1.5;
        ctx.stroke();
        drawLines(c);
    }
}

// ── 描画：ノード ───────────────────────────────────────────────────
function drawNode(n) {
    const x = n.x - n.w / 2, y = n.y;

    // グロー
    ctx.shadowColor = 'rgba(79,195,247,0.3)';
    ctx.shadowBlur  = 10;

    // 背景
    ctx.beginPath();
    ctx.roundRect(x, y, n.w, NODE_H, 8);
    ctx.fillStyle   = NODE_BG;
    ctx.fill();
    ctx.strokeStyle = BORDER;
    ctx.lineWidth   = 2;
    ctx.stroke();
    ctx.shadowBlur  = 0;

    for (let i = 0; i < n.keys.length; i++) {
        const kx = x + H_PAD + i * KEY_W;

        // キー区切り線
        if (i > 0) {
            ctx.beginPath();
            ctx.moveTo(kx, y + 8);
            ctx.lineTo(kx, y + NODE_H - 8);
            ctx.strokeStyle = 'rgba(79,195,247,0.35)';
            ctx.lineWidth   = 1;
            ctx.stroke();
        }

        // 新規キーのハイライト背景
        if (i === n.newIdx) {
            ctx.fillStyle   = NEW_CLR;
            ctx.globalAlpha = 0.2;
            ctx.fillRect(kx + 2, y + 5, KEY_W - 4, NODE_H - 10);
            ctx.globalAlpha = 1;
        }

        // キーのテキスト
        ctx.fillStyle    = i === n.newIdx ? NEW_CLR : TXT;
        ctx.font         = 'bold 18px "Courier New"';
        ctx.textAlign    = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(n.keys[i], kx + KEY_W / 2, y + NODE_H / 2);
    }

    n.children.forEach(drawNode);
}

// ── 描画：HUD ──────────────────────────────────────────────────────
function drawHUD() {
    ctx.textAlign = 'left'; ctx.textBaseline = 'top';

    // 挿入待ちキュー
    const rem = VALUES.slice(qIdx);
    ctx.fillStyle = DIM_C;
    ctx.font      = '13px "Courier New"';
    ctx.fillText('挿入待ち: ' + (rem.length ? '[' + rem.join('  ') + ']' : '完了'), 20, 14);

    // 現在の操作
    ctx.fillStyle = TXT;
    ctx.font      = 'bold 14px "Courier New"';
    ctx.fillText(statusTxt, 20, 36);

    // 分裂フラッシュメッセージ
    if (splitTimer > 0) {
        ctx.globalAlpha = Math.min(1, splitTimer / 40);
        ctx.fillStyle   = SPLIT_C;
        ctx.font        = 'bold 14px "Courier New"';
        ctx.textAlign   = 'right';
        ctx.fillText(splitMsg, W - 20, 14);
        ctx.globalAlpha = 1;
        splitTimer--;
    }

    // フッター説明
    ctx.fillStyle    = DIM_C;
    ctx.font         = '12px "Courier New"';
    ctx.textAlign    = 'right';
    ctx.textBaseline = 'bottom';
    ctx.fillText('次数3 ─ 1ノード最大2キー / 最大3子  |  クリックでリプレイ', W - 20, H - 14);
    ctx.textAlign = 'left';
}

// ── メインループ ───────────────────────────────────────────────────
function loop() {
    frame++;

    // 自動挿入
    if (qIdx < VALUES.length && frame >= nextIns && settled(root)) {
        clearHL(root);
        const v = VALUES[qIdx++];
        statusTxt = `挿入: ${v}`;
        insert(v);
        nextIns = frame + INS_FRAMES;
        if (qIdx >= VALUES.length) {
            setTimeout(() => { statusTxt = '完了！  クリックでリプレイ'; }, 1000);
        }
    }

    lerpAll(root);

    ctx.clearRect(0, 0, W, H);
    drawLines(root);
    drawNode(root);
    drawHUD();

    requestAnimationFrame(loop);
}

// リプレイ
canvas.addEventListener('click', () => {
    if (qIdx < VALUES.length) return;
    root = new BNode(true);
    qIdx = 0; frame = 0; nextIns = 60;
    statusTxt = 'B-Tree 次数3 — 自動挿入中';
    splitMsg = ''; splitTimer = 0;
    layout();
});

layout();
loop();
</script>
</body>
</html>
