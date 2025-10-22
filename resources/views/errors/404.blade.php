<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8 text-center">
            <div class="text-6xl mb-4">🚫</div>
            <h1 class="text-3xl font-bold text-gray-800 mb-4">Acceso Denegado</h1>
            <p class="text-gray-600 mb-6">
                {{ $message ?? 'No tienes permiso para acceder a esta sección.' }}
            </p>
            <div class="space-y-2">
                <a href="{{ route('dashboard') }}" class="block w-full bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700 transition">
                    Ir al Dashboard
                </a>
                <a href="javascript:history.back()" class="block w-full bg-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-400 transition">
                    Volver
                </a>
            </div>
        </div>
    </div>
</body>
</html>