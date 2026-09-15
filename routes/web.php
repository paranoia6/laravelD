<?php

use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\SoftwareController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\TicketController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\WalletController;




/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/admin/login', [AuthController::class, 'showLogin'])
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->name('admin.login.submit');

Route::post('/admin/logout', [AuthController::class, 'logout'])
    ->name('admin.logout');

Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

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

    Route::get('/admin/supports/{support}', [SupportController::class, 'show'])
        ->name('admin.supports.show');

    Route::post('/admin/supports/{support}/links', [SupportController::class, 'store'])
        ->name('admin.supports.store');

    Route::delete('/admin/support-links/{supportLink}', [SupportController::class, 'destroy'])
        ->name('admin.support-links.destroy');

    Route::patch('/admin/support-links/{supportLink}/toggle', [SupportController::class, 'toggle'])
        ->name('admin.support-links.toggle');

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
    | Configs
    |--------------------------------------------------------------------------
    */

    Route::middleware(['auth', 'role:super_admin'])->group(function () {

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


    Route::get('/admin/software', [SoftwareController::class, 'index'])
        ->name('admin.software');

    Route::middleware('role:super_admin')->group(function () {
        Route::post('/admin/software', [SoftwareController::class, 'store'])
            ->name('admin.software.store');

        Route::patch('/admin/software/{software}/toggle', [SoftwareController::class, 'toggle'])
            ->name('admin.software.toggle');

        Route::delete('/admin/software/{software}', [SoftwareController::class, 'destroy'])
            ->name('admin.software.destroy');

        Route::middleware('role:super_admin')->group(function () {
            Route::get('/admin/admins', [AdminController::class, 'index'])
                ->name('admin.admins');

            Route::post('/admin/admins', [AdminController::class, 'store'])
                ->name('admin.admins.store');


            Route::post('/admin/admins/{user}/credit', [AdminController::class, 'credit'])
                ->name('admin.admins.credit');
        });
        Route::patch(
            '/admin/admins/{user}/credit',
            [AdminController::class, 'adjustCredit']
        )->name('admin.admins.credit.adjust');

    });



    Route::get('/admin/plans', [PlanController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('admin.plans');

    Route::patch('/admin/plans/{plan}', [PlanController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('admin.plans.update');


// Accounts
    Route::get('/admin/accounts', [
        AccountController::class,
        'index'
    ])->name('admin.accounts');

    Route::get('/admin/accounts/create', [
        AccountController::class,
        'create'
    ])->name('admin.accounts.create');

    Route::post('/admin/accounts', [
        AccountController::class,
        'store'
    ])->name('admin.accounts.store');

    Route::get('/admin/accounts/result', [
        AccountController::class,
        'result'
    ])->name('admin.accounts.result');

    Route::get('/admin/accounts/{account}', [
        AccountController::class,
        'show'
    ])->name('admin.accounts.show');

    Route::post('/admin/accounts/{account}/renew', [
        AccountController::class,
        'renew'
    ])->name('admin.accounts.renew');

    Route::patch('/admin/accounts/{account}/block', [
        AccountController::class,
        'block'
    ])->name('admin.accounts.block');



    Route::get('/admin/audit-logs', [
        AuditLogController::class,
        'index'
    ])
        ->middleware('role:super_admin')
        ->name('admin.audit-logs');

    Route::get('/admin/notifications', [
        NotificationController::class,
        'index'
    ])->name('admin.notifications');

    Route::patch('/admin/notifications/{id}/read', [
        NotificationController::class,
        'read'
    ])->name('admin.notifications.read');

    Route::patch(
        '/admin/admins/{user}/block',
        [AdminController::class, 'block']
    )->middleware('role:super_admin')
        ->name('admin.admins.block');

    Route::patch(
        '/admin/admins/{user}/unblock',
        [AdminController::class, 'unblock']
    )->middleware('role:super_admin')
        ->name('admin.admins.unblock');


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


    Route::get('/admin/reports', [ReportController::class, 'index'])
        ->name('admin.reports');

    Route::get('/admin/wallet', [WalletController::class, 'index'])
        ->name('admin.wallet');




});
