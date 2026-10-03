<?php

use App\Http\Controllers\AccountGeneratorController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', function () {
    return 'Admin Login';
})->name('admin.login');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/accounts/generator', [AccountGeneratorController::class, 'create'])
    ->name('accounts.generator');

Route::post('/accounts/generator', [AccountGeneratorController::class, 'store'])
    ->name('accounts.generator.store');
