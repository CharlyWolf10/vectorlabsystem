<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Inventario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Control de Inventario</h2>
                <div>
                    <button onclick="nuevoProducto()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mr-2">
                        <i class="fas fa-plus mr-2"></i> Nuevo Producto
                    </button>
                    <button wire:click="exportSelected" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
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
                                <th class="py-2 px-4 text-left">Código</th>
                                <th class="py-2 px-4 text-left">Producto</th>
                                <th class="py-2 px-4 text-left">Proveedor</th>
                                <th class="py-2 px-4 text-right">Costo</th>
                                <th class="py-2 px-4 text-right">Precio</th>
                                <th class="py-2 px-4 text-center">Stock</th>
                                <th class="py-2 px-4 text-center">Acciones</th>
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
                                <td class="py-2 px-4 text-sm text-gray-500">{{ $producto->proveedor ? $producto->proveedor->nombre : 'Sin proveedor' }}</td>
                                <td class="py-2 px-4 text-right text-red-600">${{ number_format($producto->precio_compra, 2) }}</td>
                                <td class="py-2 px-4 text-right text-green-600 font-bold">${{ number_format($producto->precio_venta, 2) }}</td>
                                <td class="py-2 px-4 text-center">
                                    <span class="px-2 py-1 rounded {{ $producto->stock <= $producto->stock_minimo ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} font-bold">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <button onclick="editarProducto('{{ $producto->id }}', '{{ $producto->codigo }}', '{{ $producto->nombre }}', '{{ $producto->precio_compra }}', '{{ $producto->precio_venta }}', '{{ $producto->stock }}', '{{ $producto->stock_minimo }}', '{{ $producto->proveedor_id }}')" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>
                                    <button onclick="eliminarProducto('{{ $producto->id }}')" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="py-4 text-center text-gray-500">No hay productos en el inventario.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('swal:success', event => {
            Swal.fire({
                icon: 'success',
                title: event.detail[0].title,
                text: event.detail[0].text,
            });
        });

        window.addEventListener('swal:error', event => {
            Swal.fire({
                icon: 'error',
                title: event.detail[0].title,
                text: event.detail[0].text,
            });
        });

        function nuevoProducto() {
            let proveedoresHtml = '<select id="prod_proveedor" class="swal2-select w-full max-w-[200px] mt-2 mb-2"><option value="">Seleccione Proveedor (Opcional)</option>';
            @foreach($proveedores as $prov)
                proveedoresHtml += `<option value="{{ $prov->id }}">{{ $prov->nombre }}</option>`;
            @endforeach
            proveedoresHtml += '</select>';

            Swal.fire({
                title: 'Nuevo Producto',
                html: `
                    <input id="prod_codigo" class="swal2-input" placeholder="Código de Barras/SKU" required>
                    <input id="prod_nombre" class="swal2-input" placeholder="Nombre del Producto" required>
                    ${proveedoresHtml}
                    <input id="prod_compra" type="number" step="0.01" class="swal2-input" placeholder="Precio de Compra (Costo) $">
                    <input id="prod_venta" type="number" step="0.01" class="swal2-input" placeholder="Precio de Venta (Público) $">
                    <input id="prod_stock" type="number" class="swal2-input" placeholder="Cantidad en Stock">
                    <input id="prod_minimo" type="number" class="swal2-input" placeholder="Stock Mínimo (Alerta)">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const codigo = document.getElementById('prod_codigo').value;
                    const nombre = document.getElementById('prod_nombre').value;
                    if (!codigo || !nombre) {
                        Swal.showValidationMessage('El código y el nombre son obligatorios');
                        return false;
                    }
                    return {
                        codigo: codigo,
                        nombre: nombre,
                        precio_compra: document.getElementById('prod_compra').value || 0,
                        precio_venta: document.getElementById('prod_venta').value || 0,
                        stock: document.getElementById('prod_stock').value || 0,
                        stock_minimo: document.getElementById('prod_minimo').value || 0,
                        proveedor_id: document.getElementById('prod_proveedor').value || null
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('guardarProducto', [result.value]);
                }
            });
        }

        function editarProducto(id, codigo, nombre, compra, venta, stock, minimo, proveedor_id) {
            let proveedoresHtml = '<select id="prod_proveedor" class="swal2-select w-full max-w-[200px] mt-2 mb-2"><option value="">Seleccione Proveedor (Opcional)</option>';
            @foreach($proveedores as $prov)
                proveedoresHtml += `<option value="{{ $prov->id }}" ${proveedor_id == '{{ $prov->id }}' ? 'selected' : ''}>{{ $prov->nombre }}</option>`;
            @endforeach
            proveedoresHtml += '</select>';

            Swal.fire({
                title: 'Editar Producto',
                html: `
                    <input id="prod_id" type="hidden" value="${id}">
                    <input id="prod_codigo" class="swal2-input" placeholder="Código de Barras/SKU" value="${codigo}" required readonly>
                    <input id="prod_nombre" class="swal2-input" placeholder="Nombre del Producto" value="${nombre}" required>
                    ${proveedoresHtml}
                    <input id="prod_compra" type="number" step="0.01" class="swal2-input" placeholder="Precio de Compra (Costo) $" value="${compra}">
                    <input id="prod_venta" type="number" step="0.01" class="swal2-input" placeholder="Precio de Venta (Público) $" value="${venta}">
                    <input id="prod_stock" type="number" class="swal2-input" placeholder="Cantidad en Stock" value="${stock}">
                    <input id="prod_minimo" type="number" class="swal2-input" placeholder="Stock Mínimo (Alerta)" value="${minimo}">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Actualizar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('prod_nombre').value;
                    if (!nombre) {
                        Swal.showValidationMessage('El nombre es obligatorio');
                        return false;
                    }
                    return {
                        id: document.getElementById('prod_id').value,
                        codigo: document.getElementById('prod_codigo').value,
                        nombre: nombre,
                        precio_compra: document.getElementById('prod_compra').value || 0,
                        precio_venta: document.getElementById('prod_venta').value || 0,
                        stock: document.getElementById('prod_stock').value || 0,
                        stock_minimo: document.getElementById('prod_minimo').value || 0,
                        proveedor_id: document.getElementById('prod_proveedor').value || null
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('guardarProducto', [result.value]);
                }
            });
        }

        function eliminarProducto(id) {
            Swal.fire({
                title: '¿Eliminar producto?',
                text: "Esta acción no se puede deshacer.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('eliminarProducto', [id]);
                }
            });
        }
    </script>
</div>
