<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Cliente;
use Livewire\Attributes\On;

use App\Models\CuentaPorCobrar;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Clientes extends Component
{
    public $search = '';
    public $selectedClientes = [];
    public $selectAll = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedClientes = Cliente::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedClientes = [];
        }
    }

    public function render()
    {
        $clientes = Cliente::where('nombre', 'like', '%' . $this->search . '%')
                           ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                           ->orWhere('email', 'like', '%' . $this->search . '%')
                           ->orWhere('matricula', 'like', '%' . $this->search . '%')
                           ->get();

        $cuentasPorCobrar = CuentaPorCobrar::with('cliente')->where('saldo_pendiente', '>', 0)->get();

        return view('livewire.clientes', compact('clientes', 'cuentasPorCobrar'))->layout('layouts.app');
    }

    #[On('guardarCliente')]
    public function guardarCliente($data)
    {
        $id = $data['id'] ?? null;
        
        $constanciaPath = null;
        if (!empty($data['constancia_base64'])) {
            // Guardar archivo desde base64
            $base64 = $data['constancia_base64'];
            $extension = explode('/', explode(':', substr($base64, 0, strpos($base64, ';')))[1])[1];
            $replace = substr($base64, 0, strpos($base64, ',')+1);
            $file = str_replace($replace, '', $base64);
            $file = str_replace(' ', '+', $file);
            $fileName = Str::random(10) . '.' . $extension;
            Storage::disk('public')->put('constancias/' . $fileName, base64_decode($file));
            $constanciaPath = 'constancias/' . $fileName;
        }

        $clienteData = [
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'es_estudiante' => $data['es_estudiante'],
            'matricula' => $data['es_estudiante'] ? $data['matricula'] : null,
            'escuela' => $data['es_estudiante'] ? ($data['escuela'] ?? null) : null,
            'telefono' => $data['telefono'],
            'email' => $data['email'],
            'rfc' => !$data['es_estudiante'] ? ($data['rfc'] ?? null) : null,
            'limite_credito' => $data['limite_credito'] ?? 0,
            // Only set saldo_pendiente to 0 if it's a new record
            'saldo_pendiente' => $id ? Cliente::find($id)->saldo_pendiente : 0
        ];

        if ($constanciaPath) {
            $clienteData['constancia_fiscal'] = $constanciaPath;
        }

        Cliente::updateOrCreate(['id' => $id], $clienteData);
        
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Cliente guardado correctamente.']);
    }

    #[On('eliminarCliente')]
    public function eliminarCliente($id)
    {
        $cliente = Cliente::find($id);
        if ($cliente) {
            $cliente->delete();
            $this->dispatch('swal:success', ['title' => '¡Eliminado!', 'text' => 'Cliente eliminado de la base de datos.']);
        }
    }

    #[On('crearCuentaPorCobrar')]
    public function crearCuentaPorCobrar($clienteId, $monto)
    {
        $cliente = Cliente::find($clienteId);
        if ($cliente && $monto > 0) {
            CuentaPorCobrar::create([
                'cliente_id' => $clienteId,
                'monto_total' => $monto,
                'saldo_pendiente' => $monto,
                'estado' => 'pendiente'
            ]);
            $cliente->saldo_pendiente += $monto;
            $cliente->save();
            $this->dispatch('swal:success', ['title' => '¡Deuda registrada!', 'text' => 'La cuenta por cobrar se ha guardado correctamente.']);
        }
    }

    #[On('aplicarAbono')]
    public function aplicarAbono($cuentaId, $montoAbono)
    {
        $cuenta = CuentaPorCobrar::find($cuentaId);
        if ($cuenta && $montoAbono > 0 && $montoAbono <= $cuenta->saldo_pendiente) {
            $cuenta->saldo_pendiente -= $montoAbono;
            
            if ($cuenta->saldo_pendiente <= 0) {
                $cuenta->estado = 'pagada';
            }
            
            $cuenta->save();

            // Actualizar saldo global del cliente
            $cliente = $cuenta->cliente;
            $cliente->saldo_pendiente -= $montoAbono;
            $cliente->save();

            $this->dispatch('swal:success', ['title' => '¡Abono registrado!', 'text' => 'El saldo de la deuda se ha actualizado correctamente.']);
        }
    }

    #[On('eliminarCuenta')]
    public function eliminarCuenta($id)
    {
        $cuenta = CuentaPorCobrar::find($id);
        if ($cuenta) {
            $cliente = $cuenta->cliente;
            $cliente->saldo_pendiente -= $cuenta->saldo_pendiente; // Restaurar saldo global
            $cliente->save();
            
            $cuenta->delete();
            $this->dispatch('swal:success', ['title' => '¡Eliminada!', 'text' => 'La cuenta por cobrar ha sido eliminada.']);
        }
    }
}
