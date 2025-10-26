<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\PresupuestoController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\AprobacionController;
use App\Http\Controllers\AdquisicionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\UnidadController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CatalogoProductoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\ProfileController;

// Rutas de autenticación (Breeze las genera automáticamente)
require __DIR__.'/auth.php';

// Ruta raíz
Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

// Rutas protegidas - Requieren autenticación
Route::middleware(['auth'])->group(function () {
    
    // Dashboard general (todos los roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Perfil de usuario
    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notificaciones (todos los roles)
    Route::get('/notificaciones', [NotificacionController::class, 'index'])->name('notificaciones.index');
    Route::post('/notificaciones/{notificacion}/leer', [NotificacionController::class, 'marcarLeida'])->name('notificaciones.leer');
    
    // ========================================
    // SOLICITANTE - Gestión de solicitudes propias
    // ========================================
    Route::middleware(['role:Solicitante,Admin'])->group(function () {
        Route::get('/solicitudes', [SolicitudController::class, 'index'])->name('solicitudes.index');
        Route::get('/solicitudes/crear', [SolicitudController::class, 'create'])->name('solicitudes.create');
        Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
        Route::get('/solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show');
        Route::get('/solicitudes/{solicitud}/editar', [SolicitudController::class, 'edit'])->name('solicitudes.edit');
        Route::put('/solicitudes/{solicitud}', [SolicitudController::class, 'update'])->name('solicitudes.update');
        Route::get('/solicitudes/{solicitud}/historial', [SolicitudController::class, 'historial'])->name('solicitudes.historial');
        Route::post('/solicitudes/{solicitud}/enviar-presupuesto', [SolicitudController::class, 'enviarAPresupuesto'])->name('solicitudes.enviar-presupuesto');
        Route::post('/solicitudes/{solicitud}/cancelar', [SolicitudController::class, 'cancelar'])->name('solicitudes.cancelar');
    });
    
    // ========================================
    // PRESUPUESTO - Validación presupuestaria
    // ========================================
    Route::middleware(['role:Presupuesto,Admin'])->group(function () {
        Route::get('/presupuesto', [PresupuestoController::class, 'index'])->name('presupuesto.index');
        Route::get('/presupuesto/{solicitud}/validar', [PresupuestoController::class, 'validar'])->name('presupuesto.validar');
        Route::post('/presupuesto/{solicitud}/procesar', [PresupuestoController::class, 'procesarValidacion'])->name('presupuesto.procesar');
        Route::get('/presupuesto/historial', [PresupuestoController::class, 'historial'])->name('presupuesto.historial');
        Route::get('/presupuesto/ver/{presupuesto}', [PresupuestoController::class, 'ver'])->name('presupuesto.ver');
    });
    
    // ========================================
    // COMPRAS - Cotizaciones y Adquisiciones
    // ========================================
    Route::middleware(['role:Compras,Admin'])->group(function () {
        Route::get('/compras', [CotizacionController::class, 'index'])->name('compras.index');
        
        Route::get('/cotizaciones/crear/{solicitud}', [CotizacionController::class, 'create'])->name('cotizaciones.create');
        Route::post('/cotizaciones', [CotizacionController::class, 'store'])->name('cotizaciones.store');
        Route::get('/cotizaciones/comparar/{solicitud}', [CotizacionController::class, 'comparar'])->name('cotizaciones.comparar');
        Route::post('/cotizaciones/{cotizacion}/seleccionar', [CotizacionController::class, 'seleccionar'])->name('cotizaciones.seleccionar');
        Route::post('/cotizaciones/enviar-aprobacion/{solicitud}', [CotizacionController::class, 'enviarAAprobacion'])->name('cotizaciones.enviar-aprobacion');
        Route::get('/cotizaciones/ver/{cotizacion}', [CotizacionController::class, 'ver'])->name('cotizaciones.ver');
        
        Route::resource('proveedores', ProveedorController::class)->parameters([
            'proveedores' => 'proveedor'
        ]);
        
        Route::get('/adquisiciones', [AdquisicionController::class, 'index'])->name('adquisiciones.index');
        Route::get('/adquisiciones/crear/{solicitud}', [AdquisicionController::class, 'create'])->name('adquisiciones.create');
        Route::post('/adquisiciones', [AdquisicionController::class, 'store'])->name('adquisiciones.store');
        Route::get('/adquisiciones/ver/{adquisicion}', [AdquisicionController::class, 'ver'])->name('adquisiciones.ver');
        Route::post('/adquisiciones/{adquisicion}/entrega', [AdquisicionController::class, 'actualizarEntrega'])->name('adquisiciones.entrega');
        Route::get('/adquisiciones/historial', [AdquisicionController::class, 'historial'])->name('adquisiciones.historial');
    });
    
    // ========================================
    // AUTORIDAD - Aprobación de solicitudes
    // ========================================
    Route::middleware(['role:Autoridad,Admin'])->group(function () {
        Route::get('/aprobacion', [AprobacionController::class, 'index'])->name('aprobacion.index');
        Route::get('/aprobacion/revisar/{solicitud}', [AprobacionController::class, 'revisar'])->name('aprobacion.revisar');
        Route::post('/aprobacion/procesar/{solicitud}', [AprobacionController::class, 'procesar'])->name('aprobacion.procesar');
        Route::get('/aprobacion/historial', [AprobacionController::class, 'historial'])->name('aprobacion.historial');
        Route::get('/aprobacion/ver/{aprobacion}', [AprobacionController::class, 'ver'])->name('aprobacion.ver');
    });
    
    // ========================================
    // ADMIN - Administración completa
    // ========================================
    Route::middleware(['role:Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('usuarios', UsuarioController::class);
        Route::post('usuarios/{usuario}/toggle-activo', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
        
        Route::resource('unidades', UnidadController::class);
        
        Route::resource('categorias', CategoriaProductoController::class);
        Route::resource('productos', CatalogoProductoController::class);
        
        Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
        Route::get('/reportes/solicitudes', [ReporteController::class, 'solicitudes'])->name('reportes.solicitudes');
        Route::get('/reportes/proveedores', [ReporteController::class, 'proveedores'])->name('reportes.proveedores');
        Route::get('/reportes/presupuesto', [ReporteController::class, 'presupuesto'])->name('reportes.presupuesto');
        Route::post('/reportes/exportar', [ReporteController::class, 'exportar'])->name('reportes.exportar');
        
        Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('auditoria.index');
        Route::get('/auditoria/{auditoria}', [AuditoriaController::class, 'show'])->name('auditoria.show');
        
        Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
    });
});

Route::fallback(function () {
    abort(404);
});