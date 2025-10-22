<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ProveedorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Proveedor::query();

        if ($request->has('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('razon_social', 'LIKE', "%{$search}%")
                  ->orWhere('nit', 'LIKE', "%{$search}%")
                  ->orWhere('codigo', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('estado')) {
            $query->where('activo', $request->estado == 'activo');
        }

        $proveedores = $query->orderBy('razon_social')->paginate(10);
        return view('proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('proveedores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:proveedor,codigo',
            'razon_social' => 'required|max:255',
            'nit' => 'required|unique:proveedor,nit',
            'direccion' => 'required',
            'telefono' => 'required',
            'correo' => 'required|email',
            'contacto_principal' => 'required'
        ]);

        $validated['activo'] = true;
        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'codigo' => 'required|unique:proveedor,codigo,'.$proveedor->id_proveedor.',id_proveedor',
            'razon_social' => 'required|max:255',
            'nit' => 'required|unique:proveedor,nit,'.$proveedor->id_proveedor.',id_proveedor',
            'direccion' => 'required',
            'telefono' => 'required',
            'correo' => 'required|email',
            'contacto_principal' => 'required'
        ]);

        $proveedor->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function toggleStatus(Proveedor $proveedor)
    {
        $proveedor->activo = !$proveedor->activo;
        $proveedor->save();

        return redirect()->route('proveedores.index')
            ->with('success', 'Estado del proveedor actualizado exitosamente.');
    }
}