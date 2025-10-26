@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-receipt me-2"></i>Detalle de Cotización</h2>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-6">
    <div class="card mb-3">
      <div class="card-header bg-white">Información de Cotización</div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Solicitud:</dt>
          <dd class="col-sm-7"><strong>{{ $cotizacion->solicitud->numero_solicitud ?? '' }}</strong></dd>
          <dt class="col-sm-5">Proveedor:</dt>
          <dd class="col-sm-7"><strong>{{ $cotizacion->proveedor->razon_social ?? '' }}</strong></dd>
          <dt class="col-sm-5">NIT/RFC:</dt>
          <dd class="col-sm-7">{{ $cotizacion->proveedor->nit_rfc ?? '' }}</dd>
          <dt class="col-sm-5">Fecha cotización:</dt>
          <dd class="col-sm-7">{{ $cotizacion->fecha_cotizacion ? \Carbon\Carbon::parse($cotizacion->fecha_cotizacion)->format('d/m/Y') : '' }}</dd>
          <dt class="col-sm-5">Vigencia:</dt>
          <dd class="col-sm-7">{{ $cotizacion->vigencia_cotizacion ? \Carbon\Carbon::parse($cotizacion->vigencia_cotizacion)->format('d/m/Y') : '' }}</dd>
          <dt class="col-sm-5">Tiempo entrega:</dt>
          <dd class="col-sm-7">{{ $cotizacion->tiempo_entrega }} días</dd>
          <dt class="col-sm-5">Garantía:</dt>
          <dd class="col-sm-7">{{ $cotizacion->garantia }}</dd>
          <dt class="col-sm-5">Monto total:</dt>
          <dd class="col-sm-7 fw-bold text-success">Q {{ number_format($cotizacion->monto_total, 2) }}</dd>
          <dt class="col-sm-5">Seleccionada:</dt>
          <dd class="col-sm-7">
            @if($cotizacion->seleccionada)
              <span class="badge bg-success">Sí</span>
            @else
              <span class="badge bg-secondary">No</span>
            @endif
          </dd>
        </dl>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card mb-3">
      <div class="card-header bg-white">Condiciones y Observaciones</div>
      <div class="card-body">
        <p class="mb-2"><strong>Condiciones:</strong><br><small>{{ $cotizacion->condiciones_pago ?: 'Sin condiciones especificadas' }}</small></p>
        <p class="mb-0"><strong>Observaciones:</strong><br><small>{{ $cotizacion->observaciones ?: 'Sin observaciones' }}</small></p>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header bg-white">Detalles de la Cotización</div>
  <div class="card-body">
    @if($cotizacion->detalles->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Producto</th>
              <th>Descripción</th>
              <th>Cantidad</th>
              <th>Precio unitario</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            @foreach($cotizacion->detalles as $d)
              <tr>
                <td><strong>{{ $d->producto->nombre ?? '' }}</strong></td>
                <td><small>{{ $d->descripcion_proveedor }}</small></td>
                <td>{{ $d->cantidad }} {{ $d->unidad_medida }}</td>
                <td>Q {{ number_format($d->precio_unitario, 2) }}</td>
                <td class="fw-bold">Q {{ number_format($d->precio_total, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr class="table-light">
              <td colspan="4" class="text-end fw-bold">Total:</td>
              <td class="fw-bold text-success fs-5">Q {{ number_format($cotizacion->monto_total, 2) }}</td>
            </tr>
          </tfoot>
        </table>
      </div>
    @else
      <p class="text-center text-muted my-3">No hay detalles de cotización</p>
    @endif
  </div>
  <div class="card-footer bg-white">
    <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Regresar</a>
    @if($cotizacion->solicitud)
      <a href="{{ route('cotizaciones.comparar', $cotizacion->id_solicitud) }}" class="btn btn-outline-info"><i class="bi bi-arrow-left-right me-1"></i>Comparar cotizaciones</a>
    @endif
  </div>
</div>
@endsection
