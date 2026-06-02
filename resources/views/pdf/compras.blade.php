<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cuentas por Pagar</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total { font-weight: bold; }
    </style>
</head>
<body>
    <h2>Reporte de Cuentas por Pagar</h2>
    <p>Fecha de generación: {{ now()->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Proveedor</th>
                <th>Fecha Compra</th>
                <th>Estado</th>
                <th class="text-right">Monto Total</th>
                <th class="text-right">Saldo Pendiente</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $granTotal = 0; 
                $granPendiente = 0; 
            @endphp
            @foreach($cuentas as $cuenta)
                @php 
                    $granTotal += $cuenta->monto_total; 
                    $granPendiente += $cuenta->saldo_pendiente; 
                @endphp
                <tr>
                    <td>{{ $cuenta->id }}</td>
                    <td>{{ $cuenta->proveedor->nombre }}</td>
                    <td>{{ $cuenta->compra->fecha }}</td>
                    <td>{{ ucfirst($cuenta->estado) }}</td>
                    <td class="text-right">${{ number_format($cuenta->monto_total, 2) }}</td>
                    <td class="text-right">${{ number_format($cuenta->saldo_pendiente, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="4" class="text-right">TOTALES</td>
                <td class="text-right">${{ number_format($granTotal, 2) }}</td>
                <td class="text-right">${{ number_format($granPendiente, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
