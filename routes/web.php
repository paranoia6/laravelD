<?php

use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SoftwareController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WalletController;
use App\Http\Controllers\HappClientController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/happ', [HappClientController::class, 'checkRequest']);


Route::get('/admin/login', [AuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

Route::get('/', function () {
    return redirect()->route('admin.login');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/admin', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Users
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])
            ->name('admin.users');

        Route::get('/admin/users/{user}', [UserController::class, 'show'])
            ->name('admin.users.show');

        Route::patch('/admin/users/{user}/status', [UserController::class, 'toggleStatus'])
            ->name('admin.users.toggle-status');
    });

    /*
    |--------------------------------------------------------------------------
    | Supports
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/supports', [SupportController::class, 'index'])
        ->name('admin.supports');

    Route::post('/admin/supports', [SupportController::class, 'store'])
        ->name('admin.supports.store');

    Route::get('/admin/supports/{support}', [SupportController::class, 'show'])
        ->name('admin.supports.show');

    Route::patch('/admin/supports/{support}', [SupportController::class, 'update'])
        ->name('admin.supports.update');

    Route::patch('/admin/supports/{support}/toggle', [SupportController::class, 'toggle'])
        ->name('admin.supports.toggle');

    Route::delete('/admin/supports/{support}', [SupportController::class, 'destroy'])
        ->name('admin.supports.destroy');

    /*
    |--------------------------------------------------------------------------
    | Alerts
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/alerts', [AlertController::class, 'index'])
        ->name('admin.alerts');

    Route::get('/admin/alerts/{alert}', [AlertController::class, 'show'])
        ->name('admin.alerts.show');

    /*
    |--------------------------------------------------------------------------
    | Configs - Super Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:super_admin')->group(function () {
        Route::get('/admin/configs', [ConfigController::class, 'index'])
            ->name('admin.configs');

        Route::get('/admin/configs/{config}', [ConfigController::class, 'show'])
            ->name('admin.configs.show');

        Route::post('/admin/configs', [ConfigController::class, 'store'])
            ->name('admin.configs.store');

        Route::patch('/admin/configs/{config}', [ConfigController::class, 'update'])
            ->name('admin.configs.update');

        Route::delete('/admin/configs/{config}', [ConfigController::class, 'destroy'])
            ->name('admin.configs.destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Software
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/software', [SoftwareController::class, 'index'])
        ->name('admin.software');

    Route::middleware('role:super_admin')->group(function () {
        Route::post('/admin/software', [SoftwareController::class, 'store'])
            ->name('admin.software.store');

        Route::patch('/admin/software/{software}/toggle', [SoftwareController::class, 'toggle'])
            ->name('admin.software.toggle');

        Route::delete('/admin/software/{software}', [SoftwareController::class, 'destroy'])
            ->name('admin.software.destroy');

        /*
        |--------------------------------------------------------------------------
        | Admin Management
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/admins', [AdminController::class, 'index'])
            ->name('admin.admins');

        Route::post('/admin/admins', [AdminController::class, 'store'])
            ->name('admin.admins.store');

        Route::post('/admin/admins/{user}/credit', [AdminController::class, 'credit'])
            ->name('admin.admins.credit');

        Route::patch('/admin/admins/{user}/credit', [AdminController::class, 'adjustCredit'])
            ->name('admin.admins.credit.adjust');

        Route::patch('/admin/admins/{user}/block', [AdminController::class, 'block'])
            ->name('admin.admins.block');

        Route::patch('/admin/admins/{user}/unblock', [AdminController::class, 'unblock'])
            ->name('admin.admins.unblock');

        /*
        |--------------------------------------------------------------------------
        | Audit Logs
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/audit-logs', [AuditLogController::class, 'index'])
            ->name('admin.audit-logs');

        /*
        |--------------------------------------------------------------------------
        | Plans
        |--------------------------------------------------------------------------
        */

        Route::get('/admin/plans', [PlanController::class, 'index'])
            ->name('admin.plans');

        Route::post('/admin/plans', [PlanController::class, 'store'])
            ->name('admin.plans.store');

        Route::patch('/admin/plans/{plan}', [PlanController::class, 'update'])
            ->name('admin.plans.update');
    });

    /*
    |--------------------------------------------------------------------------
    | Accounts
    |--------------------------------------------------------------------------
    | Keep the result route before the dynamic account route.
    */

    Route::get('/admin/accounts', [AccountController::class, 'index'])
        ->name('admin.accounts');

    Route::get('/admin/accounts/create', [AccountController::class, 'create'])
        ->name('admin.accounts.create');

    Route::post('/admin/accounts', [AccountController::class, 'store'])
        ->name('admin.accounts.store');

    Route::get('/admin/accounts/result', [AccountController::class, 'result'])
        ->name('admin.accounts.result');

    Route::get('/admin/accounts/{account}', [AccountController::class, 'show'])
        ->name('admin.accounts.show');

    Route::post('/admin/accounts/{account}/renew', [AccountController::class, 'renew'])
        ->name('admin.accounts.renew');

    Route::patch('/admin/accounts/{account}/block', [AccountController::class, 'block'])
        ->name('admin.accounts.block');

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/notifications', [NotificationController::class, 'index'])
        ->name('admin.notifications');

    Route::patch('/admin/notifications/{id}/read', [NotificationController::class, 'read'])
        ->name('admin.notifications.read');

    /*
    |--------------------------------------------------------------------------
    | Tickets
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Reports and Wallet
    |--------------------------------------------------------------------------
    */

    Route::get('/admin/reports', [ReportController::class, 'index'])
        ->name('admin.reports');

    Route::get('/admin/wallet', [WalletController::class, 'index'])
        ->name('admin.wallet');
});
