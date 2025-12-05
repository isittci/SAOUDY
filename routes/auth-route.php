<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->prefix("auth")->name('auth.')->group(function () {
    Route::get('/', [AuthController::class,'index'])->name('index');
    Route::post('/', [AuthController::class,'login'])->name('login');


    // Vérification du code
    Route::get('/verify/{token}', [AuthController::class, 'showVerifyForm'])->name('verify.show');
    Route::post('/verify/{token}', [AuthController::class, 'verifyCode'])->name('verify.post');
    Route::post('/verify/{token}/resend', [AuthController::class, 'resendCode'])->name('verify.resend');

    // Réinitialisation du mot de passe
    Route::get('/password/forgot', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset/{token}', [AuthController::class, 'resetPassword'])->name('password.update');
});



