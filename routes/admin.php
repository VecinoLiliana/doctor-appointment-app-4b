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
Route::get('/doctors/{doctor}/schedule', [\App\Http\Controllers\Admin\DoctorController::class, 'schedule'])->name('doctors.schedule');
Route::post('/doctors/{doctor}/schedule/bulk', [\App\Http\Controllers\Admin\DoctorController::class, 'bulkUpdateSchedule'])->name('doctors.schedule.bulkUpdate');
Route::post('/doctors/{doctor}/schedule', [\App\Http\Controllers\Admin\DoctorController::class, 'storeSchedule'])->name('doctors.schedule.store');
Route::get('/doctors/schedule/{schedule}/edit', [\App\Http\Controllers\Admin\DoctorController::class, 'editSchedule'])->name('doctors.schedule.edit');
Route::put('/doctors/schedule/{schedule}', [\App\Http\Controllers\Admin\DoctorController::class, 'updateSchedule'])->name('doctors.schedule.update');
Route::delete('/doctors/schedule/{schedule}', [\App\Http\Controllers\Admin\DoctorController::class, 'deleteSchedule'])->name('doctors.schedule.delete');

//Gestión de citas
Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentController::class);

// Gestión de consultas
Route::get('/consultations/attend/{appointment}', [\App\Http\Controllers\Admin\ConsultationController::class, 'attend'])->name('consultations.attend');
Route::post('/consultations/store/{appointment}', [\App\Http\Controllers\Admin\ConsultationController::class, 'store'])->name('consultations.store');

// Búsqueda de disponibilidad
Route::post('/availability/search', \App\Http\Controllers\Admin\AvailabilityController::class . '@search')->name('admin.availability.search');
