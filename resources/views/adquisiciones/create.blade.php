@extends('layouts.app')

@section('title', 'Generar Orden de Compra')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-bag"></i> 
            Generar Orden de Compra - Solicitud #{{ $solicitud->numero_solicitud }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('adquisiciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<form action="{{ route('adquisiciones.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="id_solicitud" value="{{ $solicitud->id_solicitud }}">
    <input type="hidden" name="id_cotizacion_seleccionada" value="{{ $cotizacion->id_cotizacion }}">

    <div class="row">
        <div class="col-md-8">
            <!-- Información de la Orden -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información de la Orden</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="numero_orden_compra" class="form-label">Número de Orden *</label>
                            <input type="text" class="form-control @error('numero_orden_compra') is-invalid @enderror" 
                                   id="numero_orden_compra" name="numero_orden_compra" 
                                   value="{{ old('numero_orden_compra') }}" required>
                            @error('numero_orden_compra')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_orden" class="form-label">Fecha de Orden *</label>
                            <input type="date" class="form-control @error('fecha_orden') is-invalid @enderror" 
                                   id="fecha_orden" name="fecha_orden" 
                                   value="{{ old('fecha_orden', date('Y-m-d')) }}" required>
                            @error('fecha_orden')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="fecha_entrega_esperada" class="form-label">Fecha de Entrega Esperada *</label>
                            <input type="date" class="form-control @error('fecha_entrega_esperada') is-invalid @enderror" 
                                   id="fecha_entrega_esperada" name="fecha_entrega_esperada" 
                                   value="{{ old('fecha_entrega_esperada', date('Y-m-d', strtotime('+' . $cotizacion->tiempo_entrega . ' days'))) }}" 
                                   required>
                            @error('fecha_entrega_esperada')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="lugar_entrega" class="form-label">Lugar de Entrega *</label>
                            <input type="text" class="form-control @error('lugar_entrega') is-invalid @enderror" 
                                   id="lugar_entrega" name="lugar_entrega" 
                                   value="{{ old('lugar_entrega') }}" required>
                            @error('lugar_entrega')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="instrucciones_especiales" class="form-label">Instrucciones Especiales</label>
                            <textarea class="form-control @error('instrucciones_especiales') is-invalid @enderror" 
                                      id="instrucciones_especiales" name="instrucciones_especiales" 
                                      rows="3">{{ old('instrucciones_especiales') }}</textarea>
                            @error('instrucciones_especiales')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detalles de Productos -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Detalles de Productos</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio Unit.</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cotizacion->detalles as $detalle)
                                <tr>
                                    <td>
                                        {{ $detalle->producto->nombre }}
                                        @if($detalle->observaciones)
                                        <br>
                                        <small class="text-muted">
                                            {{ $detalle->observaciones }}
                                        </small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ number_format($detalle->cantidad, 2) }}
                                        {{ $detalle->producto->unidad_medida }}
                                    </td>
                                    <td>${{ number_format($detalle->precio_unitario, 2) }}</td>
                                    <td>${{ number_format($detalle->precio_total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                    <td><strong>${{ number_format($cotizacion->monto_total, 2) }}</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Información del Proveedor -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Información del Proveedor</h5>
                </div>
                <div class="card-body">
                    <p><strong>Razón Social:</strong><br>
                        {{ $cotizacion->proveedor->razon_social }}
                    </p>
                    <p><strong>NIT:</strong><br>
                        {{ $cotizacion->proveedor->nit }}
                    </p>
                    <p><strong>Dirección:</strong><br>
                        {{ $cotizacion->proveedor->direccion }}
                    </p>
                    <p><strong>Contacto:</strong><br>
                        {{ $cotizacion->proveedor->contacto_principal }}
                    </p>
                    <p><strong>Teléfono:</strong><br>
                        {{ $cotizacion->proveedor->telefono }}
                    </p>
                    <p><strong>Correo:</strong><br>
                        {{ $cotizacion->proveedor->correo }}
                    </p>
                </div>
            </div>

            <!-- Documento de Orden -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Documento de Orden</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="archivo_orden" class="form-label">Adjuntar PDF *</label>
                        <input type="file" class="form-control @error('archivo_orden') is-invalid @enderror" 
                               id="archivo_orden" name="archivo_orden" 
                               accept=".pdf" required>
                        <div class="form-text">Formato PDF (Máx. 5MB)</div>
                        @error('archivo_orden')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card">
                <div class="card-body">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Generar Orden de Compra
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
