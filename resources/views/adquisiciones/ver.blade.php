@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-file-earmark-text me-2"></i>Detalle de Orden de Compra</h2>
@endsection

@section('content')
<div class="row">
  <div class="col-lg-6">
    <div class="card mb-3">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span>Orden de Compra</span>
        <strong class="text-primary">{{ $adquisicion->numero_orden_compra }}</strong>
      </div>
      <div class="card-body">
        <dl class="row mb-0">
          <dt class="col-sm-5">Solicitud:</dt>
          <dd class="col-sm-7"><strong>{{ $adquisicion->solicitud->numero_solicitud ?? '' }}</strong></dd>
          <dt class="col-sm-5">Proveedor:</dt>
          <dd class="col-sm-7"><strong>{{ $adquisicion->proveedor->razon_social ?? '' }}</strong></dd>
          <dt class="col-sm-5">NIT/RFC:</dt>
          <dd class="col-sm-7">{{ $adquisicion->proveedor->nit_rfc ?? '' }}</dd>
          <dt class="col-sm-5">Fecha orden:</dt>
          <dd class="col-sm-7">{{ $adquisicion->fecha_orden_compra ? \Carbon\Carbon::parse($adquisicion->fecha_orden_compra)->format('d/m/Y') : '' }}</dd>
          <dt class="col-sm-5">Fecha est. entrega:</dt>
          <dd class="col-sm-7">{{ $adquisicion->fecha_estimada_entrega ? \Carbon\Carbon::parse($adquisicion->fecha_estimada_entrega)->format('d/m/Y') : '' }}</dd>
          <dt class="col-sm-5">Monto total:</dt>
          <dd class="col-sm-7 fw-bold text-success">Q {{ number_format($adquisicion->monto_total, 2) }}</dd>
          <dt class="col-sm-5">Estado:</dt>
          <dd class="col-sm-7">
            @if($adquisicion->estado_entrega == 'Pendiente')
              <span class="badge bg-warning text-dark">Pendiente</span>
            @elseif($adquisicion->estado_entrega == 'Entregado')
              <span class="badge bg-success">Entregado</span>
            @elseif($adquisicion->estado_entrega == 'Parcial')
              <span class="badge bg-info">Parcial</span>
            @else
              <span class="badge bg-secondary">{{ $adquisicion->estado_entrega }}</span>
            @endif
          </dd>
        </dl>
      </div>
    </div>

    <div class="card mb-3">
      <div class="card-header bg-white">Condiciones y Observaciones</div>
      <div class="card-body">
        <p class="mb-2"><strong>Lugar de entrega:</strong><br>{{ $adquisicion->lugar_entrega }}</p>
        <p class="mb-2"><strong>Condiciones de pago:</strong><br>{{ $adquisicion->condiciones_pago ?: 'No especificadas' }}</p>
        <p class="mb-0"><strong>Observaciones:</strong><br>{{ $adquisicion->observaciones ?: 'Sin observaciones' }}</p>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    @if($adquisicion->estado_entrega == 'Pendiente' || $adquisicion->estado_entrega == 'Parcial')
      <form method="POST" action="{{ route('adquisiciones.entrega', $adquisicion->id_adquisicion) }}" class="card mb-3">
        @csrf
        @method('PUT')
        <div class="card-header bg-white">Actualizar Estado de Entrega</div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">Estado de entrega</label>
            <select name="estado_entrega" class="form-select" required>
              <option value="Pendiente" {{ $adquisicion->estado_entrega == 'Pendiente' ? 'selected' : '' }}>Pendiente</option>
              <option value="Parcial" {{ $adquisicion->estado_entrega == 'Parcial' ? 'selected' : '' }}>Entrega Parcial</option>
              <option value="Entregado" {{ $adquisicion->estado_entrega == 'Entregado' ? 'selected' : '' }}>Entregado</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Fecha de entrega</label>
            <input type="date" name="fecha_entrega" class="form-control" value="{{ $adquisicion->fecha_entrega ? \Carbon\Carbon::parse($adquisicion->fecha_entrega)->format('Y-m-d') : '' }}" max="{{ date('Y-m-d') }}">
          </div>

          <div class="mb-3">
            <label class="form-label">Notas de entrega</label>
            <textarea name="notas_entrega" rows="3" class="form-control">{{ old('notas_entrega', $adquisicion->notas_entrega) }}</textarea>
          </div>
        </div>
        <div class="card-footer bg-white">
          <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i>Actualizar entrega</button>
        </div>
      </form>
    @else
      <div class="card mb-3">
        <div class="card-header bg-white bg-success text-white">
          <i class="bi bi-check-circle me-2"></i>Entrega completada
        </div>
        <div class="card-body">
          <p class="mb-2"><strong>Fecha de entrega:</strong> {{ $adquisicion->fecha_entrega ? \Carbon\Carbon::parse($adquisicion->fecha_entrega)->format('d/m/Y') : '' }}</p>
          <p class="mb-0"><strong>Notas:</strong><br>{{ $adquisicion->notas_entrega ?: 'Sin notas' }}</p>
        </div>
      </div>
    @endif

    <div class="card">
      <div class="card-header bg-white">Datos del Responsable</div>
      <div class="card-body">
        <p class="mb-0"><strong>Usuario Compras:</strong> {{ $adquisicion->usuarioCompras->name ?? '' }}</p>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header bg-white">Detalles de la Solicitud</div>
  <div class="card-body">
    <p><strong>Descripción:</strong> {{ $adquisicion->solicitud->descripcion ?? '' }}</p>
    @if($adquisicion->solicitud->detalles->count())
      <div class="table-responsive mt-3">
        <table class="table table-sm table-hover">
          <thead class="table-light">
            <tr><th>Producto</th><th>Especificaciones</th><th>Cant.</th><th>Precio</th><th>Total</th></tr>
          </thead>
          <tbody>
            @foreach($adquisicion->solicitud->detalles as $d)
              <tr>
                <td>{{ $d->producto->nombre ?? '' }}</td>
                <td><small>{{ $d->especificaciones_tecnicas }}</small></td>
                <td>{{ $d->cantidad }} {{ $d->unidad->abreviatura ?? '' }}</td>
                <td>Q {{ number_format($d->precio_estimado, 2) }}</td>
                <td class="fw-bold">Q {{ number_format($d->subtotal_estimado, 2) }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
  <div class="card-footer bg-white">
    <a href="{{ route('adquisiciones.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Regresar</a>
  </div>
</div>
@endsection