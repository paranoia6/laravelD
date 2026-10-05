<?php

use App\Http\Controllers\AccountGeneratorController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\WalletController;
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

Route::get('/admin/plans', [PlanController::class, 'index'])
    ->name('admin.plans');

Route::patch('/admin/plans/{plan}', [PlanController::class, 'update'])
    ->name('admin.plans.update');


Route::get('/admin/tickets', [TicketController::class, 'index'])
    ->name('admin.tickets');

Route::get('/admin/tickets/create', [TicketController::class, 'create'])
    ->name('admin.tickets.create');

Route::post('/admin/tickets', [TicketController::class, 'store'])
    ->name('admin.tickets.store');

Route::get('/admin/tickets/{ticket}', [TicketController::class, 'show'])
    ->name('admin.tickets.show');

Route::post('/admin/tickets/{ticket}/reply', [TicketController::class, 'reply'])
    ->name('admin.tickets.reply');

Route::patch('/admin/tickets/{ticket}/status', [TicketController::class, 'status'])
    ->name('admin.tickets.status');


Route::middleware('auth')->group(function () {
    Route::get('/admin/wallet', [WalletController::class, 'index'])
        ->name('admin.wallet');
});
