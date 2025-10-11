<!DOCTYPE html>
<html>
<head>
    <title>Datos de TestTable</title>
</head>
<body>
    <h1>Listado de datos</h1>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Creado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $dato)
                <tr>
                    <td>{{ $dato->id }}</td>
                    <td>{{ $dato->nombre }}</td>
                    <td>{{ $dato->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>