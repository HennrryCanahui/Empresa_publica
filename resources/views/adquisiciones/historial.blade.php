@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-clock-history me-2"></i>Historial de Adquisiciones</h2>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    @if($adquisiciones->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Orden Compra</th>
              <th>Solicitud</th>
              <th>Proveedor</th>
              <th>Fecha Orden</th>
              <th>Monto</th>
              <th>Estado Entrega</th>
              <th>Fecha Entrega</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($adquisiciones as $a)
              <tr>
                <td><strong>{{ $a->numero_orden_compra }}</strong></td>
                <td>
                  {{ $a->solicitud->numero_solicitud ?? '' }}<br>
                  <small class="text-muted">{{ Str::limit($a->solicitud->descripcion ?? '', 40) }}</small>
                </td>
                <td>{{ $a->proveedor->razon_social ?? '' }}</td>
                <td>{{ $a->fecha_orden_compra ? \Carbon\Carbon::parse($a->fecha_orden_compra)->format('d/m/Y') : '' }}</td>
                <td class="fw-bold">Q {{ number_format($a->monto_total, 2) }}</td>
                <td>
                  @if($a->estado_entrega == 'Pendiente')
                    <span class="badge bg-warning text-dark">Pendiente</span>
                  @elseif($a->estado_entrega == 'Entregado')
                    <span class="badge bg-success">Entregado</span>
                  @elseif($a->estado_entrega == 'Parcial')
                    <span class="badge bg-info">Parcial</span>
                  @else
                    <span class="badge bg-secondary">{{ $a->estado_entrega }}</span>
                  @endif
                </td>
                <td>{{ $a->fecha_entrega ? \Carbon\Carbon::parse($a->fecha_entrega)->format('d/m/Y') : 'Pendiente' }}</td>
                <td>
                  <a href="{{ route('adquisiciones.ver', $a->id_adquisicion) }}" class="btn btn-sm btn-outline-primary" title="Ver detalle">
                    <i class="bi bi-eye"></i>
                  </a>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="mt-3 text-muted">No hay adquisiciones registradas</p>
      </div>
    @endif
  </div>
</div>
@endsection
