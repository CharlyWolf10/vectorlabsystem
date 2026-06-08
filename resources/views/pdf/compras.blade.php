@extends('pdf.layout')

@section('title', $title)

@section('content')
    <h1 class="report-title">{{ $title }}</h1>

    <table class="data-table">
        <thead>
            <tr>
                <th>Empresa</th>
                <th>Contacto</th>
                <th>Teléfono</th>
                <th>Email</th>
            </tr>
        </thead>
        <tbody>
            @foreach($proveedores as $proveedor)
            <tr>
                <td><strong class="font-bold">{{ $proveedor->nombre }}</strong></td>
                <td>{{ $proveedor->contacto }}</td>
                <td>{{ $proveedor->telefono }}</td>
                <td>{{ $proveedor->email }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endsection
