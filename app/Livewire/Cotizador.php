<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\Formula;
use App\Models\FormulaIngrediente;
use App\Models\GastoOperativo;

class Cotizador extends Component
{
    public $nombre = '';
    public $margen_ganancia = 0;
    public $minutos_produccion = 0;
    
    // Búsqueda de productos
    public $search = '';
    public $resultados = [];

    // Ingredientes seleccionados
    public $ingredientes = []; // formato: ['producto_id' => 1, 'nombre' => 'Papel', 'cantidad' => 1, 'precio' => 10, 'unidad' => 'pieza']
    
    // Tintas CMYK (especial)
    public $lleva_impresion = false;
    public $cmyk = [
        'C' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
        'M' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
        'Y' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
        'K' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
    ];

    public $costo_por_minuto = 0;

    public function mount()
    {
        $gasto = GastoOperativo::first();
        $this->costo_por_minuto = $gasto ? $gasto->costo_por_minuto : 0;
    }

    public function updatedSearch()
    {
        if (strlen($this->search) > 1) {
            $this->resultados = Producto::where('nombre', 'like', '%' . $this->search . '%')
                                        ->orWhere('codigo', 'like', '%' . $this->search . '%')
                                        ->take(5)
                                        ->get();
        } else {
            $this->resultados = [];
        }
    }

    public function agregarIngrediente($producto_id)
    {
        $producto = Producto::find($producto_id);
        if ($producto) {
            // Check if already exists
            $exists = collect($this->ingredientes)->contains('producto_id', $producto->id);
            if (!$exists) {
                // Determine unit and cost
                $unidad = 'pieza';
                $precio_unitario = $producto->precio_pieza > 0 ? $producto->precio_pieza : $producto->precio_venta;
                
                $this->ingredientes[] = [
                    'producto_id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'cantidad' => 1,
                    'precio_unitario' => $precio_unitario,
                    'unidad' => $unidad
                ];
            }
        }
        $this->search = '';
        $this->resultados = [];
    }

    public function removerIngrediente($index)
    {
        unset($this->ingredientes[$index]);
        $this->ingredientes = array_values($this->ingredientes);
    }

    // Configurar tintas desde el inventario. Idealmente el usuario buscaría la tinta y la asignaría
    // Pero por simplicidad, permitiremos buscar la tinta para cada color
    public $search_c = '', $search_m = '', $search_y = '', $search_k = '';
    public $res_c = [], $res_m = [], $res_y = [], $res_k = [];

    public function updatedSearchC() { $this->res_c = $this->buscarTinta($this->search_c); }
    public function updatedSearchM() { $this->res_m = $this->buscarTinta($this->search_m); }
    public function updatedSearchY() { $this->res_y = $this->buscarTinta($this->search_y); }
    public function updatedSearchK() { $this->res_k = $this->buscarTinta($this->search_k); }

    private function buscarTinta($term) {
        if(strlen($term) > 1) {
            return Producto::where('nombre', 'like', '%' . $term . '%')->take(3)->get();
        }
        return [];
    }

    public function seleccionarTinta($color, $producto_id) {
        $producto = Producto::find($producto_id);
        if($producto) {
            $precio_ml = 0;
            if($producto->cantidad_paquete > 0) {
                // Asumiendo que el costo total de la botella está en precio_compra o precio_venta y cantidad_paquete son los mililitros
                // Usaremos el precio_pieza si está calculado, de lo contrario lo calculamos
                $precio_ml = $producto->precio_pieza > 0 ? $producto->precio_pieza : ($producto->precio_compra / $producto->cantidad_paquete);
            }
            $this->cmyk[$color]['producto_id'] = $producto->id;
            $this->cmyk[$color]['nombre'] = $producto->nombre;
            $this->cmyk[$color]['precio_ml'] = $precio_ml;
        }
        $this->{"search_".strtolower($color)} = '';
        $this->{"res_".strtolower($color)} = [];
    }

    public function getCostoMaterialesProperty()
    {
        $costo = 0;
        foreach ($this->ingredientes as $ing) {
            $costo += ($ing['cantidad'] * $ing['precio_unitario']);
        }
        
        if ($this->lleva_impresion) {
            foreach (['C', 'M', 'Y', 'K'] as $color) {
                $costo += ($this->cmyk[$color]['cantidad'] * $this->cmyk[$color]['precio_ml']);
            }
        }
        return $costo;
    }

    public function getCostoOperativoProperty()
    {
        return $this->minutos_produccion * $this->costo_por_minuto;
    }

    public function getCostoTotalProperty()
    {
        return $this->costo_materiales + $this->costo_operativo;
    }

    public function getPrecioSugeridoProperty()
    {
        $costo = $this->costo_total;
        if ($this->margen_ganancia > 0) {
            $precio = $costo + ($costo * ($this->margen_ganancia / 100));
        } else {
            $precio = $costo;
        }
        return round($precio);
    }

    public function guardar()
    {
        $this->validate([
            'nombre' => 'required|string|max:255',
            'margen_ganancia' => 'numeric|min:0',
            'minutos_produccion' => 'numeric|min:0',
        ]);

        if (empty($this->ingredientes) && !$this->lleva_impresion) {
            $this->dispatch('swal:error', ['title' => 'Error', 'text' => 'Debes agregar al menos un ingrediente o tinta.']);
            return;
        }

        $formula = Formula::create([
            'nombre' => $this->nombre,
            'precio_costo' => $this->costo_total,
            'margen_ganancia' => $this->margen_ganancia,
            'precio_venta' => $this->precio_sugerido,
            'minutos_produccion' => $this->minutos_produccion,
            'costo_operativo' => $this->costo_operativo
        ]);

        foreach ($this->ingredientes as $ing) {
            FormulaIngrediente::create([
                'formula_id' => $formula->id,
                'producto_id' => $ing['producto_id'],
                'cantidad' => $ing['cantidad'],
                'unidad' => $ing['unidad']
            ]);
        }

        if ($this->lleva_impresion) {
            foreach (['C', 'M', 'Y', 'K'] as $color) {
                if ($this->cmyk[$color]['producto_id'] && $this->cmyk[$color]['cantidad'] > 0) {
                    FormulaIngrediente::create([
                        'formula_id' => $formula->id,
                        'producto_id' => $this->cmyk[$color]['producto_id'],
                        'cantidad' => $this->cmyk[$color]['cantidad'],
                        'unidad' => 'ml'
                    ]);
                }
            }
        }

        $this->reset(['nombre', 'margen_ganancia', 'minutos_produccion', 'ingredientes', 'lleva_impresion']);
        $this->cmyk = [
            'C' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
            'M' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
            'Y' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
            'K' => ['cantidad' => 0, 'producto_id' => null, 'precio_ml' => 0],
        ];

        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Fórmula guardada correctamente.']);
    }

    public function render()
    {
        return view('livewire.cotizador')->layout('layouts.app');
    }
}
