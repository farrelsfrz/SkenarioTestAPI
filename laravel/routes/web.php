<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\SkenarioController;

Route::view('/', 'welcome');

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Rute untuk registrasi
Route::get('/registrasi', [AuthController::class, 'showRegistrationForm'])->name('registrasi');
Route::post('/users', [AuthController::class, 'register'])->name('register.user');

Route::middleware(['cors'])->group(function () {
    Route::get('/test_cases', [SkenarioController::class, 'index']);
    Route::get('/test_cases/{id}', [SkenarioController::class, 'show']);
    Route::post('/test_cases', [SkenarioController::class, 'store']);
    Route::put('/test_cases/{id}', [SkenarioController::class, 'update']);
    Route::delete('/test_cases/{id}', [SkenarioController::class, 'destroy']);
});

Route::get('/test_steps', [TestingController::class, 'index']);
Route::get('/test_steps/{id}', [TestingController::class, 'show']);
Route::post('/test_steps', [TestingController::class, 'store']);
Route::put('/test_steps/{id}', [TestingController::class, 'update']);
Route::delete('/test_steps/{id}', [TestingController::class, 'destroy']);

Route::middleware(['auth'])->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');
    Route::view('/skenario', 'skenario')->name('skenario');
    Route::view('/testing', 'testing')->name('testing');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});