<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('unidad');

        // Filtro por rol
        if ($request->filled('rol')) {
            $query->where('rol', $request->rol);
        }

        // Filtro por estado activo
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        // Filtro por unidad
        if ($request->filled('id_unidad')) {
            $query->where('id_unidad', $request->id_unidad);
        }

        // Búsqueda por nombre o correo
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('apellido', 'like', "%{$buscar}%")
                  ->orWhere('correo', 'like', "%{$buscar}%");
            });
        }

        $usuarios = $query->orderBy('activo', 'desc')
                         ->orderBy('nombre')
                         ->paginate(15);

        $unidades = Unidad::where('activo', true)
                         ->orderBy('nombre_unidad')
                         ->get();

        $roles = ['Solicitante', 'Presupuesto', 'Compras', 'Autoridad', 'Admin'];

        return view('admin.usuarios.index', compact('usuarios', 'unidades', 'roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unidades = Unidad::where('activo', true)
                         ->orderBy('nombre_unidad')
                         ->get();

        $roles = ['Solicitante', 'Presupuesto', 'Compras', 'Autoridad', 'Admin'];

        return view('admin.usuarios.create', compact('unidades', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|max:150|unique:USUARIO,correo',
            'contrasena' => 'required|string|min:8|confirmed',
            'rol' => 'required|in:Solicitante,Presupuesto,Compras,Autoridad,Admin',
            'id_unidad' => 'required|exists:UNIDAD,id_unidad',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            // Obtener el siguiente ID
            $nextId = DB::selectOne("SELECT NVL(MAX(id_usuario), 0) + 1 as next_id FROM USUARIO")->next_id;

            $usuario = new User();
            $usuario->id_usuario = $nextId;
            $usuario->nombre = $validated['nombre'];
            $usuario->apellido = $validated['apellido'];
            $usuario->correo = $validated['correo'];
            $usuario->contrasena = Hash::make($validated['contrasena']);
            $usuario->rol = $validated['rol'];
            $usuario->id_unidad = $validated['id_unidad'];
            $usuario->telefono = $validated['telefono'] ?? null;
            $usuario->activo = $request->has('activo') ? true : false;
            $usuario->save();

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                           ->with('success', 'Usuario creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al crear el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $usuario)
    {
        $usuario->load('unidad', 'solicitudes');
        return view('admin.usuarios.show', compact('usuario'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $usuario)
    {
        $unidades = Unidad::where('activo', true)
                         ->orderBy('nombre_unidad')
                         ->get();

        $roles = ['Solicitante', 'Presupuesto', 'Compras', 'Autoridad', 'Admin'];

        return view('admin.usuarios.edit', compact('usuario', 'unidades', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => [
                'required',
                'email',
                'max:150',
                Rule::unique('USUARIO', 'correo')->ignore($usuario->id_usuario, 'id_usuario')
            ],
            'contrasena' => 'nullable|string|min:8|confirmed',
            'rol' => 'required|in:Solicitante,Presupuesto,Compras,Autoridad,Admin',
            'id_unidad' => 'required|exists:UNIDAD,id_unidad',
            'telefono' => 'nullable|string|max:20',
            'activo' => 'boolean'
        ]);

        DB::beginTransaction();
        try {
            $usuario->nombre = $validated['nombre'];
            $usuario->apellido = $validated['apellido'];
            $usuario->correo = $validated['correo'];
            
            // Solo actualizar contraseña si se proporcionó una nueva
            if ($request->filled('contrasena')) {
                $usuario->contrasena = Hash::make($validated['contrasena']);
            }
            
            $usuario->rol = $validated['rol'];
            $usuario->id_unidad = $validated['id_unidad'];
            $usuario->telefono = $validated['telefono'] ?? null;
            $usuario->activo = $request->has('activo') ? true : false;
            $usuario->save();

            DB::commit();

            return redirect()->route('admin.usuarios.index')
                           ->with('success', 'Usuario actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                       ->with('error', 'Error al actualizar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $usuario)
    {
        try {
            // No eliminar físicamente, solo desactivar
            $usuario->activo = false;
            $usuario->save();

            return redirect()->route('admin.usuarios.index')
                           ->with('success', 'Usuario desactivado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al desactivar el usuario: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the active status of a user (AJAX endpoint).
     */
    public function toggleActivo(User $usuario)
    {
        try {
            $usuario->activo = !$usuario->activo;
            $usuario->save();

            return response()->json([
                'success' => true,
                'activo' => $usuario->activo,
                'message' => $usuario->activo ? 'Usuario activado' : 'Usuario desactivado'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cambiar el estado: ' . $e->getMessage()
            ], 500);
        }
    }
}

