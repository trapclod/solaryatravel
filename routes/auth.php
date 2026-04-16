<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('registrati', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('registrati', [RegisteredUserController::class, 'store']);

    Route::get('accedi', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('accedi', [AuthenticatedSessionController::class, 'store']);

    Route::get('password-dimenticata', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('password-dimenticata', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verifica-email', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('verifica-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
