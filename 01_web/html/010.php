<?php
?>
<canvas id="glCanvas" width="400" height="400"></canvas>
<script>
const canvas = document.getElementById("glCanvas");
const gl = canvas.getContext("webgl");

// 頂点シェーダー
const vsSource = `
  attribute vec4 aVertexPosition;
  void main() {
    gl_Position = aVertexPosition;
  }
`;

// フラグメントシェーダー（円を描画）
const fsSource = `
  precision mediump float;
  void main() {
    vec2 center = vec2(0.5, 0.5);
    vec2 pos = gl_FragCoord.xy / vec2(400.0, 400.0);
    float dist = distance(pos, center);
    if (dist < 0.35) {
      gl_FragColor = vec4(0.0, 0.0, 1.0, 1.0);
    } else {
      gl_FragColor = vec4(1.0, 1.0, 1.0, 1.0);
    }
  }
`;

// シェーダーをコンパイル
function loadShader(gl, type, source) {
  const shader = gl.createShader(type);
  gl.shaderSource(shader, source);
  gl.compileShader(shader);
  return shader;
}

const vertexShader = loadShader(gl, gl.VERTEX_SHADER, vsSource);
const fragmentShader = loadShader(gl, gl.FRAGMENT_SHADER, fsSource);

// プログラムを作成
const shaderProgram = gl.createProgram();
gl.attachShader(shaderProgram, vertexShader);
gl.attachShader(shaderProgram, fragmentShader);
gl.linkProgram(shaderProgram);

// 四角形の頂点データ
const positions = new Float32Array([
  -1.0,  1.0,
   1.0,  1.0,
  -1.0, -1.0,
   1.0, -1.0,
]);

const positionBuffer = gl.createBuffer();
gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
gl.bufferData(gl.ARRAY_BUFFER, positions, gl.STATIC_DRAW);

// 描画
gl.clearColor(1.0, 1.0, 1.0, 1.0);
gl.clear(gl.COLOR_BUFFER_BIT);

gl.useProgram(shaderProgram);

const vertexPosition = gl.getAttribLocation(shaderProgram, "aVertexPosition");
gl.bindBuffer(gl.ARRAY_BUFFER, positionBuffer);
gl.vertexAttribPointer(vertexPosition, 2, gl.FLOAT, false, 0, 0);
gl.enableVertexAttribArray(vertexPosition);

gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
</script>
<?php

echo '<hr>';
highlight_file(__FILE__);
