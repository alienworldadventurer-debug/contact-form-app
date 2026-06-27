<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// お問い合わせ入力画面（トップページ）の表示
Route::get('/', [ContactController::class, 'index']);

// お問い合わせ確認画面の表示
Route::post('/contacts/confirm', [ContactController::class, 'confirm']);

// お問い合わせデータの保存処理
Route::post('/contacts', [ContactController::class, 'store']);

// サンクスページの表示
Route::get('/thanks', [ContactController::class, 'thanks']);

Route::middleware('auth')->group(function () {
    // admin画面(管理画面一覧)の表示
    Route::get('/admin', [AdminController::class, 'index']);

    // お問い合わせ詳細表示
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show'])->name('admin.show');

    // お問い合わせ削除
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy'])->name('admin.destroy');

    // タグ管理
    Route::post('/admin/tags', [TagController::class, 'store'])->name('admin.tags.store');
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit'])->name('admin.tags.edit');
    Route::put('/admin/tags/{tag}', [TagController::class, 'update'])->name('admin.tags.update');
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy'])->name('admin.tags.destroy');

    // CSV出力
    Route::get('/contacts/export', [ContactController::class, 'export']);
});
