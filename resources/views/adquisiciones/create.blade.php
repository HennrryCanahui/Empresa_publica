@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Generar Orden de Compra</h2>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-8">
    <div class="card mb-3">
      <div class="card-header bg-white">Solicitud: <strong>{{ $solicitud->numero_solicitud }}</strong></div>
      <div class="card-body">
        <p class="mb-2"><strong>Descripción:</strong> {{ $solicitud->descripcion }}</p>
        <p class="mb-2"><strong>Justificación:</strong> {{ $solicitud->justificacion }}</p>
        <p class="mb-0"><strong>Solicitante:</strong> {{ $solicitud->solicitante->name ?? '' }} - {{ $solicitud->unidad->nombre ?? '' }}</p>
      </div>
    </div>

    @if($cotizacionSeleccionada)
    <div class="card">
      <div class="card-header bg-white">Cotización seleccionada - <strong>{{ $cotizacionSeleccionada->proveedor->razon_social ?? '' }}</strong></div>
      <div class="card-body">
        <dl class="row">
          <dt class="col-sm-3">NIT/RFC:</dt>
          <dd class="col-sm-9">{{ $cotizacionSeleccionada->proveedor->nit_rfc ?? '' }}</dd>
          <dt class="col-sm-3">Dirección:</dt>
          <dd class="col-sm-9">{{ $cotizacionSeleccionada->proveedor->direccion ?? '' }}</dd>
          <dt class="col-sm-3">Teléfono:</dt>
          <dd class="col-sm-9">{{ $cotizacionSeleccionada->proveedor->telefono ?? '' }}</dd>
          <dt class="col-sm-3">Email:</dt>
          <dd class="col-sm-9">{{ $cotizacionSeleccionada->proveedor->email ?? '' }}</dd>
        </dl>

        <div class="table-responsive mt-3">
          <table class="table table-sm">
            <thead class="table-light">
              <tr><th>Producto</th><th>Cantidad</th><th>P. Unitario</th><th>Total</th></tr>
            </thead>
            <tbody>
              @foreach($cotizacionSeleccionada->detalles as $d)
                <tr>
                  <td>{{ $d->producto->nombre ?? '' }}</td>
                  <td>{{ $d->cantidad }} {{ $d->unidad_medida }}</td>
                  <td>Q {{ number_format($d->precio_unitario, 2) }}</td>
                  <td class="fw-bold">Q {{ number_format($d->precio_total, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr class="table-light">
                <td colspan="3" class="text-end fw-bold">Total:</td>
                <td class="fw-bold text-success fs-5">Q {{ number_format($cotizacionSeleccionada->monto_total, 2) }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>
    @endif
  </div>

  <div class="col-lg-4">
    <form method="POST" action="{{ route('adquisiciones.store') }}" class="card">
      @csrf
      <input type="hidden" name="id_solicitud" value="{{ $solicitud->id_solicitud }}">
      <input type="hidden" name="id_proveedor" value="{{ $cotizacionSeleccionada->id_proveedor ?? '' }}">
      <input type="hidden" name="monto_total" value="{{ $cotizacionSeleccionada->monto_total ?? 0 }}">

      <div class="card-header bg-white">Datos de la Orden de Compra</div>
      <div class="card-body">
        <div class="mb-3">
          <label class="form-label">Fecha estimada entrega <span class="text-danger">*</span></label>
          <input type="date" name="fecha_estimada_entrega" class="form-control" required min="{{ date('Y-m-d') }}">
          <small class="text-muted">Tiempo estimado: {{ $cotizacionSeleccionada->tiempo_entrega ?? 0 }} días</small>
        </div>

        <div class="mb-3">
          <label class="form-label">Lugar de entrega <span class="text-danger">*</span></label>
          <textarea name="lugar_entrega" rows="2" class="form-control" required>{{ old('lugar_entrega', $solicitud->unidad->direccion ?? '') }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Condiciones de pago</label>
          <textarea name="condiciones_pago" rows="2" class="form-control">{{ old('condiciones_pago', $cotizacionSeleccionada->condiciones_pago ?? '') }}</textarea>
        </div>

        <div class="mb-3">
          <label class="form-label">Observaciones</label>
          <textarea name="observaciones" rows="2" class="form-control">{{ old('observaciones') }}</textarea>
        </div>
      </div>
      <div class="card-footer bg-white d-flex justify-content-between">
        <a href="{{ route('adquisiciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-success"><i class="bi bi-check2 me-1"></i>Generar Orden</button>
      </div>
    </form>
  </div>
</div>
@endsection