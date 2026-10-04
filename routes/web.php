<?php

use App\Http\Controllers\Admin\SupportController;
use Illuminate\Support\Facades\Route;

Route::get('/admin/login', function () {
    return 'Admin Login';
})->name('admin.login');

Route::get('/', function () {
    return view('welcome');
});
Route::redirect('/demo', '/demo/dashboard');

Route::view('/demo/dashboard', 'dashboard.index')->name('demo.dashboard');
Route::view('/demo/users', 'users.index')->name('demo.users');

Route::get('/admin/supports', [SupportController::class, 'index'])->name('admin.supports');
Route::post('/admin/supports', [SupportController::class, 'store'])->name('admin.supports.store');
Route::get('/admin/supports/{support}', [SupportController::class, 'show'])->name('admin.supports.show');
Route::patch('/admin/supports/{support}', [SupportController::class, 'update'])->name('admin.supports.update');
Route::patch('/admin/supports/{support}/toggle', [SupportController::class, 'toggle'])->name('admin.supports.toggle');
Route::delete('/admin/supports/{support}', [SupportController::class, 'destroy'])->name('admin.supports.destroy');
