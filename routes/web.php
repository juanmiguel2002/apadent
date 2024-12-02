<?php

use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\ClinicaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ImagenesController;
use App\Http\Controllers\PacienteHistorial;
use App\Http\Controllers\Pacientes;
use App\Http\Controllers\PacienteShowController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\UsersClinica;
use App\Livewire\ClinicaShow;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    Route::get('/paciente/{paciente}/etapa/{etapa}/imagenes', [ImagenesController::class, 'verImagenes'])
    ->middleware('check.role:doctor_admin, doctor, clinica_user')->name('imagenes.ver');

    Route::get('/imagenes/{filePath}', [ImagenesController::class, 'mostrarImagen'])
    ->name('imagenes.protegidas')->where('filePath', '.*');

    Route::get('/etapa/descargar/{filePath}', [ArchivoController::class, 'archivo'])
    ->name('archivo.descargar')->where('filePath', '.*');

    Route::get('/dashboard',[DashboardController::class, 'show'])->name('dashboard');

    // Rutas del administrador
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/clinicas', [DashboardController::class, 'show'])->name('admin.clinica');
        Route::get('/admin/clinica/{id}', [ClinicaController::class, 'index'])->name('admin.clinica.view');
    });

    // Rutas del doctor Administrador
    Route::middleware(['role:doctor_admin|admin'])->group(function(){
        Route::get('/users', [UsersClinica::class, 'index'])->name('users');
    });

    Route::middleware(['role:doctor_admin'])->group(function () {
        Route::get('/doctor/clinica', [DashboardController::class, 'show'])->name('dashboard');
        Route::get('/pacientes', [DashboardController::class, 'show'])->name('doctor-admin.pacientes');
        Route::get('/tratamientos', [TratamientoController::class, 'index'])->name('doctor-admin.tratamientos');
    });

    // Rutas del doctor
    Route::middleware(['role:doctor'])->group(function () {
        // Route::get('/doctor/clinica', [DashboardController::class, 'show'])->name('doctor.clinica');
        Route::get('/doctor/pacientes', [Pacientes::class, 'index'])->name('doctor.pacientes');
        Route::get('/doctor/tratamientos', [TratamientoController::class, 'index'])->name('doctor.tratamientos');
    });

    Route::middleware(['role:docor|doctor_admin'])->group(function (){
        Route::get('/facturas/{factura}/descargar', [ClinicaShow::class, 'download'])->name('facturas.download');
    });


    // Rutas de la clínica
    Route::middleware(['role:clinica'])->group(function () {
        Route::get('/clinica/pacientes', [Pacientes::class, 'index'])->name('clinica.pacientes');
    });

    Route::get('/paciente/{id}/perfil/', [PacienteShowController::class, 'show'])->name('pacientes-show');
    Route::get('/paciente/{id}/historial/{tratId?}', [PacienteHistorial::class, 'index'])->name('paciente-historial');
    Route::get('/clinica/{id}/', [ClinicaController::class, 'index'])->name('clinica');

});

