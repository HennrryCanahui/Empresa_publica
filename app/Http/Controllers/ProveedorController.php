<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proveedor::query();

        // Filtro por estado activo
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        // Búsqueda por razón social, NIT o código
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->whereRaw('UPPER(razon_social) LIKE ?', ['%' . strtoupper($buscar) . '%'])
                  ->orWhereRaw('UPPER(nit) LIKE ?', ['%' . strtoupper($buscar) . '%'])
                  ->orWhereRaw('UPPER(codigo) LIKE ?', ['%' . strtoupper($buscar) . '%']);
            });
        }

        $proveedores = $query->orderBy('activo', 'desc')
                            ->orderBy('razon_social')
                            ->paginate(15);

        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('PROVEEDOR', 'CODIGO')
            ],
            'razon_social' => 'required|string|max:200',
            'nit_rfc' => [
                'required',
                'string',
                'max:20',
                Rule::unique('PROVEEDOR', 'NIT')
            ],
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'contacto_principal' => 'nullable|string|max:150',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Obtener el siguiente ID
            $nextId = DB::selectOne("SELECT NVL(MAX(id_proveedor), 0) + 1 as next_id FROM PROVEEDOR")->next_id;

            $proveedor = new Proveedor();
            $proveedor->id_proveedor = $nextId;
            $proveedor->codigo = strtoupper($validated['codigo']);
            $proveedor->razon_social = $validated['razon_social'];
            $proveedor->nit_rfc = $validated['nit_rfc'];
            $proveedor->direccion = $validated['direccion'] ?? null;
            $proveedor->telefono = $validated['telefono'] ?? null;
            $proveedor->correo = $validated['correo'] ?? null;
            $proveedor->contacto_principal = $validated['contacto_principal'] ?? null;
            $proveedor->activo = $request->has('activo') ? 1 : 1; // Por defecto activo
            
            // Debug: ver qué se va a guardar
            \Log::info('Guardando proveedor:', $proveedor->toArray());
            
            $proveedor->save();

            DB::commit();

            return redirect()->route('proveedores.index')
                           ->with('success', 'Proveedor creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al crear proveedor: ' . $e->getMessage());
            return back()->withInput()
                       ->with('error', 'Error al crear el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedor)
    {
        $proveedor->load('cotizaciones');
        return view('proveedores.show', compact('proveedor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedor)
    {
        return view('proveedores.edit', compact('proveedor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'codigo' => [
                'required',
                'string',
                'max:20',
                Rule::unique('PROVEEDOR', 'CODIGO')->ignore($proveedor->id_proveedor, 'ID_PROVEEDOR')
            ],
            'razon_social' => 'required|string|max:200',
            'nit_rfc' => [
                'required',
                'string',
                'max:20',
                Rule::unique('PROVEEDOR', 'NIT')->ignore($proveedor->id_proveedor, 'ID_PROVEEDOR')
            ],
            'direccion' => 'nullable|string|max:500',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'contacto_principal' => 'nullable|string|max:150',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            \Log::info('Actualizando proveedor ID: ' . $proveedor->id_proveedor);
            \Log::info('Datos validados:', $validated);
            
            $proveedor->codigo = strtoupper($validated['codigo']);
            $proveedor->razon_social = $validated['razon_social'];
            $proveedor->nit_rfc = $validated['nit_rfc'];
            $proveedor->direccion = $validated['direccion'] ?? null;
            $proveedor->telefono = $validated['telefono'] ?? null;
            $proveedor->correo = $validated['correo'] ?? null;
            $proveedor->contacto_principal = $validated['contacto_principal'] ?? null;
            $proveedor->activo = $request->has('activo') ? 1 : 0;
            
            \Log::info('Proveedor antes de guardar:', $proveedor->toArray());
            
            $proveedor->save();

            DB::commit();

            return redirect()->route('proveedores.index')
                           ->with('success', 'Proveedor actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error al actualizar proveedor: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->withInput()
                       ->with('error', 'Error al actualizar el proveedor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedor)
    {
        try {
            // Verificar si tiene cotizaciones asociadas
            if ($proveedor->cotizaciones()->count() > 0) {
                return back()->with('error', 'No se puede eliminar el proveedor porque tiene cotizaciones asociadas. Desactívelo en su lugar.');
            }

            // No eliminar físicamente, solo desactivar
            $proveedor->activo = false;
            $proveedor->save();

            return redirect()->route('proveedores.index')
                           ->with('success', 'Proveedor desactivado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar el proveedor: ' . $e->getMessage());
        }
    }
}
