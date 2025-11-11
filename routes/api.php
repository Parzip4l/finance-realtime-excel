<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinanceController;

Route::get('/finance/{type}/data', [FinanceController::class, 'getData'])->name('finance.data');
Route::get('/finance/{type}/total', [FinanceController::class, 'getTotalOnly'])->name('finance.total');
