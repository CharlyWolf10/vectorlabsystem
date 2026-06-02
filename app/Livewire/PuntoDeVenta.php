<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\ArqueoCaja;
use Livewire\Attributes\On;

class PuntoDeVenta extends Component
{
    public $productos;
    public $clientes;
    
    // Carrito de compras en memoria
    public $carrito = [];
    public $total = 0;

    public $search = '';
    public $cliente_id = '';

    public function mount()
    {
        $this->clientes = Cliente::all();
    }

    public function render()
    {
        $this->productos = Producto::where('nombre', 'like', '%' . $this->search . '%')
                                   ->orWhere('codigo', 'like', '%' . $this->search . '%')
                                   ->get();
        return view('livewire.punto-de-venta')->layout('layouts.app');
    }

    #[On('agregarAlCarrito')]
    public function agregarAlCarrito($productoId)
    {
        $producto = Producto::find($productoId);
        if (!$producto) return;

        $index = array_search($productoId, array_column($this->carrito, 'id'));
        if ($index !== false) {
            $this->carrito[$index]['cantidad']++;
        } else {
            $this->carrito[] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'precio' => $producto->precio_venta,
                'cantidad' => 1
            ];
        }
        $this->actualizarTotal();
        $this->dispatch('swal:toast', ['title' => $producto->nombre . ' agregado.']);
    }

    public function actualizarCantidad($index, $cantidad)
    {
        if (isset($this->carrito[$index])) {
            $this->carrito[$index]['cantidad'] = $cantidad > 0 ? $cantidad : 1;
            $this->actualizarTotal();
        }
    }

    public function eliminarDelCarrito($index)
    {
        if (isset($this->carrito[$index])) {
            unset($this->carrito[$index]);
            $this->carrito = array_values($this->carrito); // reindex
            $this->actualizarTotal();
        }
    }

    public function cancelarVenta()
    {
        $this->carrito = [];
        $this->actualizarTotal();
    }

    public function actualizarTotal()
    {
        $this->total = array_reduce($this->carrito, function($carry, $item) {
            return $carry + ($item['precio'] * $item['cantidad']);
        }, 0);
    }

    #[On('registrarVenta')]
    public function registrarVenta($metodo, $clienteId = null)
    {
        if (empty($this->carrito)) return;

        // Registrar la venta
        Venta::create([
            'cliente_id' => $clienteId ?: null,
            'user_id' => auth()->id(),
            'total' => $this->total,
            'metodo_pago' => $metodo
        ]);

        // Descontar stock
        foreach ($this->carrito as $item) {
            $prod = Producto::find($item['id']);
            if ($prod) {
                $prod->stock -= $item['cantidad'];
                $prod->save();
            }
        }

        // Si es crédito, actualizar el saldo del cliente
        if ($metodo === 'credito' && $clienteId) {
            $cliente = Cliente::find($clienteId);
            if ($cliente) {
                $cliente->saldo_pendiente += $this->total;
                $cliente->save();
            }
        }

        $this->cancelarVenta();
        $this->dispatch('swal:success', ['title' => '¡Venta Registrada!', 'text' => 'La venta se procesó correctamente.']);
    }
}
