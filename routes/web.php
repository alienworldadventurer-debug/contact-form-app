<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;

// お問い合わせ入力画面（トップページ）の表示
Route::get('/', [ContactController::class, 'index']);
// お問い合わせ確認画面の表示
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);
// お問い合わせデータの保存処理
Route::post('/contacts', [ContactController::class, 'store']);
// サンクスページの表示
Route::get('/thanks', [ContactController::class, 'thanks']);
// admin画面(管理画面一覧)の表示
Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});
