<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */


    public function index(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Normalizar rol: puede almacenarse en mayúsculas/minúsculas
        $role = strtolower(trim($user->rol ?? ''));

        switch ($role) {
            case 'admin':
                // Admin no tiene dashboard propio, mostrar vista dashboard con rol Admin
                return view('dashboard');
            case 'presupuesto':
                return redirect()->route('presupuesto.index');
            case 'compras':
                return redirect()->route('compras.index');
            case 'autoridad':
                return redirect()->route('aprobacion.index');
            case 'solicitante':
                return redirect()->route('solicitudes.index');
            default:
                // Si rol desconocido, mostrar dashboard básico
                return view('dashboard');
        }
    }
}
