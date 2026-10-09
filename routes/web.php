<?php

use App\Http\Controllers\AccountGeneratorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', [AuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->name('admin.logout');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', [DashboardController::class, 'index'])
    ->name('admin.dashboard');

Route::get('/accounts/generator', [AccountGeneratorController::class, 'create'])
    ->name('accounts.generator');

Route::post('/accounts/generator', [AccountGeneratorController::class, 'store'])
    ->name('accounts.generator.store');

Route::get('/admin/plans', [PlanController::class, 'index'])
    ->name('admin.plans');

Route::patch('/admin/plans/{plan}', [PlanController::class, 'update'])
    ->name('admin.plans.update');

Route::get('/admin/supports', [SupportController::class, 'index'])->name('admin.supports');
Route::post('/admin/supports', [SupportController::class, 'store'])->name('admin.supports.store');
Route::get('/admin/supports/{support}', [SupportController::class, 'show'])->name('admin.supports.show');
Route::patch('/admin/supports/{support}', [SupportController::class, 'update'])->name('admin.supports.update');
Route::patch('/admin/supports/{support}/toggle', [SupportController::class, 'toggle'])->name('admin.supports.toggle');
Route::delete('/admin/supports/{support}', [SupportController::class, 'destroy'])->name('admin.supports.destroy');
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
