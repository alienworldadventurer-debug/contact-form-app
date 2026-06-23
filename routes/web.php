<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController; // 追加

Route::get('/', [ContactController::class, 'index']);
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
