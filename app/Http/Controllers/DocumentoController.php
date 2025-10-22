<?php

namespace App\Http\Controllers;

use App\Models\DocumentoAdjunto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentoController extends Controller
{
    public function index(Request $request)
    {
        $query = DocumentoAdjunto::with(['solicitud', 'usuarioCarga']);

        if ($request->has('buscar')) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('nombre_archivo', 'LIKE', "%{$search}%")
                  ->orWhere('tipo_documento', 'LIKE', "%{$search}%");
            });
        }

        if ($request->has('tipo')) {
            $query->where('tipo_documento', $request->tipo);
        }

        $documentos = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('documentos.index', compact('documentos'));
    }

    public function show(DocumentoAdjunto $documento)
    {
        // Verificar permisos de acceso
        if (!Auth::user()->can('ver', $documento)) {
            abort(403);
        }

        // Obtener la ruta del archivo
        $path = storage_path('app/' . $documento->ruta_archivo);

        if (!Storage::exists($documento->ruta_archivo)) {
            abort(404);
        }

        // Retornar el archivo
        return response()->file($path);
    }

    public function download(DocumentoAdjunto $documento)
    {
        // Verificar permisos de acceso
        if (!Auth::user()->can('descargar', $documento)) {
            abort(403);
        }

        // Verificar que el archivo existe
        if (!Storage::exists($documento->ruta_archivo)) {
            abort(404);
        }

        // Descargar el archivo
        return Storage::download($documento->ruta_archivo, $documento->nombre_archivo);
    }
}