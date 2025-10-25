@extends('layouts.app')

@section('header')
<h2 class="h4 mb-0"><i class="bi bi-cart-check me-2"></i>Gestión de Adquisiciones</h2>
@endsection

@section('content')
<div class="card">
  <div class="card-body">
    @if($solicitudes->count())
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Solicitud</th>
              <th>Descripción</th>
              <th>Cotización seleccionada</th>
              <th>Monto</th>
              <th>Estado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach($solicitudes as $s)
              @php
                $cotizacion = $s->cotizaciones->where('seleccionada', true)->first();
                $adquisicion = $s->adquisicion;
              @endphp
              <tr>
                <td>
                  <strong>{{ $s->numero_solicitud }}</strong><br>
                  <small class="text-muted">{{ $s->fecha_solicitud ? \Carbon\Carbon::parse($s->fecha_solicitud)->format('d/m/Y') : '' }}</small>
                </td>
                <td>
                  <div class="mb-1">{{ Str::limit($s->descripcion, 50) }}</div>
                  <small class="text-muted">{{ $s->solicitante->name ?? '' }} - {{ $s->unidad->nombre ?? '' }}</small>
                </td>
                <td>
                  @if($cotizacion)
                    <strong>{{ $cotizacion->proveedor->razon_social ?? '' }}</strong><br>
                    <small class="text-muted">{{ $cotizacion->proveedor->nit_rfc ?? '' }}</small>
                  @else
                    <span class="text-muted">Sin cotización</span>
                  @endif
                </td>
                <td class="fw-bold">Q {{ number_format($cotizacion->monto_total ?? 0, 2) }}</td>
                <td>
                  @if($s->estado == 'Aprobada')
                    <span class="badge bg-success">Aprobada</span>
                  @elseif($s->estado == 'Orden_Compra_Generada')
                    <span class="badge bg-info">OC Generada</span>
                  @elseif($s->estado == 'Completada')
                    <span class="badge bg-dark">Completada</span>
                  @else
                    <span class="badge bg-secondary">{{ $s->estado }}</span>
                  @endif
                </td>
                <td>
                  @if(!$adquisicion && $cotizacion)
                    <a href="{{ route('adquisiciones.create', $s->id_solicitud) }}" class="btn btn-sm btn-primary" title="Generar Orden de Compra">
                      <i class="bi bi-file-earmark-plus"></i> Generar OC
                    </a>
                  @elseif($adquisicion)
                    <a href="{{ route('adquisiciones.ver', $adquisicion->id_adquisicion) }}" class="btn btn-sm btn-outline-info" title="Ver detalle">
                      <i class="bi bi-eye"></i>
                    </a>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center py-5">
        <i class="bi bi-inbox display-1 text-muted"></i>
        <p class="mt-3 text-muted">No hay solicitudes aprobadas pendientes de adquisición</p>
      </div>
    @endif
  </div>
</div>
@endsection
