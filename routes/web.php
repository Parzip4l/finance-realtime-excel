<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceController;

Route::get('/', [FinanceController::class, 'uploadPage']);
Route::get('/upload', [FinanceController::class, 'uploadPage'])->name('upload.page');
Route::post('/upload', [FinanceController::class, 'upload'])->name('upload.excel');

Route::get('/finance/list', [FinanceController::class, 'list'])->name('finance.list');