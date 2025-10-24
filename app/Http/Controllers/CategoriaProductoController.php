<?php

namespace App\Http\Controllers;

use App\Models\Categoria_producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Categoria_producto::withCount('productos');

        // Filtro por estado activo
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        // Búsqueda por nombre o código
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $categorias = $query->orderBy('activo', 'desc')
                           ->orderBy('nombre')
                           ->paginate(15);

        return view('admin.categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:CATEGORIA_PRODUCTO,codigo',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Obtener el siguiente ID
            $nextId = DB::selectOne("SELECT NVL(MAX(id_categoria), 0) + 1 as next_id FROM CATEGORIA_PRODUCTO")->next_id;

            $categoria = new Categoria_producto();
            $categoria->id_categoria = $nextId;
            $categoria->codigo = strtoupper($validated['codigo']);
            $categoria->nombre = $validated['nombre'];
            $categoria->descripcion = $validated['descripcion'] ?? null;
            $categoria->activo = $request->has('activo') ? true : false;
            $categoria->save();

            DB::commit();

            return redirect()->route('admin.categorias.index')
                           ->with('success', 'Categoría creada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al crear la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria_producto $categoria)
    {
        $categoria->load('productos');
        return view('admin.categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria_producto $categoria)
    {
        return view('admin.categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria_producto $categoria)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:20|unique:CATEGORIA_PRODUCTO,codigo,' . $categoria->id_categoria . ',id_categoria',
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $categoria->codigo = strtoupper($validated['codigo']);
            $categoria->nombre = $validated['nombre'];
            $categoria->descripcion = $validated['descripcion'] ?? null;
            $categoria->activo = $request->has('activo') ? true : false;
            $categoria->save();

            DB::commit();

            return redirect()->route('admin.categorias.index')
                           ->with('success', 'Categoría actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al actualizar la categoría: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria_producto $categoria)
    {
        try {
            // Verificar si tiene productos asociados
            if ($categoria->productos()->count() > 0) {
                return back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados. Desactívela en su lugar.');
            }

            // No eliminar físicamente, solo desactivar
            $categoria->activo = false;
            $categoria->save();

            return redirect()->route('admin.categorias.index')
                           ->with('success', 'Categoría desactivada exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar la categoría: ' . $e->getMessage());
        }
    }
}

