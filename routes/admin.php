<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
}) -> name('dashboard');

//Gestión de roles
Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);

// Gestión de Usuarios
Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
