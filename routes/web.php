<?php

use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\CarpetaController;
use App\Http\Controllers\ClinicaController;
use App\Http\Controllers\DashboardController;

use App\Http\Controllers\Pacientes\ImagenesController;
use App\Http\Controllers\Pacientes\Pacientes;
use App\Http\Controllers\Pacientes\PacienteHistorial;
use App\Http\Controllers\Pacientes\PacienteShowController;

use App\Http\Controllers\PacientesAdmin;
use App\Http\Controllers\PermissionsController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\UsersController;
use App\Livewire\ClinicaShow;
use App\Livewire\Pacientes\Pacientes as PacientesPacientes;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum',config('jetstream.auth_session'),'verified',])->group(function () {
    Route::get('/dashboard',[DashboardController::class, 'show'])->name('dashboard');
});
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/paciente/{paciente}/etapa/{etapa}/imagenes', [ImagenesController::class, 'verImagenes'])
    ->middleware('role:doctor_admin|doctor|clinica|admin')->name('imagenes.ver');

    Route::get('/paciente/{paciente}/stripping/', [ImagenesController::class, 'verStripping'])
    ->middleware('role:doctor_admin|doctor|clinica_user|admin')->name('stripping');

    Route::get('/imagenes/{filePath}', [ImagenesController::class, 'mostrarImagen'])
    ->name('imagenes.protegidas')->where('filePath', '.*');

    Route::get('/etapa/descargar/{filePath}', [ArchivoController::class, 'archivo'])
    ->name('archivo.descargar')->where('filePath', '.*');

    //Rutas PDF Facturas
    Route::get('/facturas/{factura}/descargar', [ClinicaShow::class, 'download'])->name('facturas.download');
    Route::get('/ver-pdf/{ruta}', [ClinicaController::class, 'mostrarVistaPdf'])
    ->where('ruta', '.*') // Permite rutas con subcarpetas
    ->name('ver.pdf');
    Route::get('/ver-pdf/{ruta}', [ClinicaController::class, 'verPdfPrivado'])
    ->where('ruta', '.*') // Permite rutas con subcarpetas
    ->name('ver.pdf');

    Route::get('/subir', [UploadController::class,'index'])->name('upload.index');
    Route::post('/upload', [UploadController::class,'upload'])->name('upload');
    Route::post('/upload-cbct', [PacientesPacientes::class,'guardarCBCT'])->name('upload-cbct');

    // Rutas del administrador
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/clinicas', [DashboardController::class, 'show'])->name('admin.clinica');
        Route::get('/admin/clinica/{id}', [ClinicaController::class, 'index'])->name('admin.clinica.view');
        Route::post('/admin/clinica/{id}', [ClinicaController::class, 'destroy'])->name('admin.clinica.delete');

        Route::get('/admin/pacientes',[PacientesAdmin::class, 'index'])->name('admin.pacientes');
        Route::post('/admin/paciente/{id}', [PacientesAdmin::class, 'destroy'])->name('admin.pacientes.delete');

        Route::get('/admin/tratamientos', [TratamientoController::class, 'index'])->name('admin.tratamientos');

        Route::get('/admin/mi-unidad', [CarpetaController::class, 'index'])->name('admin.archivos');
        Route::get('/admin/mi-unidad/{id}', [CarpetaController::class, 'show'])->name('admin.archivos.view');
        Route::post('/admin/mi-unidad/{id}', [CarpetaController::class, 'destroy'])->name('admin.archivos.delete');

        Route::get('/admin/permisos', [PermissionsController::class, 'index'])->name('permisos');
    });

    // Rutas del doctor Administrador
    Route::middleware(['role:doctor_admin|admin'])->group(function(){
        Route::get('/users', [UsersController::class, 'index'])->name('users');
    });

    Route::middleware(['role:doctor_admin'])->group(function () {
        Route::get('/pacientes', [DashboardController::class, 'show'])->name('doctor-admin.pacientes');
        Route::get('/tratamientos', [TratamientoController::class, 'index'])->name('doctor-admin.tratamientos');
    });

    // Rutas del doctor
    Route::middleware(['role:doctor'])->group(function () {
        Route::get('/doctor/pacientes', [Pacientes::class, 'index'])->name('doctor.pacientes');
        Route::get('/doctor/tratamientos', [TratamientoController::class, 'index'])->name('doctor.tratamientos');
    });

    // Rutas de la clínica
    Route::middleware(['role:clinica'])->group(function () {
        Route::get('/clinica/pacientes', [Pacientes::class, 'index'])->name('clinica.pacientes');
    });

    Route::get('/paciente/{id}/perfil/', [PacienteShowController::class, 'show'])->name('pacientes-show');
    Route::get('/paciente/{id}/historial/{tratId?}', [PacienteHistorial::class, 'index'])->name('paciente-historial');
    Route::get('/clinica/{id}/', [ClinicaController::class, 'index'])->name('clinica');
});
