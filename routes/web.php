<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\excelController;

Route::get('/', function() {
    return view('home');
});

Route::post('/readFile', [excelController::class, 'store']);
Route::post('/saveFile', [excelController::class, 'save']);

Route::post('/import',[UserController::class, 'import'])->name('import-users');
Route::get('/export-users',[UserController::class, 'exportUsers'])->name('export-users');