<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ArqueoCaja;
use App\Models\Venta;
use Livewire\Attributes\On;

class Arqueos extends Component
{
    public $arqueos;
    public $arqueoActivo;
    
    // Ventas
    public $ventasEfectivo = 0;
    public $ventasTarjeta = 0;
    public $ventasTransferencia = 0;
    public $totalVentasHoy = 0;

    // Compras / Gastos
    public $comprasEfectivo = 0;
    public $comprasTarjeta = 0;
    public $comprasTransferencia = 0;
    public $totalComprasHoy = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->arqueos = ArqueoCaja::with('user')->orderBy('created_at', 'desc')->get();
        $this->arqueoActivo = ArqueoCaja::where('user_id', auth()->id())->whereNull('total_registrado_sistema')->latest()->first();

        // Ventas del día por método de pago
        $ventasHoy = Venta::whereDate('created_at', today())->get();
        $this->ventasEfectivo = $ventasHoy->where('metodo_pago', 'efectivo')->sum('total');
        $this->ventasTarjeta = $ventasHoy->whereIn('metodo_pago', ['tarjeta', 'tarjeta_credito', 'tarjeta_debito'])->sum('total');
        $this->ventasTransferencia = $ventasHoy->where('metodo_pago', 'transferencia')->sum('total');
        $this->totalVentasHoy = $ventasHoy->sum('total');

        // Compras/Gastos del día por método de pago
        $comprasHoy = \App\Models\Compra::whereDate('fecha', today())->get();
        $this->comprasEfectivo = $comprasHoy->where('metodo_pago', 'efectivo')->sum('monto');
        $this->comprasTarjeta = $comprasHoy->whereIn('metodo_pago', ['tarjeta', 'tarjeta_credito', 'tarjeta_debito'])->sum('monto');
        $this->comprasTransferencia = $comprasHoy->where('metodo_pago', 'transferencia')->sum('monto');
        $this->totalComprasHoy = $comprasHoy->sum('monto');
    }

    #[On('abrirCaja')]
    public function abrirCaja($fondo_inicial)
    {
        // En este caso, fondo_inicial siempre será 500 según la regla de negocio solicitada
        ArqueoCaja::create([
            'user_id' => auth()->id(),
            'fondo_inicial' => $fondo_inicial,
        ]);
        $this->loadData();
        $this->dispatch('swal:success', ['title' => 'Caja Abierta', 'text' => 'Se ha registrado la apertura de caja con el conteo físico correcto.']);
    }

    #[On('cerrarCaja')]
    public function cerrarCaja($data)
    {
        $arqueo = $this->arqueoActivo;
        if ($arqueo) {
            $arqueo->monedas_50c = $data['m_50c'];
            $arqueo->monedas_1 = $data['m_1'];
            $arqueo->monedas_2 = $data['m_2'];
            $arqueo->monedas_5 = $data['m_5'];
            $arqueo->monedas_10 = $data['m_10'];
            $arqueo->monedas_20 = $data['m_20'];
            $arqueo->billetes_50 = $data['b_50'];
            $arqueo->billetes_100 = $data['b_100'];
            $arqueo->billetes_200 = $data['b_200'];
            $arqueo->billetes_500 = $data['b_500'];
            
            $arqueo->total_calculado = $data['total'];
            
            // Total Sistema Efectivo = Fondo Inicial + Ventas Efectivo - Compras Efectivo
            $totalSistemaEfectivo = $arqueo->fondo_inicial + $this->ventasEfectivo - $this->comprasEfectivo;
            
            $arqueo->total_registrado_sistema = $totalSistemaEfectivo;
            $arqueo->diferencia = $data['total'] - $totalSistemaEfectivo;
            
            $arqueo->save();
        }

        $this->loadData();
        $this->dispatch('swal:success', ['title' => 'Corte de Caja Exitoso', 'text' => 'El arqueo se ha registrado y guardado.']);
    }

    public function render()
    {
        return view('livewire.arqueos')->layout('layouts.app');
    }
}
