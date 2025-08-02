<?php

use Illuminate\Support\Facades\Route;
use Filament\Http\Controllers\Auth\LoginController;

// Route::get('login', [LoginController::class, 'show'])->name('filament.admin.auth.login');
Route::post('login', [LoginController::class, 'store']);
Route::post('logout', [LoginController::class, 'destroy'])->name('filament.admin.auth.logout');
