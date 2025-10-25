<?php

namespace App\Http\Controllers;

use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UnidadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Unidad::withCount('usuarios');

        // Filtro por estado activo
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        // Búsqueda por nombre
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $unidades = $query->orderBy('activo', 'desc')
                         ->orderBy('nombre')
                         ->paginate(15);

        return view('admin.unidades.index', compact('unidades'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.unidades.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:UNIDAD,nombre',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Obtener el siguiente ID
            $nextId = DB::selectOne("SELECT NVL(MAX(id_unidad), 0) + 1 as next_id FROM UNIDAD")->next_id;

            $unidad = new Unidad();
            $unidad->id_unidad = $nextId;
            $unidad->nombre = $validated['nombre'];
            $unidad->descripcion = $validated['descripcion'] ?? null;
            $unidad->activo = $request->has('activo') ? true : false;
            $unidad->save();

            DB::commit();

            return redirect()->route('admin.unidades.index')
                           ->with('success', 'Unidad creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al crear la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unidad $unidad)
    {
        $unidad->load('usuarios');
        return view('admin.unidades.show', compact('unidad'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unidad $unidad)
    {
        return view('admin.unidades.edit', compact('unidad'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unidad $unidad)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:UNIDAD,nombre,' . $unidad->id_unidad . ',id_unidad',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $unidad->nombre = $validated['nombre'];
            $unidad->descripcion = $validated['descripcion'] ?? null;
            $unidad->activo = $request->has('activo') ? true : false;
            $unidad->save();

            DB::commit();

            return redirect()->route('admin.unidades.index')
                           ->with('success', 'Unidad actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al actualizar la unidad: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unidad $unidad)
    {
        try {
            // Verificar si tiene usuarios asociados
            if ($unidad->usuarios()->count() > 0) {
                return back()->with('error', 'No se puede eliminar la unidad porque tiene usuarios asociados. Desactívela en su lugar.');
            }

            // No eliminar físicamente, solo desactivar
            $unidad->activo = false;
            $unidad->save();

            return redirect()->route('admin.unidades.index')
                           ->with('success', 'Unidad desactivada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar la unidad: ' . $e->getMessage());
        }
    }
}
