<?php

echo '<canvas id="myCanvas" width="400" height="400"></canvas>';
echo '<script>';
echo 'const canvas = document.getElementById("myCanvas");';
echo 'const ctx = canvas.getContext("2d");';
echo 'ctx.beginPath();';
echo 'ctx.arc(200, 200, 150, 0, 2 * Math.PI);';
echo 'ctx.stroke();';
echo '</script>';

echo '<hr>';
highlight_file(__FILE__);
