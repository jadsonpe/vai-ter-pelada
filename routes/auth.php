<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\GoogleAuthController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::redirect('register', 'cadastro');
    Route::redirect('login', 'entrar');
    Route::redirect('forgot-password', 'esqueci-senha');

    Route::get('reset-password/{token}', fn (string $token) => redirect()->route('password.reset', [
        'token' => $token,
        'email' => request('email'),
    ]));

    Route::get('cadastro', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('cadastro', [RegisteredUserController::class, 'store']);

    Route::get('entrar', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('entrar', [AuthenticatedSessionController::class, 'store']);

    Route::get('entrar/google/redirecionar', [GoogleAuthController::class, 'redirect'])
        ->name('auth.google.redirect');

    Route::get('entrar/google/retorno', [GoogleAuthController::class, 'callback'])
        ->name('auth.google.callback');

    Route::get('esqueci-senha', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('esqueci-senha', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('redefinir-senha/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('redefinir-senha', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::redirect('verify-email', 'verificar-email');
    Route::redirect('confirm-password', 'confirmar-senha');

    Route::get('verificar-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verificar-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/notificacao-verificacao', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirmar-senha', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirmar-senha', [ConfirmablePasswordController::class, 'store']);

    Route::put('senha', [PasswordController::class, 'update'])->name('password.update');

    Route::post('sair', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
