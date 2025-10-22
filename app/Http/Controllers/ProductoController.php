<?php

namespace App\Http\Controllers;

use App\Models\CatalogoProducto;
use App\Models\CategoriaProducto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class ProductoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!in_array(Auth::user()->rol, ['Compras', 'Admin'])) {
                return redirect()->route('dashboard')
                    ->with('error', 'No tiene permisos para acceder a esta sección.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        $query = CatalogoProducto::query();
        
        // Filtros
        if ($request->filled('categoria')) {
            $query->where('id_categoria', $request->categoria);
        }
        
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        
        if ($request->filled('buscar')) {
            $query->where(function($q) use ($request) {
                $q->where('nombre', 'like', "%{$request->buscar}%")
                  ->orWhere('codigo', 'like', "%{$request->buscar}%");
            });
        }

        $productos = $query->with('categoria')
            ->orderBy('nombre')
            ->paginate(10);
            
        $categorias = CategoriaProducto::where('activo', 1)->get();
        $tipos = CatalogoProducto::TIPOS;

        return view('productos.index', compact('productos', 'categorias', 'tipos'));
    }

    public function create()
    {
        $categorias = CategoriaProducto::where('activo', 1)->get();
        $tipos = CatalogoProducto::TIPOS;
        return view('productos.create', compact('categorias', 'tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:catalogo_producto,codigo',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:4000',
            'tipo' => 'required|in:' . implode(',', array_keys(CatalogoProducto::TIPOS)),
            'id_categoria' => 'required|exists:categoria_producto,id_categoria',
            'unidad_medida' => 'required|string|max:50',
            'precio_referencia' => 'nullable|numeric|min:0',
            'especificaciones_tecnicas' => 'nullable|string|max:4000'
        ]);

        $producto = CatalogoProducto::create($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(CatalogoProducto $producto)
    {
        $categorias = CategoriaProducto::where('activo', 1)->get();
        $tipos = CatalogoProducto::TIPOS;
        return view('productos.edit', compact('producto', 'categorias', 'tipos'));
    }

    public function update(Request $request, CatalogoProducto $producto)
    {
        $request->validate([
            'codigo' => 'required|string|max:50|unique:catalogo_producto,codigo,' . $producto->id_producto . ',id_producto',
            'nombre' => 'required|string|max:200',
            'descripcion' => 'nullable|string|max:4000',
            'tipo' => 'required|in:' . implode(',', array_keys(CatalogoProducto::TIPOS)),
            'id_categoria' => 'required|exists:categoria_producto,id_categoria',
            'unidad_medida' => 'required|string|max:50',
            'precio_referencia' => 'nullable|numeric|min:0',
            'especificaciones_tecnicas' => 'nullable|string|max:4000'
        ]);

        $producto->update($request->all());

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(CatalogoProducto $producto)
    {
        // Verificar si el producto está siendo usado
        if ($producto->detallesSolicitud()->exists() || $producto->detallesCotizacion()->exists()) {
            return back()->with('error', 'No se puede eliminar el producto porque está siendo utilizado en solicitudes o cotizaciones.');
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}