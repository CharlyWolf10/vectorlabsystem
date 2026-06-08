@extends('pdf.layout')

@section('title', $title)

@section('content')
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Dirección</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clientes as $cliente)
            <tr>
                <td><strong class="font-bold">{{ $cliente->nombre }} {{ $cliente->apellidos }}</strong></td>
                <td>{{ $cliente->telefono }}</td>
                <td>{{ $cliente->email }}</td>
                <td>{{ $cliente->direccion }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
