<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PerfilController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        return view('perfil.index', compact('usuario'));
    }

    public function update(Request $request)
    {
        $usuario = Auth::user();
        
        $validated = $request->validate([
            'nombre' => 'required|max:255',
            'apellido' => 'required|max:255',
            'correo' => 'required|email|unique:usuario,correo,'.$usuario->id_usuario.',id_usuario',
            'telefono' => 'nullable',
            'contrasena_actual' => 'required_with:contrasena_nueva',
            'contrasena_nueva' => 'nullable|min:6|confirmed',
        ]);

        // Actualizar datos básicos
        $usuario->nombre = $validated['nombre'];
        $usuario->apellido = $validated['apellido'];
        $usuario->correo = $validated['correo'];
        $usuario->telefono = $validated['telefono'];

        // Actualizar contraseña si se proporcionó
        if (!empty($validated['contrasena_actual'])) {
            if (!Hash::check($validated['contrasena_actual'], $usuario->contrasena)) {
                return back()->withErrors([
                    'contrasena_actual' => 'La contraseña actual no es correcta.'
                ]);
            }
            
            $usuario->contrasena = Hash::make($validated['contrasena_nueva']);
        }

        $usuario->save();

        return redirect()->route('perfil.index')
            ->with('success', 'Perfil actualizado exitosamente.');
    }
}