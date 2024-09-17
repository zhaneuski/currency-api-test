<?php

use App\Http\Controllers\Currency\CalculateController;
use App\Http\Controllers\Currency\GetController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Currency\SyncController;

Route::get('/', function () {
    return view('welcome');
});

// todo: Для тестов
// Route::match(['get', 'post'], '/currency/sync', SyncController::class)->name('currencySync');

// Роут для конвертации валюты.
Route::post('/currency/calculate', CalculateController::class)->name('currencyCalculate');

// Роут для получения актуальных курсов.
Route::get('/currency/get', GetController::class)->name('currencyGet');


