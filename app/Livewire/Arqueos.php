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
    public $totalVentasHoy = 0;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->arqueos = ArqueoCaja::with('user')->orderBy('created_at', 'desc')->get();
        // Verificar si hay un arqueo abierto hoy (sin fecha de cierre o similar, simplificado aquí)
        // Para simplificar, asumiremos que el último arqueo si no está marcado, es el de hoy.
        // Simularemos las ventas del día
        $this->totalVentasHoy = Venta::whereDate('created_at', today())->sum('total');
    }

    #[On('abrirCaja')]
    public function abrirCaja($fondo_inicial)
    {
        ArqueoCaja::create([
            'user_id' => auth()->id(),
            'fondo_inicial' => $fondo_inicial,
        ]);
        $this->loadData();
        $this->dispatch('swal:success', ['title' => 'Caja Abierta', 'text' => 'Se ha registrado la apertura de caja.']);
    }

    #[On('cerrarCaja')]
    public function cerrarCaja($data)
    {
        // Obtener el último arqueo del usuario que podría considerarse "abierto"
        $arqueo = ArqueoCaja::where('user_id', auth()->id())->latest()->first();
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
            
            // Simulación: total registrado en sistema = fondo inicial + ventas en efectivo (asumiendo todas las ventas)
            $totalSistema = $arqueo->fondo_inicial + $this->totalVentasHoy;
            $arqueo->total_registrado_sistema = $totalSistema;
            $arqueo->diferencia = $data['total'] - $totalSistema;
            
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
