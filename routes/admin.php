<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
}) -> name('dashboard');

//Gestión de roles
Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
