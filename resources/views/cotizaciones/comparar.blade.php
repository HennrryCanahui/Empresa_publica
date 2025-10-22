@extends('layouts.app')

@section('title', 'Comparar Cotizaciones')

@section('content')
<div class="row mb-4">
    <div class="col-md-8">
        <h2>
            <i class="bi bi-table"></i> 
            Comparar Cotizaciones - Solicitud #{{ $solicitud->numero_solicitud }}
        </h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver al Listado
        </a>
    </div>
</div>

<!-- Información de la Solicitud -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="card-title mb-0">Información de la Solicitud</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-4">
                <strong>Unidad Solicitante:</strong>
                <p class="mb-0">{{ $solicitud->unidadSolicitante->nombre }}</p>
            </div>
            <div class="col-md-4">
                <strong>Fecha de Solicitud:</strong>
                <p class="mb-0">{{ $solicitud->created_at->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-4">
                <strong>Monto Estimado:</strong>
                <p class="mb-0">{{ number_format($solicitud->monto_estimado_total, 2) }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <strong>Descripción:</strong>
                <p class="mb-0">{{ $solicitud->descripcion }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Comparativa -->
<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Tabla Comparativa de Cotizaciones</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 25%">Producto</th>
                        <th style="width: 10%">Cant.</th>
                        @foreach($solicitud->cotizaciones as $cotizacion)
                        <th colspan="2" class="text-center" style="width: {{ 65/count($solicitud->cotizaciones) }}%">
                            {{ $cotizacion->proveedor->razon_social }}<br>
                            <small class="text-muted">{{ $cotizacion->numero_cotizacion }}</small>
                        </th>
                        @endforeach
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                        @foreach($solicitud->cotizaciones as $cotizacion)
                        <th class="text-center">P.U.</th>
                        <th class="text-center">Total</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($solicitud->detalles as $detalle)
                    <tr>
                        <td>
                            {{ $detalle->producto->nombre }}
                            @if($detalle->especificaciones_adicionales)
                            <br>
                            <small class="text-muted">
                                {{ $detalle->especificaciones_adicionales }}
                            </small>
                            @endif
                        </td>
                        <td class="text-center">
                            {{ number_format($detalle->cantidad, 2) }}<br>
                            <small class="text-muted">{{ $detalle->producto->unidad_medida }}</small>
                        </td>
                        @foreach($solicitud->cotizaciones as $cotizacion)
                            @php
                                $detalleCotizacion = $cotizacion->detalles->where('id_producto', $detalle->id_producto)->first();
                            @endphp
                            <td class="text-end">
                                @if($detalleCotizacion)
                                    {{ number_format($detalleCotizacion->precio_unitario, 2) }}
                                @else
                                    <span class="text-danger">N/A</span>
                                @endif
                            </td>
                            <td class="text-end {{ $detalleCotizacion && isLowestPrice($detalleCotizacion, $detalle) ? 'table-success' : '' }}">
                                @if($detalleCotizacion)
                                    {{ number_format($detalleCotizacion->precio_total, 2) }}
                                @else
                                    <span class="text-danger">N/A</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="2" class="text-end"><strong>Total:</strong></td>
                        @foreach($solicitud->cotizaciones as $cotizacion)
                        <td colspan="2" class="text-end">
                            <strong>{{ number_format($cotizacion->monto_total, 2) }}</strong><br>
                            <small class="text-muted">
                                {{ number_format(($cotizacion->monto_total - $solicitud->monto_estimado_total) / $solicitud->monto_estimado_total * 100, 1) }}% vs Est.
                            </small>
                        </td>
                        @endforeach
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end"><strong>Tiempo de Entrega:</strong></td>
                        @foreach($solicitud->cotizaciones as $cotizacion)
                        <td colspan="2" class="text-center">{{ $cotizacion->tiempo_entrega }} días</td>
                        @endforeach
                    </tr>
                    <tr>
                        <td colspan="2" class="text-end"><strong>Condiciones de Pago:</strong></td>
                        @foreach($solicitud->cotizaciones as $cotizacion)
                        <td colspan="2">{{ $cotizacion->condiciones_pago }}</td>
                        @endforeach
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    Las celdas en verde indican el precio más bajo para cada producto.
                </div>
            </div>
        </div>

        <!-- Selección de Cotización -->
        @can('seleccionar-cotizacion')
        @if(!$solicitud->cotizaciones->where('estado', 'Seleccionada')->count())
        <div class="row mt-4">
            <div class="col-md-12">
                <form action="{{ route('cotizaciones.seleccionar', $solicitud) }}" method="POST" class="card">
                    @csrf
                    <div class="card-header">
                        <h5 class="card-title mb-0">Seleccionar Cotización Ganadora</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_cotizacion" class="form-label">Cotización Seleccionada *</label>
                                    <select class="form-select @error('id_cotizacion') is-invalid @enderror" 
                                            id="id_cotizacion" name="id_cotizacion" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($solicitud->cotizaciones as $cotizacion)
                                        <option value="{{ $cotizacion->id_cotizacion }}">
                                            {{ $cotizacion->proveedor->razon_social }} - 
                                            ${{ number_format($cotizacion->monto_total, 2) }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('id_cotizacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="justificacion" class="form-label">Justificación de Selección *</label>
                                    <textarea class="form-control @error('justificacion') is-invalid @enderror" 
                                              id="justificacion" name="justificacion" rows="3" required>{{ old('justificacion') }}</textarea>
                                    @error('justificacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Confirmar Selección
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        @endcan
    </div>
</div>

@php
function isLowestPrice($detalleCotizacion, $detalleSolicitud) {
    $cotizaciones = $detalleSolicitud->solicitud->cotizaciones;
    $precios = $cotizaciones->map(function($cotizacion) use ($detalleSolicitud) {
        $detalle = $cotizacion->detalles->where('id_producto', $detalleSolicitud->id_producto)->first();
        return $detalle ? $detalle->precio_unitario : PHP_FLOAT_MAX;
    });
    return $detalleCotizacion->precio_unitario == $precios->min();
}
@endphp
@endsection
