<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Currency\SyncController;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/currency/sync', SyncController::class);

