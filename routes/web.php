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
        Route::get('/solicitudes', [SolicitudController::class, 'misSolicitudes'])->name('solicitudes.index');
        Route::get('/solicitudes/crear', [SolicitudController::class, 'create'])->name('solicitudes.create');
        Route::post('/solicitudes', [SolicitudController::class, 'store'])->name('solicitudes.store');
        Route::get('/solicitudes/{solicitud}', [SolicitudController::class, 'show'])->name('solicitudes.show');
        Route::get('/solicitudes/{solicitud}/editar', [SolicitudController::class, 'edit'])->name('solicitudes.edit');
        Route::put('/solicitudes/{solicitud}', [SolicitudController::class, 'update'])->name('solicitudes.update');
        Route::patch('/solicitudes/{solicitud}/anular', [SolicitudController::class, 'anular'])->name('solicitudes.anular');
        Route::patch('/solicitudes/{solicitud}/reabrir', [SolicitudController::class, 'reabrir'])->name('solicitudes.reabrir');
        Route::get('/solicitudes/{solicitud}/historial', [SolicitudController::class, 'historial'])->name('solicitudes.historial');
        Route::post('/solicitudes/{solicitud}/cambiar-estado', [SolicitudController::class, 'cambiarEstado'])->name('solicitudes.cambiarEstado');
    });
    
    // ========================================
    // PRESUPUESTO - Validación presupuestaria
    // ========================================
    Route::middleware(['role:Presupuesto,Admin'])->group(function () {
        Route::get('/presupuesto/pendientes', [PresupuestoController::class, 'pendientes'])->name('presupuesto.pendientes');
        Route::get('/presupuesto/{solicitud}/validar', [PresupuestoController::class, 'validar'])->name('presupuesto.validar');
        Route::post('/presupuesto', [PresupuestoController::class, 'store'])->name('presupuesto.store');
    });
    
    // ========================================
    // COMPRAS - Cotizaciones y Adquisiciones
    // ========================================
    Route::middleware(['role:Compras,Admin'])->group(function () {
        Route::get('/compras/solicitudes', [CotizacionController::class, 'solicitudesPresupuestadas'])->name('compras.solicitudes');
        
        Route::get('/cotizaciones/{solicitud}', [CotizacionController::class, 'index'])->name('cotizaciones.index');
        Route::get('/cotizaciones/{solicitud}/crear', [CotizacionController::class, 'create'])->name('cotizaciones.create');
        Route::post('/cotizaciones', [CotizacionController::class, 'store'])->name('cotizaciones.store');
        Route::get('/cotizaciones/{cotizacion}/editar', [CotizacionController::class, 'edit'])->name('cotizaciones.edit');
        Route::put('/cotizaciones/{cotizacion}', [CotizacionController::class, 'update'])->name('cotizaciones.update');
        Route::post('/cotizaciones/{cotizacion}/seleccionar', [CotizacionController::class, 'seleccionar'])->name('cotizaciones.seleccionar');
        Route::post('/cotizaciones/{cotizacion}/descartar', [CotizacionController::class, 'descartar'])->name('cotizaciones.descartar');
        
        Route::resource('proveedores', ProveedorController::class);
        
        Route::get('/ordenes', [AdquisicionController::class, 'index'])->name('ordenes.index');
        Route::get('/ordenes/crear/{solicitud}', [AdquisicionController::class, 'create'])->name('ordenes.create');
        Route::post('/ordenes', [AdquisicionController::class, 'store'])->name('ordenes.store');
        Route::get('/ordenes/{adquisicion}', [AdquisicionController::class, 'show'])->name('ordenes.show');
        Route::post('/ordenes/{adquisicion}/entrega', [AdquisicionController::class, 'registrarEntrega'])->name('ordenes.entrega');
    });
    
    // ========================================
    // AUTORIDAD - Aprobación de solicitudes
    // ========================================
    Route::middleware(['role:Autoridad,Admin'])->group(function () {
        Route::get('/aprobacion/pendientes', [AprobacionController::class, 'pendientes'])->name('aprobacion.pendientes');
        Route::get('/aprobacion/{solicitud}', [AprobacionController::class, 'show'])->name('aprobacion.show');
        Route::post('/aprobacion', [AprobacionController::class, 'store'])->name('aprobacion.store');
        Route::get('/aprobacion/{solicitud}/comparar-cotizaciones', [AprobacionController::class, 'compararCotizaciones'])->name('aprobacion.comparar');
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