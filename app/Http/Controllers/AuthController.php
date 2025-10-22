<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = $request->filled('remember');

        // Map form 'email' to DB column 'correo'. Pass plain password as 'password'
        // so Auth::attempt will perform the hash check via the Usuario model.
        if (Auth::attempt([
            'correo' => $credentials['email'],
            'password' => $credentials['password'],
            'activo' => 1,
        ], $remember)) {
            $request->session()->regenerate();

            // Actualizar último acceso
            $usuario = Auth::user();
            $usuario->ultimo_acceso = now();
            $usuario->save();

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
