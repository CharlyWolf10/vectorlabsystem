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
    public $selectAll = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
        $this->loadData();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProveedores = Proveedor::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedProveedores = [];
        }
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
        $id = $data['id'] ?? null;
        Proveedor::updateOrCreate(
            ['id' => $id],
            [
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'],
                'email' => $data['email'],
                'direccion' => $data['direccion'] ?? null,
                'rfc' => $data['rfc'] ?? null,
                'banco' => $data['banco'],
                'clabe' => $data['clabe'],
                'num_cuenta' => $data['num_cuenta'],
                'titular_cuenta' => $data['titular_cuenta'] ?? null,
            ]
        );
        $this->loadData();
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Proveedor guardado correctamente.']);
    }

    #[On('eliminarProveedor')]
    public function eliminarProveedor($id)
    {
        $proveedor = Proveedor::find($id);
        if ($proveedor) {
            $proveedor->delete();
            $this->loadData();
            $this->dispatch('swal:success', ['title' => '¡Eliminado!', 'text' => 'Proveedor eliminado de la base de datos.']);
        }
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
    public function aplicarAbono($cuentaId, $montoAbono)
    {
        $cuenta = CuentaPorPagar::find($cuentaId);
        if ($cuenta && $montoAbono > 0 && $montoAbono <= $cuenta->saldo_pendiente) {
            $cuenta->saldo_pendiente -= $montoAbono;
            
            if ($cuenta->saldo_pendiente <= 0) {
                $cuenta->estado = 'pagada';
            }
            
            $cuenta->save();
            $this->dispatch('swal:success', ['title' => '¡Abono registrado!', 'text' => 'El saldo de la deuda se ha actualizado correctamente.']);
        }
        
        $this->loadData();
    }

    #[On('eliminarCuenta')]
    public function eliminarCuenta($id)
    {
        $cuenta = CuentaPorPagar::find($id);
        if ($cuenta) {
            $cuenta->delete();
            $this->dispatch('swal:success', ['title' => '¡Eliminada!', 'text' => 'La cuenta por pagar ha sido eliminada.']);
        }
    }

    public function exportSelected()
    {
        if (empty($this->selectedProveedores)) {
            $this->dispatch('swal:error', ['title' => 'Atención', 'text' => 'Debes seleccionar al menos un proveedor para exportar.']);
            return;
        }

        $ids = implode(',', $this->selectedProveedores);
        return redirect()->route('compras.export', ['ids' => $ids]);
    }
}
