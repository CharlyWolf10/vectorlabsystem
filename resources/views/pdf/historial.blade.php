<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #0056b3; padding-bottom: 10px; }
        .logo { max-width: 150px; max-height: 80px; }
        .title { font-size: 20px; font-weight: bold; color: #0056b3; margin: 0; text-align: center; margin-top: 15px; }
        .date { text-align: right; margin-bottom: 15px; font-weight: bold; }
        .info-table { width: 100%; border: none; margin: 0; padding: 0; }
        .info-table td { border: none; padding: 0; }
        .info-box { background-color: #f1f5f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background-color: #0056b3; color: white; padding: 8px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { position: absolute; bottom: 30px; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { display: inline-block; padding: 3px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; color: #fff; }
        .badge-creado { background-color: #10b981; }
        .badge-editado { background-color: #3b82f6; }
        .badge-ingreso_stock { background-color: #8b5cf6; }
        .badge-default { background-color: #6b7280; }
        ul { margin: 0; padding-left: 15px; }
    </style>
</head>
<body>
    <div class="header">
        <table class="info-table">
            <tr>
                <td style="width: 30%; text-align: left; vertical-align: top;">
                    <img src="{{ $logo }}" class="logo" alt="Logo">
                </td>
                <td style="width: 70%; text-align: right; vertical-align: top; line-height: 1.3;">
                    @if(isset($perfil) && $perfil)
                        <h2 style="margin: 0; color: #0056b3; font-size: 18px;">{{ $perfil->nombre }}</h2>
                        @if($perfil->rfc)<div style="font-size: 11px;"><strong>RFC:</strong> {{ $perfil->rfc }}</div>@endif
                        @if($perfil->direccion)<div style="font-size: 11px;">{{ $perfil->direccion }}</div>@endif
                        @if($perfil->telefono || $perfil->correo)
                            <div style="font-size: 11px;">
                                @if($perfil->telefono) Tel: {{ $perfil->telefono }} @endif
                                @if($perfil->telefono && $perfil->correo) | @endif
                                @if($perfil->correo) Email: {{ $perfil->correo }} @endif
                            </div>
                        @endif
                        @if($perfil->sitio_web)<div style="font-size: 11px;">Web: {{ $perfil->sitio_web }}</div>@endif
                    @else
                        <h2 style="margin: 0; color: #0056b3; font-size: 18px;">Vector Lab</h2>
                    @endif
                </td>
            </tr>
        </table>
        <h1 class="title">{{ $title }}</h1>
    </div>
    
    <div class="date">Fecha de Emisión: {{ $date }}</div>

    <div class="info-box">
        <table style="width: 100%; margin: 0;">
            <tr>
                <td style="border: none; width: 50%;">
                    <strong>Código/SKU:</strong> {{ $producto->codigo }}<br>
                    <strong>Producto:</strong> {{ $producto->nombre }}<br>
                    <strong>Categoría:</strong> {{ $producto->categoria ?? 'N/A' }}
                </td>
                <td style="border: none; width: 50%;">
                    <strong>Stock Actual:</strong> {{ $producto->stock }}<br>
                    <strong>Costo Neto:</strong> ${{ number_format($producto->precio_compra, 2) }}
                    @if($producto->aplica_iva) (+16% IVA) @endif <br>
                    <strong>Proveedor:</strong> {{ $producto->proveedor ? $producto->proveedor->nombre : 'N/A' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="subtitle">Auditoría e Historial de Eventos</div>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Fecha y Hora</th>
                <th style="width: 15%;">Usuario</th>
                <th style="width: 15%;">Acción</th>
                <th style="width: 55%;">Detalles</th>
            </tr>
        </thead>
        <tbody>
            @forelse($historial as $evento)
            <tr>
                <td>{{ $evento->created_at->format('d/m/Y H:i:s') }}</td>
                <td>{{ $evento->user ? $evento->user->name : 'Sistema' }}</td>
                <td>
                    @php
                        $badgeClass = 'badge-default';
                        if ($evento->accion === 'CREADO') $badgeClass = 'badge-creado';
                        if ($evento->accion === 'EDITADO') $badgeClass = 'badge-editado';
                        if ($evento->accion === 'INGRESO_STOCK') $badgeClass = 'badge-ingreso_stock';
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $evento->accion }}</span>
                </td>
                <td>
                    @if(is_array($evento->detalles) && count($evento->detalles) > 0)
                        <ul>
                        @foreach($evento->detalles as $detalle)
                            <li>{{ $detalle }}</li>
                        @endforeach
                        </ul>
                    @else
                        No hay detalles adicionales.
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No hay historial disponible para este producto.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema Vector Lab.
    </div>
</body>
</html>
