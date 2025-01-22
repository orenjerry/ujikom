<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AlbumController;
use App\Http\Controllers\FotoController;
use App\Http\Middleware\CheckLoginStatus;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/auth/login');

Route::prefix('/auth')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('auth.login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('auth.register.show');
    Route::post('/register', [AuthController::class, 'register'])->name('auth.register');
});

Route::get('/auth/logout', [AuthController::class, 'doLogout'])->name('auth.logout');
Route::post('/auth/logout', [AuthController::class, 'doLogout'])->name('auth.logout');

Route::middleware([CheckLoginStatus::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');

    Route::prefix('/album')->group(function () {
        Route::get('/', [AlbumController::class, 'showAlbum'])->name('album');
        Route::get('/create', [AlbumController::class, 'showCreateAlbum'])->name('showCreateAlbum');
        Route::post('/create', [AlbumController::class, 'createAlbum'])->name('createAlbum');
        Route::get('/{id}', [AlbumController::class, 'showDetailAlbum'])->name('showDetailAlbum');
    });


    Route::prefix('/foto')->group(function () {
        Route::get('/add', [FotoController::class, 'showAddFoto']);
        Route::post('/add', [FotoController::class, 'addFoto'])->name('addFoto');
        Route::get('/{id}', [FotoController::class, 'showDetailFoto']);
        Route::post('/{id}/like', [FotoController::class, 'toggleLike'])->name('toggleLike');
        Route::post('/{id}/comment', [FotoController::class, 'addComment'])->name('addComment');
        Route::delete('/{id}/delete', [FotoController::class, 'deleteFoto'])->name('deleteFoto');
        Route::put('/{id}/edit', [FotoController::class, 'editFoto'])->name('editFoto');
    });

    Route::get('/admin/dashboard', function() {
        return view('welcome');
    })->name('admin.dashboard');
});
