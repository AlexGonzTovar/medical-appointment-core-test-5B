<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PatientController;
use App\Http\Controllers\Admin\DoctorController;

Route::get('/', function(){
    return view ('admin.dashboard');
})->name('dashboard');

//Gestión de Roles
Route::resource('roles', RoleController::class);

//Gestión de Usuarios
Route::resource('users', UserController::class);

//Gestión de pacientes
Route::resource('patients', PatientController::class);

//Gestión de doctores
Route::resource('doctors', DoctorController::class);
Route::get('doctors/{doctor}/schedule', [\App\Http\Controllers\Admin\DoctorScheduleController::class, 'edit'])->name('doctors.schedule');
Route::post('doctors/{doctor}/schedule', [\App\Http\Controllers\Admin\DoctorScheduleController::class, 'store'])->name('doctors.schedule.store');

//Gestión de Citas
Route::resource('appointments', \App\Http\Controllers\Admin\AppointmentController::class);
Route::get('appointments/{appointment}/consultation', [\App\Http\Controllers\Admin\AppointmentController::class, 'consultation'])->name('appointments.consultation');