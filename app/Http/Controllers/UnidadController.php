<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnidadController extends Controller
{
    public function index(Request $request)
    {
        $query = Unidad::query();

        if ($request->has('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('estado')) {
            $query->where('activo', $request->estado == 'activo');
        }

        $unidades = $query->orderBy('nombre')->paginate(10);
        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:unidad,codigo',
            'nombre' => 'required|max:255',
            'tipo' => 'required',
            'descripcion' => 'nullable'
        ]);

        $validated['activo'] = true;
        Unidad::create($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad creada exitosamente.');
    }

    public function edit(Unidad $unidad)
    {
        return view('unidades.edit', compact('unidad'));
    }

    public function update(Request $request, Unidad $unidad)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:unidad,codigo,'.$unidad->id_unidad.',id_unidad',
            'nombre' => 'required|max:255',
            'tipo' => 'required',
            'descripcion' => 'nullable'
        ]);

        $unidad->update($validated);

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function toggleStatus(Unidad $unidad)
    {
        $unidad->activo = !$unidad->activo;
        $unidad->save();

        return redirect()->route('unidades.index')
            ->with('success', 'Estado de la unidad actualizado exitosamente.');
    }
}