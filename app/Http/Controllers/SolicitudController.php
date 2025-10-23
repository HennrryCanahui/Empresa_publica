<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SolicitudController extends Controller
{
    // Solicitante flow

    public function misSolicitudes()
    {
        $user = Auth::user();
        $solicitudes = Solicitud::where('id_usuario_creador', $user->id_usuario)->orderBy('created_at', 'desc')->get();

        return view('solicitudes.index', compact('solicitudes'));
    }

    public function create()
    {
        return view('solicitudes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'monto_estimated' => ['nullable', 'numeric'],
        ]);

        $user = Auth::user();

        $sol = Solicitud::create([
            'id_usuario_creador' => $user->id_usuario,
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'monto_estimated' => $request->monto_estimated,
            'estado' => 'Pendiente',
        ]);

        return redirect()->route('solicitudes.mias')->with('status', 'Solicitud creada correctamente.');
    }

    public function show(Solicitud $solicitud)
    {
        // cualquiera autenticado puede ver, control adicional en vista
        return view('solicitudes.show', compact('solicitud'));
    }

    public function edit(Solicitud $solicitud)
    {
        $this->authorize('update', $solicitud);
        return view('solicitudes.edit', compact('solicitud'));
    }

    public function update(Request $request, Solicitud $solicitud)
    {
        $this->authorize('update', $solicitud);

        $request->validate([
            'titulo' => ['required', 'string', 'max:255'],
            'descripcion' => ['required', 'string'],
            'monto_estimated' => ['nullable', 'numeric'],
        ]);

        $solicitud->update($request->only('titulo', 'descripcion', 'monto_estimated'));

        return redirect()->route('solicitudes.mias')->with('status', 'Solicitud actualizada.');
    }

    public function reabrir(Solicitud $solicitud)
    {
        $this->authorize('reopen', $solicitud);

        $solicitud->update(['estado' => 'Pendiente']);

        return redirect()->route('solicitudes.mias')->with('status', 'Solicitud reabierta.');
    }
}
