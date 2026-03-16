<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn() => view('circle'))->name('circle');
