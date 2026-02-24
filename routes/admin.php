<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard');
}) -> name('dashboard');

//Gestión de roles
Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);

// Gestión de Usuarios
Route::resource('users', \App\Http\Controllers\Admin\UserController::class);

//Gestión de pacientes
Route::resource('patients', \App\Http\Controllers\Admin\PatientController::class);

//Gestión de doctores
Route::resource('doctors', \App\Http\Controllers\Admin\DoctorController::class);
