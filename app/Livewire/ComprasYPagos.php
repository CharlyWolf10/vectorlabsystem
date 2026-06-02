<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Compra;
use App\Models\CuentaPorPagar;
use App\Models\Proveedor;

class ComprasYPagos extends Component
{
    public $proveedores;
    public $compras;
    public $cuentasPorPagar;
    public $searchProveedores = '';
    public $selectedProveedores = [];

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->loadData();
    }

    public function loadData()
    {
        $this->proveedores = Proveedor::all();
        $this->compras = Compra::with('proveedor', 'user')->orderBy('fecha', 'desc')->get();
        $this->cuentasPorPagar = CuentaPorPagar::with('proveedor')->where('estado', '!=', 'pagado')->get();
    }

    public function render()
    {
        $proveedores = Proveedor::where('nombre', 'like', '%' . $this->searchProveedores . '%')
                                ->orWhere('email', 'like', '%' . $this->searchProveedores . '%')
                                ->get();
                                
        $cuentasPorPagar = CuentaPorPagar::with('proveedor', 'compra')
                            ->where('saldo_pendiente', '>', 0)
                            ->get();

        $compras = Compra::with('proveedor', 'user')->orderBy('fecha', 'desc')->get();

        return view('livewire.compras-y-pagos', compact('proveedores', 'cuentasPorPagar', 'compras'))->layout('layouts.app');
    }

    #[On('guardarProveedor')]
    public function guardarProveedor($data)
    {
        Proveedor::create([
            'nombre' => $data['nombre'],
            'telefono' => $data['telefono'],
            'email' => $data['email'],
            'banco' => $data['banco'],
            'clabe' => $data['clabe'],
            'num_cuenta' => $data['num_cuenta'],
        ]);
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Proveedor registrado!', 'text' => 'El proveedor se ha guardado correctamente.']);
    }

    #[On('registrarGasto')]
    public function registrarGasto($proveedorId, $data)
    {
        $compra = Compra::create([
            'proveedor_id' => $proveedorId,
            'user_id' => auth()->id(),
            'concepto' => $data['concepto'],
            'monto' => $data['monto'],
            'metodo_pago' => $data['metodo'],
            'fecha' => now(),
        ]);

        if ($data['metodo'] === 'credito') {
            CuentaPorPagar::create([
                'proveedor_id' => $proveedorId,
                'compra_id' => $compra->id,
                'monto_total' => $data['monto'],
                'saldo_pendiente' => $data['monto'],
                'estado' => 'pendiente',
            ]);
        }

        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Gasto registrado!', 'text' => 'El movimiento ha sido procesado.']);
    }

    #[On('crearCuentaPorPagar')]
    public function crearCuentaPorPagar($proveedorId, $monto)
    {
        CuentaPorPagar::create([
            'proveedor_id' => $proveedorId,
            'compra_id' => null, // Deuda manual sin compra asociada directamente
            'monto_total' => $monto,
            'saldo_pendiente' => $monto,
            'estado' => 'pendiente',
        ]);
        
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Cuenta Creada!', 'text' => 'La deuda ha sido registrada exitosamente.']);
    }

    #[On('aplicarAbono')]
    public function aplicarAbono($cuentaId, $monto)
    {
        $cuenta = CuentaPorPagar::find($cuentaId);
        if ($cuenta) {
            $nuevoSaldo = $cuenta->saldo_pendiente - $monto;
            $cuenta->saldo_pendiente = $nuevoSaldo;
            $cuenta->estado = $nuevoSaldo <= 0 ? 'pagado' : 'parcial';
            $cuenta->save();
            
            // Aquí se podría registrar el pago en una tabla 'pagos' si existiera
        }
        
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Abono aplicado!', 'text' => 'Se ha descontado del saldo pendiente.']);
    }

    // Render handled above
}
