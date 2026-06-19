<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Producto;
use App\Models\HistorialInventario;
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
        $precio_compra = floatval($data['precio_compra'] ?? 0);
        $precio_venta = floatval($data['precio_venta'] ?? 0);
        $aplicaIva = isset($data['aplica_iva']) && $data['aplica_iva'] ? 1 : 0;
        $stock_minimo = intval($data['stock_minimo'] ?? 0);
        $stock = intval($data['stock'] ?? 0);
        $categoriaId = null;

        if (!empty($data['categoria'])) {
            $cat = \App\Models\Categoria::firstOrCreate(['nombre' => $data['categoria']]);
            $categoriaId = $cat->id;
        }

        if (isset($data['id']) && !empty($data['id'])) {
            // Edit existing product
            $producto = Producto::find($data['id']);
            if ($producto) {
                $cambios = [];
                $camposTracking = [
                    'nombre' => 'Nombre',
                    'precio_compra' => 'Costo Neto',
                    'precio_venta' => 'Precio Venta',
                    'stock' => 'Stock Actual',
                    'stock_minimo' => 'Stock Mínimo',
                    'proveedor_id' => 'Proveedor',
                    'categoria' => 'Categoría',
                    'aplica_iva' => 'Aplica IVA'
                ];
                $detalles = [];
                if ($producto->stock_minimo != $stock_minimo) {
                    $detalles[] = "Stock Mínimo cambió de '{$producto->stock_minimo}' a '{$stock_minimo}'";
                }
                if ($producto->precio_compra != $precio_compra) {
                    $detalles[] = "Precio de compra cambió de '\${$producto->precio_compra}' a '\${$precio_compra}'";
                }
                if ($producto->aplica_iva != $aplicaIva) {
                    $estadoIva = $aplicaIva ? "Activado" : "Desactivado";
                    $detalles[] = "Estado del IVA modificado: {$estadoIva}";
                }

                $producto->update([
                    'codigo' => mb_strtoupper($data['codigo'] ?? $producto->codigo),
                    'nombre' => mb_strtoupper($data['nombre']),
                    'precio_compra' => $precio_compra,
                    'precio_venta' => $precio_venta > 0 ? $precio_venta : $producto->precio_venta,
                    'aplica_iva' => $aplicaIva,
                    'stock_minimo' => $stock_minimo,
                    'proveedor_id' => $data['proveedor_id'] ?? null,
                    'categoria' => mb_strtoupper($data['categoria'] ?? ''),
                ]);

                if (!empty($detalles)) {
                    HistorialInventario::create([
                        'producto_id' => $producto->id,
                        'user_id' => auth()->id(),
                        'accion' => 'EDITADO',
                        'detalles' => $detalles
                    ]);
                }
            }
        } else {
            $nombre = $data['nombre'];
            $productoEliminado = Producto::onlyTrashed()->where('nombre', $nombre)->first();

            if ($productoEliminado) {
                $productoEliminado->restore();
                $productoEliminado->update([
                    'precio_compra' => $precio_compra,
                    'precio_venta' => $precio_venta,
                    'stock' => $stock,
                    'stock_minimo' => $stock_minimo,
                    'proveedor_id' => $data['proveedor_id'] ?? null,
                    'categoria' => mb_strtoupper($data['categoria'] ?? ''),
                    'aplica_iva' => $aplicaIva,
                ]);
                
                HistorialInventario::create([
                    'producto_id' => $productoEliminado->id,
                    'user_id' => auth()->id(),
                    'accion' => 'RESTAURADO',
                    'detalles' => ['El producto fue restaurado y actualizado.']
                ]);
                $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'El producto ha sido editado correctamente.']);
                return;
            }

            $nuevoProducto = Producto::create([
                'codigo' => $data['codigo'] ?? 'N/A',
                'nombre' => mb_strtoupper($data['nombre']),
                'precio_compra' => $precio_compra,
                'precio_venta' => $precio_venta,
                'stock' => $stock,
                'stock_minimo' => $stock_minimo,
                'proveedor_id' => $data['proveedor_id'] ?? null,
                'categoria' => mb_strtoupper($data['categoria'] ?? ''),
                'aplica_iva' => $aplicaIva,
            ]);

            HistorialInventario::create([
                'producto_id' => $nuevoProducto->id,
                'user_id' => auth()->id(),
                'accion' => 'CREADO',
                'detalles' => ["Producto ingresado al sistema con precio base \$$precio_compra"]
            ]);
        }
        
        $this->dispatch('swal:success', ['title' => isset($data['id']) ? '¡Editado!' : '¡Agregado!', 'text' => isset($data['id']) ? 'El producto ha sido editado correctamente.' : 'El producto ha sido agregado al inventario.']);
    }

    /**
     * Crea una nueva categoría en el catálogo.
     */
    #[On('crearCategoria')]
    public function crearCategoria($nombre, $tipo_medida = null)
    {
        $nombre = strtoupper(trim($nombre));
        if (!empty($nombre)) {
            $cat = \App\Models\Categoria::firstOrCreate(
                ['nombre' => $nombre],
                ['tipo_medida' => $tipo_medida]
            );
            $this->emitirCategoriasActualizadas();
            $this->dispatch('categoriaCreada', nombre: $cat->nombre, tipo_medida: $cat->tipo_medida);
            $this->dispatch('swal:success', [
                'title' => '¡Agregada!',
                'text'  => 'La categoría ha sido creada correctamente.'
            ]);
        }
    }

    /**
     * Marca un producto como eliminado (Soft Delete).
     */
    #[On('eliminarProducto')]
    public function eliminarProducto($id)
    {
        $producto = Producto::find($id);
        if ($producto) {
            HistorialInventario::create([
                'producto_id' => $producto->id,
                'user_id' => auth()->id(),
                'accion' => 'ELIMINADO',
                'detalles' => ["Producto eliminado del inventario."]
            ]);
            $producto->delete();
            $this->dispatch('swal:success', ['title' => '¡Eliminado!', 'text' => 'Producto eliminado del inventario.']);
        }
    }

    /**
     * Edita los detalles de una categoría existente.
     */
    #[On('actualizarCategoria')]
    public function actualizarCategoria($id, $nuevoNombre, $tipo_medida = null)
    {
        $categoria = \App\Models\Categoria::find($id);
        if ($categoria && !empty(trim($nuevoNombre))) {
            $viejoNombre = $categoria->nombre;
            $categoria->nombre = trim($nuevoNombre);
            $categoria->tipo_medida = $tipo_medida;
            $categoria->save();
            
            \App\Models\Producto::where('categoria', $viejoNombre)->update(['categoria' => $categoria->nombre]);
            
            $this->dispatch('swal:success', ['title' => '¡Editada!', 'text' => 'La categoría ha sido editada correctamente.']);
            $this->emitirCategoriasActualizadas();
            $this->dispatch('categoriaActualizada');
        }
    }

    /**
     * Elimina una categoría y limpia la referencia en los productos asociados.
     */
    #[On('eliminarCategoria')]
    public function eliminarCategoria($id)
    {
        $categoria = \App\Models\Categoria::find($id);
        if ($categoria) {
            $nombre = $categoria->nombre;
            $categoria->delete();
            
            \App\Models\Producto::where('categoria', $nombre)->update(['categoria' => null]);
            
            $this->dispatch('swal:success', ['title' => '¡Eliminada!', 'text' => 'La categoría ha sido eliminada correctamente.']);
            $this->emitirCategoriasActualizadas();
        }
    }

    /**
     * Registra un ingreso de stock independiente.
     * Actualiza el stock del producto y guarda un registro "INGRESO_STOCK" en el historial de auditoría.
     */
    #[On('registrarIngresoStock')]
    public function registrarIngresoStock($productoId, $cantidadASumar, $detallesAuditoria)
    {
        $producto = Producto::find($productoId);
        if ($producto && $cantidadASumar > 0) {
            $stockAnterior = $producto->stock;
            $producto->stock += $cantidadASumar;
            $producto->save();

            HistorialInventario::create([
                'producto_id' => $producto->id,
                'user_id' => auth()->id(),
                'accion' => 'INGRESO_STOCK',
                'detalles' => [
                    $detallesAuditoria,
                    "Stock anterior: $stockAnterior",
                    "Stock nuevo: " . $producto->stock
                ]
            ]);

            $this->dispatch('swal:success', ['title' => '¡Stock Actualizado!', 'text' => 'El inventario se ha incrementado correctamente.']);
        }
    }

    /**
     * Emite un evento para actualizar la lista de categorías en la interfaz.
     */
    private function emitirCategoriasActualizadas()
    {
        $categorias = \App\Models\Categoria::orderBy('nombre')->get()->toArray();
        $this->dispatch('categoriasActualizadas', categorias: $categorias);
    }

    /**
     * Valida la selección de productos para exportación.
     */
    public function attemptExport()
    {
        if (empty($this->selectedProductos)) {
            $this->dispatch('swal:error', ['title' => 'Atención', 'text' => 'Debes seleccionar al menos un producto para exportar.']);
            return;
        }
        // Directamente abrir la ventana flotante de previsualización
        $this->exportSelected();
    }

    #[On('exportSelected')]
    public function exportSelected()
    {
        $params = [];
        if (!empty($this->selectedProductos)) {
            $params['ids'] = implode(',', $this->selectedProductos);
        }
        if ($this->filterFaltantes) {
            $params['faltantes'] = 1;
        }
        $url = route('inventario.export', $params);
        $previewUrl = route('inventario.export', array_merge($params, ['preview' => true]));
        $this->dispatch('openExportPreview', previewUrl: $previewUrl, exportPdfUrl: $url, title: 'Reporte de Inventario');
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

    #[On('guardarProveedor')]
    public function guardarProveedor($data)
    {
        $id = $data['id'] ?? null;
        \App\Models\Proveedor::updateOrCreate(
            ['id' => $id],
            [
                'nombre' => $data['nombre'],
                'telefono' => $data['telefono'] ?? null,
                'email' => $data['email'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'rfc' => $data['rfc'] ?? null,
                'banco' => $data['banco'] ?? null,
                'clabe' => $data['clabe'] ?? null,
                'num_cuenta' => $data['num_cuenta'] ?? null,
                'titular_cuenta' => $data['titular_cuenta'] ?? null,
            ]
        );
        $this->dispatch('swal:success', ['title' => '¡Éxito!', 'text' => 'Proveedor guardado correctamente.']);
    }

    #[On('cargarHistorial')]
    public function cargarHistorial($id)
    {
        $producto = Producto::withTrashed()->find($id);
        if ($producto) {
            // Retrieve history
            $historialRaw = HistorialInventario::with('user')
                                ->where('producto_id', $id)
                                ->orderBy('created_at', 'desc')
                                ->get();
                                
            $historial = $historialRaw->map(function($h) {
                return [
                    'fecha' => $h->created_at->format('d/m/Y h:i A'),
                    'usuario' => $h->user ? $h->user->name : 'Sistema',
                    'accion' => $h->accion,
                    'detalles' => is_array($h->detalles) ? $h->detalles : json_decode($h->detalles, true)
                ];
            });
            
            // Check if CREADO event exists
            $hasCreado = $historialRaw->contains('accion', 'CREADO');
            if (!$hasCreado) {
                // If not, artificially add it at the end (oldest)
                $historial->push([
                    'fecha' => $producto->created_at->format('d/m/Y h:i A'),
                    'usuario' => 'Sistema',
                    'accion' => 'CREADO',
                    'detalles' => ['Producto creado en el sistema']
                ]);
            }
            
            $this->dispatch('mostrarHistorial', historial: $historial, nombre: $producto->nombre, id: $producto->id);
        }
    }
}
