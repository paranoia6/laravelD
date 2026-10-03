<?php

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
