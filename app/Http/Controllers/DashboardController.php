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
                return view('dashboard.admin');
            case 'presupuesto':
                return view('dashboard.presupuesto');
            case 'compras':
                return view('dashboard.compras');
            case 'autoridad':
                return view('dashboard.autoridad');
            case 'solicitante':
                return view('dashboard.solicitante');
            default:
                // Si rol desconocido, mostrar una vista genérica o layout con mensaje
                return view('errors.404');
        }
    }
}
