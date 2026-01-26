<?php

use Illuminate\Support\Facades\Route;

// rota padrao (página inicial)
Route::get('/', function () {
    return view('welcome');
});
