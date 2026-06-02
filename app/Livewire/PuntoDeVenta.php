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

    public function mount()
    {
        $this->productos = Producto::all();
        $this->clientes = Cliente::all();
    }

    public function render()
    {
        return view('livewire.punto-de-venta')->layout('layouts.app');
    }
}
