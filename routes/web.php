<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;

Route::get('/', [ContactController::class, 'index']);
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
// お問い合わせデータの保存処理
Route::post('/contacts', [ContactController::class, 'store']);
// サンクスページの表示
Route::get('/thanks', [ContactController::class, 'thanks']);
