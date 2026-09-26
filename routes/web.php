<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\AuthController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

// مسارات تسجيل الدخول والخروج
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



// ==========================================
// المسارات المحمية (لا يمكن دخولها بدون تسجيل دخول)
// ==========================================
Route::middleware('auth')->group(function () {
    
    Route::get('/', function () { return redirect()->route('dashboard'); });
    
    Route::get('/dashboard', [FinancialController::class, 'dashboard'])->name('dashboard');
    Route::post('/update-balance', [FinancialController::class, 'updateBalance'])->name('update-balance');
    
    Route::get('/daily-entry', [FinancialController::class, 'create'])->name('daily-entry.create');
    Route::post('/daily-entry', [FinancialController::class, 'store'])->name('daily-entry.store');
    Route::get('/daily-entry/{id}/edit', [FinancialController::class, 'edit'])->name('daily-entry.edit');
    Route::put('/daily-entry/{id}', [FinancialController::class, 'update'])->name('daily-entry.update');
    Route::delete('/daily-entry/{id}', [FinancialController::class, 'destroy'])->name('daily-entry.destroy');
    
    Route::get('/report', [FinancialController::class, 'report'])->name('report');

    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/store-name', [\App\Http\Controllers\SettingsController::class, 'updateStoreName'])->name('settings.store-name');
    Route::post('/settings/password', [\App\Http\Controllers\SettingsController::class, 'updatePassword'])->name('settings.password');


    });
