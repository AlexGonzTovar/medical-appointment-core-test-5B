<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');
//Route::get('/', function () {
//  return view('welcome');
//});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas para la gestión de tickets de soporte
    Route::resource('tickets', App\Http\Controllers\TicketController::class)->only(['index', 'create', 'store']);
});
