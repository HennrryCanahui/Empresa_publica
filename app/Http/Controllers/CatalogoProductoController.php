<?php

namespace App\Http\Controllers;

use App\Models\Catalogo_producto;
use App\Models\Categoria_producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CatalogoProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Catalogo_producto::with('categoria');

        // Filtro por categoría
        if ($request->filled('id_categoria')) {
            $query->where('id_categoria', $request->id_categoria);
        }

        // Filtro por estado activo
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        // Búsqueda por nombre, código o descripción
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%")
                  ->orWhere('descripcion', 'like', "%{$buscar}%");
            });
        }

        $productos = $query->orderBy('activo', 'desc')
                          ->orderBy('nombre')
                          ->paginate(15);

        $categorias = Categoria_producto::where('activo', true)
                                       ->orderBy('nombre')
                                       ->get();

        $tipos = ['Bien', 'Servicio', 'Activo'];

        return view('admin.productos.index', compact('productos', 'categorias', 'tipos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria_producto::where('activo', true)
                                       ->orderBy('nombre')
                                       ->get();

        $tipos = ['Bien', 'Servicio', 'Activo'];

        return view('admin.productos.create', compact('categorias', 'tipos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:CATALOGO_PRODUCTO,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|in:Bien,Servicio,Activo',
            'id_categoria' => 'required|exists:CATEGORIA_PRODUCTO,id_categoria',
            'unidad_medida' => 'required|string|max:50',
            'precio_referencia' => 'nullable|numeric|min:0',
            'especificaciones_tecnicas' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Obtener el siguiente ID
            $nextId = DB::selectOne("SELECT NVL(MAX(id_producto), 0) + 1 as next_id FROM CATALOGO_PRODUCTO")->next_id;

            $producto = new Catalogo_producto();
            $producto->id_producto = $nextId;
            $producto->codigo = strtoupper($validated['codigo']);
            $producto->nombre = $validated['nombre'];
            $producto->descripcion = $validated['descripcion'] ?? null;
            $producto->tipo = $validated['tipo'];
            $producto->id_categoria = $validated['id_categoria'];
            $producto->unidad_medida = $validated['unidad_medida'];
            $producto->precio_referencia = $validated['precio_referencia'] ?? null;
            $producto->especificaciones_tecnicas = $validated['especificaciones_tecnicas'] ?? null;
            $producto->activo = $request->has('activo') ? true : false;
            $producto->save();

            DB::commit();

            return redirect()->route('admin.productos.index')
                           ->with('success', 'Producto creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al crear el producto: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Catalogo_producto $producto)
    {
        $producto->load('categoria', 'detallesCotizacion');
        return view('admin.productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Catalogo_producto $producto)
    {
        $categorias = Categoria_producto::where('activo', true)
                                       ->orderBy('nombre')
                                       ->get();

        $tipos = ['Bien', 'Servicio', 'Activo'];

        return view('admin.productos.edit', compact('producto', 'categorias', 'tipos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Catalogo_producto $producto)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:CATALOGO_PRODUCTO,codigo,' . $producto->id_producto . ',id_producto',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|in:Bien,Servicio,Activo',
            'id_categoria' => 'required|exists:CATEGORIA_PRODUCTO,id_categoria',
            'unidad_medida' => 'required|string|max:50',
            'precio_referencia' => 'nullable|numeric|min:0',
            'especificaciones_tecnicas' => 'nullable|string',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $producto->codigo = strtoupper($validated['codigo']);
            $producto->nombre = $validated['nombre'];
            $producto->descripcion = $validated['descripcion'] ?? null;
            $producto->tipo = $validated['tipo'];
            $producto->id_categoria = $validated['id_categoria'];
            $producto->unidad_medida = $validated['unidad_medida'];
            $producto->precio_referencia = $validated['precio_referencia'] ?? null;
            $producto->especificaciones_tecnicas = $validated['especificaciones_tecnicas'] ?? null;
            $producto->activo = $request->has('activo') ? true : false;
            $producto->save();

            DB::commit();

            return redirect()->route('admin.productos.index')
                           ->with('success', 'Producto actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al actualizar el producto: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Catalogo_producto $producto)
    {
        try {
            // No eliminar físicamente, solo desactivar
            $producto->activo = false;
            $producto->save();

            return redirect()->route('admin.productos.index')
                           ->with('success', 'Producto desactivado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar el producto: ' . $e->getMessage());
        }
    }
}

