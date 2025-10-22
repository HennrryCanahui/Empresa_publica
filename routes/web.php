<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\AprobacionController;
use App\Http\Controllers\AdquisicionController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\PerfilController;

// Rutas de autenticación
Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Rutas específicas para roles
    Route::middleware(['role:solicitante'])->group(function () {
        Route::get('/solicitudes/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
        Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
    });
    
    Route::middleware(['role:presupuesto'])->group(function () {
        Route::get('/presupuestos/validar/{solicitud}', [PresupuestoController::class, 'validar'])->name('presupuestos.validar');
        Route::post('/presupuestos/procesar/{solicitud}', [PresupuestoController::class, 'procesarValidacion'])->name('presupuestos.procesar');
    });
    
    Route::middleware(['role:compras'])->group(function () {
        Route::get('/cotizaciones/crear/{solicitud}', [CotizacionController::class, 'create'])->name('cotizaciones.create');
        Route::post('/cotizaciones/guardar/{solicitud}', [CotizacionController::class, 'store'])->name('cotizaciones.store');
        Route::get('/proveedores/gestionar', [ProveedorController::class, 'index'])->name('proveedores.gestionar');
    });
    
    Route::middleware(['role:aprobador'])->group(function () {
        Route::get('/aprobaciones/evaluar/{solicitud}', [AprobacionController::class, 'evaluar'])->name('aprobaciones.evaluar');
        Route::post('/aprobaciones/procesar/{solicitud}', [AprobacionController::class, 'procesarEvaluacion'])->name('aprobaciones.procesar');
    });
    
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('usuarios', UsuarioController::class);
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/reportes/general', [ReporteController::class, 'general'])->name('reportes.general');
    });
    
    // Solicitudes
    Route::resource('solicitudes', SolicitudController::class);
    Route::post('solicitudes/{solicitud}/cancelar', [SolicitudController::class, 'cancelar'])->name('solicitudes.cancelar');
    
    // Presupuestos
    Route::get('presupuestos', [PresupuestoController::class, 'index'])->name('presupuestos.index');
    Route::get('presupuestos/{solicitud}/validar', [PresupuestoController::class, 'validar'])->name('presupuestos.validar');
    Route::post('presupuestos/{solicitud}/procesar', [PresupuestoController::class, 'procesarValidacion'])->name('presupuestos.procesar');
    
    // Cotizaciones
    Route::resource('cotizaciones', CotizacionController::class);
    Route::get('cotizaciones/solicitud/{solicitud}', [CotizacionController::class, 'create'])->name('cotizaciones.create');
    Route::get('cotizaciones/comparar/{solicitud}', [CotizacionController::class, 'comparar'])->name('cotizaciones.comparar');
    
    // Aprobaciones
    Route::get('aprobaciones', [AprobacionController::class, 'index'])->name('aprobaciones.index');
    Route::get('aprobaciones/{solicitud}/evaluar', [AprobacionController::class, 'evaluar'])->name('aprobaciones.evaluar');
    Route::post('aprobaciones/{solicitud}/procesar', [AprobacionController::class, 'procesarEvaluacion'])->name('aprobaciones.procesar');
    
    // Adquisiciones
    Route::resource('adquisiciones', AdquisicionController::class);
    Route::post('adquisiciones/{adquisicion}/estado', [AdquisicionController::class, 'actualizarEstado'])->name('adquisiciones.estado');
    
    // Productos
    Route::resource('productos', ProductoController::class);
    Route::post('productos/{producto}/toggle', [ProductoController::class, 'toggleStatus'])->name('productos.toggle');
    
    // Proveedores
    Route::resource('proveedores', ProveedorController::class);
    Route::post('proveedores/{proveedor}/toggle', [ProveedorController::class, 'toggleStatus'])->name('proveedores.toggle');
    
    // Unidades
    Route::resource('unidades', UnidadController::class);
    Route::post('unidades/{unidad}/toggle', [UnidadController::class, 'toggleStatus'])->name('unidades.toggle');
    
    // Usuarios
    Route::resource('usuarios', UsuarioController::class);
    Route::post('usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleStatus'])->name('usuarios.toggle');
    
    // Reportes
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/solicitudes-estado', [ReporteController::class, 'solicitudesPorEstado'])->name('reportes.solicitudes-estado');
    Route::get('reportes/gastos-unidad', [ReporteController::class, 'gastosPorUnidad'])->name('reportes.gastos-unidad');
    Route::get('reportes/tiempo-promedio', [ReporteController::class, 'tiempoPromedioSolicitudes'])->name('reportes.tiempo-promedio');
    
    // Auditoría
    Route::get('auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
    Route::get('auditoria/{registro}', [AuditoriaController::class, 'show'])->name('auditoria.show');
    
    // Documentos
    Route::get('documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('documentos/{documento}', [DocumentoController::class, 'show'])->name('documentos.show');
    Route::get('documentos/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
    
    // Perfil de usuario
    Route::get('perfil', [PerfilController::class, 'index'])->name('perfil.index');
    Route::put('perfil', [PerfilController::class, 'update'])->name('perfil.update');
});

// Rutas de autenticación
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/', [DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    
    // Solicitudes
    Route::prefix('solicitudes')->group(function () {
        Route::get('/', [SolicitudController::class, 'index'])->name('solicitudes.index');
        Route::get('/create', [SolicitudController::class, 'create'])->name('solicitudes.create');
        Route::post('/', [SolicitudController::class, 'store'])->name('solicitudes.store');
        Route::get('/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show');
        Route::get('/{solicitud}/edit', [SolicitudController::class, 'edit'])->name('solicitudes.edit');
        Route::put('/{solicitud}', [SolicitudController::class, 'update'])->name('solicitudes.update');
        Route::delete('/{solicitud}', [SolicitudController::class, 'destroy'])->name('solicitudes.destroy');
    });

    // Presupuestos
    Route::prefix('presupuestos')->group(function () {
        Route::get('/', [PresupuestoController::class, 'index'])->name('presupuestos.index');
        Route::get('/{solicitud}/validar', [PresupuestoController::class, 'validar'])->name('presupuestos.validar');
        Route::post('/{solicitud}/validar', [PresupuestoController::class, 'procesarValidacion'])->name('presupuestos.procesar');
    });

    // Cotizaciones
    Route::prefix('cotizaciones')->group(function () {
        Route::get('/', [CotizacionController::class, 'index'])->name('cotizaciones.index');
        Route::get('/create/{solicitud}', [CotizacionController::class, 'create'])->name('cotizaciones.create');
        Route::post('/', [CotizacionController::class, 'store'])->name('cotizaciones.store');
        Route::get('/{solicitud}/comparar', [CotizacionController::class, 'comparar'])->name('cotizaciones.comparar');
    Route::post('/seleccionar', [CotizacionController::class, 'seleccionar'])->name('cotizaciones.seleccionar');
    });

    // Aprobaciones
    Route::prefix('aprobaciones')->group(function () {
        Route::get('/', [AprobacionController::class, 'index'])->name('aprobaciones.index');
        Route::get('/{solicitud}/evaluar', [AprobacionController::class, 'evaluar'])->name('aprobaciones.evaluar');
        Route::post('/{solicitud}/evaluar', [AprobacionController::class, 'procesarEvaluacion'])->name('aprobaciones.procesar');
    });

    // Adquisiciones
    Route::prefix('adquisiciones')->group(function () {
        Route::get('/', [AdquisicionController::class, 'index'])->name('adquisiciones.index');
        Route::get('/create/{solicitud}', [AdquisicionController::class, 'create'])->name('adquisiciones.create');
        Route::post('/', [AdquisicionController::class, 'store'])->name('adquisiciones.store');
        Route::put('/{adquisicion}/estado', [AdquisicionController::class, 'actualizarEstado'])->name('adquisiciones.estado');
    });

    // Productos
    Route::prefix('productos')->group(function () {
        Route::get('/', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/create', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/{producto}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::delete('/{producto}', [ProductoController::class, 'destroy'])->name('productos.destroy');
    });

    // Proveedores
    Route::prefix('proveedores')->group(function () {
        Route::get('/', [ProveedorController::class, 'index'])->name('proveedores.index');
        Route::get('/create', [ProveedorController::class, 'create'])->name('proveedores.create');
        Route::post('/', [ProveedorController::class, 'store'])->name('proveedores.store');
        Route::get('/{proveedor}/edit', [ProveedorController::class, 'edit'])->name('proveedores.edit');
        Route::put('/{proveedor}', [ProveedorController::class, 'update'])->name('proveedores.update');
        Route::delete('/{proveedor}', [ProveedorController::class, 'destroy'])->name('proveedores.destroy');
    });

    // Unidades
    Route::prefix('unidades')->group(function () {
        Route::get('/', [UnidadController::class, 'index'])->name('unidades.index');
        Route::get('/create', [UnidadController::class, 'create'])->name('unidades.create');
        Route::post('/', [UnidadController::class, 'store'])->name('unidades.store');
        Route::get('/{unidad}/edit', [UnidadController::class, 'edit'])->name('unidades.edit');
        Route::put('/{unidad}', [UnidadController::class, 'update'])->name('unidades.update');
        Route::delete('/{unidad}', [UnidadController::class, 'destroy'])->name('unidades.destroy');
    });

    // Usuarios
    Route::prefix('usuarios')->group(function () {
        Route::get('/', [UsuarioController::class, 'index'])->name('usuarios.index');
        Route::get('/create', [UsuarioController::class, 'create'])->name('usuarios.create');
        Route::post('/', [UsuarioController::class, 'store'])->name('usuarios.store');
        Route::get('/{usuario}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
        Route::put('/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
        Route::delete('/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');
    });

    // Reportes
    Route::prefix('reportes')->group(function () {
        Route::get('/', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/tiempos', [ReporteController::class, 'tiemposPromedio'])->name('reportes.tiempos');
        Route::get('/solicitudes', [ReporteController::class, 'solicitudesPorEstado'])->name('reportes.solicitudes');
        Route::get('/presupuesto', [ReporteController::class, 'ejecucionPresupuesto'])->name('reportes.presupuesto');
        Route::get('/proveedores', [ReporteController::class, 'desempenoProveedores'])->name('reportes.proveedores');
    });

    // Auditoría
    Route::prefix('auditoria')->group(function () {
        Route::get('/', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/{id}', [AuditoriaController::class, 'show'])->name('auditoria.show');
    });

    // Documentos
    Route::prefix('documentos')->group(function () {
        Route::get('/', [DocumentoController::class, 'index'])->name('documentos.index');
        Route::post('/upload', [DocumentoController::class, 'upload'])->name('documentos.upload');
        Route::get('/{documento}/download', [DocumentoController::class, 'download'])->name('documentos.download');
        Route::delete('/{documento}', [DocumentoController::class, 'destroy'])->name('documentos.destroy');
    });

    // Perfil de usuario
    Route::prefix('perfil')->group(function () {
        Route::get('/', [PerfilController::class, 'index'])->name('perfil.index');
        Route::put('/', [PerfilController::class, 'update'])->name('perfil.update');
        Route::put('/password', [PerfilController::class, 'updatePassword'])->name('perfil.password');
    });
});