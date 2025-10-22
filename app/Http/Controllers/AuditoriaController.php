<?php

namespace App\Http\Controllers;

use App\Models\Auditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuditoriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Auditoria::with(['usuario']);

        if ($request->has('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('tabla_afectada', 'LIKE', "%{$search}%")
                  ->orWhere('accion', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('tabla')) {
            $query->where('tabla_afectada', $request->tabla);
        }

        if ($request->has('accion')) {
            $query->where('accion', $request->accion);
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('fecha_accion', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('fecha_accion', '<=', $request->fecha_hasta);
        }

        $registros = $query->orderBy('fecha_accion', 'desc')->paginate(15);
        
        return view('auditoria.index', compact('registros'));
    }

    public function show(Auditoria $registro)
    {
        return view('auditoria.show', compact('registro'));
    }
}