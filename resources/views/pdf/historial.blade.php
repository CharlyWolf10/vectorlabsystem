@extends('pdf.layout')

@section('title', $title)

@section('styles')
<style>
    .subtitle { font-size: 14px; font-weight: bold; margin-bottom: 5px; color: #0056b3; }
    .info-box { background-color: #f1f5f9; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #0056b3; }
    .badge { display: inline-block; padding: 3px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; color: #fff; }
    .badge-creado { background-color: #10b981; }
    .badge-editado { background-color: #3b82f6; }
    .badge-ingreso_stock { background-color: #8b5cf6; }
    .badge-default { background-color: #6b7280; }
    ul { margin: 0; padding-left: 15px; }
</style>
@endsection

@section('content')
    <h1 class="report-title">{{ $title }}</h1>

    <div class="info-box">
        <table style="width: 100%; margin: 0; border: none;">
            <tr>
                <td style="border: none; width: 50%; padding: 0 10px 0 0;">
                    <strong>Código/SKU:</strong> {{ $producto->codigo }}<br>
                    <strong>Producto:</strong> {{ $producto->nombre }}<br>
                    <strong>Categoría:</strong> {{ $producto->categoria ?? 'N/A' }}
                </td>
                <td style="border: none; width: 50%; padding: 0;">
                    <strong>Stock Actual:</strong> {{ $producto->stock }}<br>
                    <strong>Costo Neto:</strong> ${{ number_format($producto->precio_compra, 2) }}
                    @if($producto->aplica_iva) (+16% IVA) @endif <br>
                    <strong>Proveedor:</strong> {{ $producto->proveedor ? $producto->proveedor->nombre : 'N/A' }}
                </td>
            </tr>
        </table>
    </div>

    <div class="subtitle">Auditoría e Historial de Eventos</div>
    <table class="data-table">
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
@endsection
