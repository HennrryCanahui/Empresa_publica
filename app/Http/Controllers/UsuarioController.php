<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Unidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with('unidad');

        if ($request->has('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('apellido', 'LIKE', "%{$search}%")
                  ->orWhere('correo', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('rol')) {
            $query->where('rol', $request->rol);
        }

        if ($request->has('estado')) {
            $query->where('activo', $request->estado == 'activo');
        }

        $usuarios = $query->orderBy('nombre')->paginate(10);
        $unidades = Unidad::where('activo', true)->orderBy('nombre')->get();
        
        return view('usuarios.index', compact('usuarios', 'unidades'));
    }

    public function create()
    {
        $unidades = Unidad::where('activo', true)->orderBy('nombre')->get();
        return view('usuarios.create', compact('unidades'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'correo' => 'required|email|unique:usuario,correo',
            'contrasena' => 'required|min:6',
            'rol' => 'required',
            'id_unidad' => 'required|exists:unidad,id_unidad',
            'telefono' => 'nullable'
        ]);

        $validated['contrasena'] = Hash::make($validated['contrasena']);
        $validated['activo'] = true;
        Usuario::create($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario creado exitosamente.');
    }

    public function edit(Usuario $usuario)
    {
        $unidades = Unidad::where('activo', true)->orderBy('nombre')->get();
        return view('usuarios.edit', compact('usuario', 'unidades'));
    }

    public function update(Request $request, Usuario $usuario)
    {
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'correo' => 'required|email|unique:usuario,correo,'.$usuario->id_usuario.',id_usuario',
            'rol' => 'required',
            'id_unidad' => 'required|exists:unidad,id_unidad',
            'telefono' => 'nullable',
            'contrasena' => 'nullable|min:6'
        ]);

        if (!empty($validated['contrasena'])) {
            $validated['contrasena'] = Hash::make($validated['contrasena']);
        } else {
            unset($validated['contrasena']);
        }

        $usuario->update($validated);

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado exitosamente.');
    }

    public function toggleStatus(Usuario $usuario)
    {
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        return redirect()->route('usuarios.index')
            ->with('success', 'Estado del usuario actualizado exitosamente.');
    }
}