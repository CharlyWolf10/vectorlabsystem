<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use Livewire\Attributes\On;

class Inventario extends Component
{
    public $search = '';
    public $selectedProductos = [];
    public $selectAll = false;
    public $filterProveedor = '';
    public $filterFaltantes = false;

    public function mount()
    {
        if (auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedProductos = Producto::pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selectedProductos = [];
        }
    }

    #[On('clearSelection')]
    public function clearSelection()
    {
        $this->selectedProductos = [];
        $this->selectAll = false;
    }

    public function render()
    {
        $query = Producto::with('proveedor')
                         ->where(function($q) {
                             $q->where('nombre', 'like', '%' . $this->search . '%')
                               ->orWhere('codigo', 'like', '%' . $this->search . '%');
                         });
                         
        if ($this->filterProveedor) {
            $query->where('proveedor_id', $this->filterProveedor);
        }

        if ($this->filterFaltantes) {
            $query->whereColumn('stock', '<=', 'stock_minimo');
        }

        $productos = $query->get();
                             
        $proveedores = \App\Models\Proveedor::all();
        $categorias = \App\Models\Categoria::all();
                             
        return view('livewire.inventario', compact('productos', 'proveedores', 'categorias'))->layout('layouts.app');
    }

    #[On('guardarProducto')]
    public function guardarProducto($data)
    {
        if (!empty($data['categoria'])) {
            \App\Models\Categoria::firstOrCreate(['nombre' => $data['categoria']]);
        }

        if (isset($data['id']) && !empty($data['id'])) {
            // Edit existing product
            $producto = Producto::find($data['id']);
            if ($producto) {
                $producto->update([
                    'nombre' => $data['nombre'],
                    'precio_compra' => $data['precio_compra'],
                    'precio_venta' => $data['precio_venta'] ?? 0,
                    'stock' => $data['stock'],
                    'stock_minimo' => $data['stock_minimo'],
                    'proveedor_id' => $data['proveedor_id'],
                    'categoria' => $data['categoria'] ?? null,
                    'ingreso_tipo_default' => $data['ingreso_tipo_default'] ?? 'unidad',
                    'ingreso_paquetes_default' => $data['ingreso_paquetes_default'] ?? 1,
                    'ingreso_unidades_default' => $data['ingreso_unidades_default'] ?? 1,
                ]);
            }
        } else {
            // Check if we can restore a soft-deleted product by exact name
            $nombre = $data['nombre'];
            $productoEliminado = Producto::onlyTrashed()
                ->where('nombre', $nombre)
                ->first();

            if ($productoEliminado) {
                // Restore the deleted product and update its info
                $productoEliminado->restore();
                $productoEliminado->update([
                    'precio_compra' => $data['precio_compra'],
                    'precio_venta' => $data['precio_venta'] ?? 0,
                    'stock' => $data['stock'],
                    'stock_minimo' => $data['stock_minimo'],
                    'proveedor_id' => $data['proveedor_id'],
                    'categoria' => $data['categoria'] ?? null,
                    'ingreso_tipo_default' => $data['ingreso_tipo_default'] ?? 'unidad',
                    'ingreso_paquetes_default' => $data['ingreso_paquetes_default'] ?? 1,
                    'ingreso_unidades_default' => $data['ingreso_unidades_default'] ?? 1,
                ]);
                $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Producto guardado correctamente.']);
                return;
            }

            // Create new product
            $codigo = $data['codigo'] ?? '';
            
            if (empty($codigo)) {
                $categoria = $data['categoria'] ?? 'General';
                $prefix = strtoupper(substr($categoria, 0, 3));
                if (strlen($prefix) < 3) {
                    $prefix = str_pad($prefix, 3, 'X');
                }
                
                // Find highest sequence for this prefix (including soft deleted)
                $lastProduct = Producto::withTrashed()
                    ->where('codigo', 'like', "VL-{$prefix}-%")
                    ->orderByRaw("CAST(SUBSTRING(codigo, 9) AS UNSIGNED) DESC")
                    ->first();
                
                $nextNum = 1;
                if ($lastProduct) {
                    $parts = explode('-', $lastProduct->codigo);
                    if (count($parts) >= 3 && is_numeric($parts[2])) {
                        $nextNum = intval($parts[2]) + 1;
                    }
                }
                
                $codigo = sprintf("VL-%s-%04d", $prefix, $nextNum);
            }

            Producto::create([
                'codigo' => $codigo,
                'nombre' => $data['nombre'],
                'precio_compra' => $data['precio_compra'],
                'precio_venta' => $data['precio_venta'] ?? 0,
                'stock' => $data['stock'],
                'stock_minimo' => $data['stock_minimo'],
                'proveedor_id' => $data['proveedor_id'],
                'categoria' => $data['categoria'] ?? null,
                'ingreso_tipo_default' => $data['ingreso_tipo_default'] ?? 'unidad',
                'ingreso_paquetes_default' => $data['ingreso_paquetes_default'] ?? 1,
                'ingreso_unidades_default' => $data['ingreso_unidades_default'] ?? 1,
            ]);
        }
        
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Producto guardado correctamente.']);
    }

    #[On('eliminarProducto')]
    public function eliminarProducto($id)
    {
        $producto = Producto::find($id);
        if ($producto) {
            $producto->delete();
            $this->dispatch('swal:success', ['title' => '¡Eliminado!', 'text' => 'Producto eliminado del inventario.']);
        }
    }

    public function attemptExport()
    {
        if (empty($this->selectedProductos)) {
            $this->dispatch('swal:error', ['title' => 'Atención', 'text' => 'Debes seleccionar al menos un producto para exportar.']);
            return;
        }
        $this->dispatch('abrirOpcionesExportacion');
    }

    #[On('exportSelected')]
    public function exportSelected()
    {
        $ids = implode(',', $this->selectedProductos);
        return redirect()->route('inventario.export', ['ids' => $ids]);
    }

    #[On('sendPdfEmail')]
    public function sendPdfEmail($email)
    {
        // Enviar al email especificado
        // Por ahora simulado
        $this->dispatch('swal:success', ['title' => '¡Enviado!', 'text' => 'El PDF ha sido enviado por correo a ' . $email]);
    }

    #[On('sendPdfWhatsApp')]
    public function sendPdfWhatsApp($telefono)
    {
        $mensaje = "TAL PARECE QUE HAY UN FALTANTE EN VECTOR LAB PORFAVOR CONSULTA AL ADMIN PARA SABER CUAL";
        $this->dispatch('openWhatsApp', ['telefono' => $telefono, 'mensaje' => $mensaje]);
    }
}
