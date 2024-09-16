<?php

use App\Http\Controllers\Currency\CalculateController;
use App\Http\Controllers\Currency\GetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Currency\SyncController;

Route::get('/', function () {
    return view('welcome');
});

Route::match(['get', 'post'], '/currency/sync', SyncController::class)->name('currencySync');
Route::match(['get', 'post'], '/currency/calculate', CalculateController::class)->name('currencyCalculate');

Route::get('/currency/get', GetController::class)->name('admin.dashboard');


