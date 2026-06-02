<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #0056b3; padding-bottom: 10px; }
        .logo { max-width: 150px; margin-bottom: 10px; }
        .title { font-size: 20px; font-weight: bold; color: #0056b3; margin: 0; }
        .date { text-align: right; margin-bottom: 20px; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #0056b3; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .footer { position: absolute; bottom: 30px; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ $logo }}" class="logo" alt="Vector Lab">
        <h1 class="title">{{ $title }}</h1>
    </div>
    
    <div class="date">Fecha de Emisión: {{ $date }}</div>

    <table>
        <thead>
            <tr>
                <th>Cliente</th>
                <th>Tipo</th>
                <th>Contacto</th>
                <th class="text-right">Límite Crédito</th>
                <th class="text-right">Saldo Deudor</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
            <tr>
                <td><strong>{{ $cliente->nombre }} {{ $cliente->apellidos }}</strong></td>
                <td>
                    @if($cliente->es_estudiante)
                        Estudiante ({{ $cliente->escuela ?: 'Sin escuela' }})<br>
                        Matrícula: {{ $cliente->matricula }}
                    @else
                        Profesionista<br>
                        RFC: {{ $cliente->rfc ?: 'N/A' }}
                    @endif
                </td>
                <td>{{ $cliente->telefono }}<br>{{ $cliente->email }}</td>
                <td class="text-right">${{ number_format($cliente->limite_credito, 2) }}</td>
                <td class="text-right" style="color: red; font-weight: bold;">${{ number_format($cliente->saldo_pendiente, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema Vector Lab.
    </div>
</body>
</html>
