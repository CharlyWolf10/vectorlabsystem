@extends('pdf.layout')

@section('title', $title)

@section('content')
    <table class="data-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Proveedor</th>
                <th class="text-right">Costo</th>
                <th class="text-center">Stock</th>
            </tr>
        </thead>
        <tbody>
            @foreach($productos as $producto)
            <tr>
                <td>{{ $producto->codigo }}</td>
                <td><strong class="font-bold">{{ $producto->nombre }}</strong></td>
                <td>{{ $producto->proveedor ? $producto->proveedor->nombre : 'N/A' }}</td>
                <td class="text-right text-red">${{ number_format($producto->precio_compra, 2) }}</td>
                <td class="text-center">{{ $producto->stock }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
