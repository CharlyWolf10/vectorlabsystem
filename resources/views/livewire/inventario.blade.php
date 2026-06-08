<div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css" />
    <link rel="stylesheet" href="{{ asset('css/inventario.css') }}" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-[95%] mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Control de Inventario</h2>
                <div>
                    <button wire:click="attemptExport" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
                    </button>
                    <button onclick="abrirGestorCategorias()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mr-2">
                        <i class="fas fa-tags mr-2"></i> Categorías
                    </button>
                    <button onclick="nuevoProveedor()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mr-2">
                        <i class="fas fa-truck mr-2"></i> Nuevo Proveedor
                    </button>
                    <button onclick="nuevoProducto()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-plus mr-2"></i> Nuevo Producto
                    </button>
                </div>
            </div>

            <!-- Panel de Inventario -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="w-full md:w-3/4 flex flex-col md:flex-row gap-4">
                        <input type="text" wire:model.live="search" placeholder="Buscar por código o nombre..." class="w-full md:w-1/2 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                        
                        <select wire:model.live="filterProveedor" class="w-full md:w-1/4 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                            <option value="">Todos los Proveedores</option>
                            @foreach($proveedores as $prov)
                                <option value="{{ $prov->id }}">{{ $prov->nombre }}</option>
                            @endforeach
                        </select>

                        <button wire:click="$toggle('filterFaltantes')" class="w-full md:w-1/4 {{ $filterFaltantes ? 'bg-orange-500 hover:bg-orange-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' }} font-bold py-2 px-4 rounded shadow transition-colors border border-gray-300 md:border-none">
                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $filterFaltantes ? 'Solo Faltantes' : 'Ver Faltantes' }}
                        </button>
                    </div>
                    @if(count($selectedProductos) > 0)
                        <div class="text-sm font-semibold text-blue-600">
                            {{ count($selectedProductos) }} producto(s) seleccionado(s)
                        </div>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-2 px-4 text-center w-12"><input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></th>
                                <th class="py-2 px-2 text-left w-24">Código</th>
                                <th class="py-2 px-4 text-left">Producto</th>
                                <th class="py-2 px-2 text-left w-36">Categoría</th>
                                <th class="py-2 px-2 text-left w-48">Proveedor</th>
                                <th class="py-2 px-4 text-right">Costo</th>
                                <th class="py-2 px-4 text-center">Stock</th>
                                <th class="py-2 px-2 text-center w-32">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productos as $producto)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4 text-center">
                                    <input type="checkbox" value="{{ $producto->id }}" wire:model="selectedProductos" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                                <td class="py-2 px-4">{{ $producto->codigo }}</td>
                                <td class="py-2 px-4 font-bold">{{ $producto->nombre }}</td>
                                <td class="py-2 px-4 text-sm text-gray-600">{{ $producto->categoria ?: 'General' }}</td>
                                <td class="py-2 px-4 text-sm text-gray-500">{{ $producto->proveedor ? $producto->proveedor->nombre : 'Sin proveedor' }}</td>
                                <td class="py-2 px-4 text-right text-red-600">
                                    ${{ number_format($producto->precio_compra, 2) }}
                                    {{-- Etiqueta visual para indicar si el costo incluye el 16% de IVA --}}
                                    @if($producto->aplica_iva)
                                        <br><span class="text-xs text-gray-500 font-bold">+16% IVA</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <span class="px-2 py-1 rounded {{ $producto->stock <= $producto->stock_minimo ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} font-bold">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                                <td class="py-2 px-2 text-center whitespace-nowrap">
                                    {{-- Botón exclusivo para ingresar stock adicional mediante el nuevo modal --}}
                                    <button onclick="ingresarStockModal('{{ $producto->id }}', '{{ addslashes($producto->nombre) }}', '{{ $producto->stock }}', '{{ $producto->ingreso_tipo_default ?? 'unidad' }}', {{ $producto->ingreso_paquetes_default ?? 1 }}, {{ $producto->ingreso_unidades_default ?? 1 }})" class="text-purple-600 hover:text-purple-800 mr-2" title="Ingresar Stock Adicional"><i class="fas fa-box-open"></i></button>
                                    
                                    {{-- Botón para editar la información básica y precios del producto --}}
                                    <button onclick="editarProducto('{{ $producto->id }}', '{{ $producto->codigo }}', '{{ addslashes($producto->nombre) }}', '{{ $producto->precio_compra }}', {{ $producto->aplica_iva ? 'true' : 'false' }}, '{{ $producto->stock_minimo }}', '{{ $producto->proveedor_id }}', '{{ $producto->categoria }}')" class="text-blue-500 hover:text-blue-700 mr-2" title="Editar Producto"><i class="fas fa-edit"></i></button>
                                    
                                    {{-- Botón para ver la auditoría y exportar el historial a PDF --}}
                                    <button wire:click="cargarHistorial({{ $producto->id }})" class="text-green-600 hover:text-green-800 mr-2" title="Historial y Auditoría"><i class="fas fa-history"></i></button>
                                    
                                    <button onclick="eliminarProducto('{{ $producto->id }}')" class="text-red-500 hover:text-red-700" title="Eliminar Producto"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="py-4 text-center text-gray-500">No hay productos en el inventario.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables dinámicas inyectadas desde Laravel para uso de JS externo
        window.inventarioProveedores = @json($proveedores);
        window.inventarioCategorias = @json($categorias);
    </script>
    <script src="{{ asset('js/proveedores.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/inventario.js') }}?v={{ time() }}"></script>
</div>
