<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('web/home/page');
});
