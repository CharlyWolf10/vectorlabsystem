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
                    <a href="#" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-file-pdf mr-2"></i> Reporte
                    </a>
                </div>
            </div>

            <!-- Panel de Inventario -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live="search" placeholder="Buscar por código o nombre..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
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
                                <th class="py-2 px-4 text-right">Costo</th>
                                <th class="py-2 px-4 text-right">Venta</th>
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
                                <td class="py-2 px-4 text-right">${{ number_format($producto->precio_compra, 2) }}</td>
                                <td class="py-2 px-4 text-right text-green-600 font-bold">${{ number_format($producto->precio_venta, 2) }}</td>
                                <td class="py-2 px-4 text-center">
                                    <span class="px-2 py-1 rounded {{ $producto->stock <= $producto->stock_minimo ? 'bg-red-200 text-red-800' : 'bg-green-200 text-green-800' }}">
                                        {{ $producto->stock }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <button onclick="editarProducto('{{ $producto->codigo }}', '{{ $producto->nombre }}', {{ $producto->precio_compra }}, {{ $producto->precio_venta }}, {{ $producto->stock }}, {{ $producto->stock_minimo }})" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>
                                    <button onclick="eliminarProducto({{ $producto->id }})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="py-4 text-center text-gray-500">No hay productos en el inventario.</td>
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

        function nuevoProducto() {
            Swal.fire({
                title: 'Nuevo Producto',
                html: `
                    <input id="prod_codigo" class="swal2-input" placeholder="Código de Barras/SKU" required>
                    <input id="prod_nombre" class="swal2-input" placeholder="Nombre del Producto" required>
                    <input id="prod_costo" type="number" step="0.01" class="swal2-input" placeholder="Precio Costo $">
                    <input id="prod_venta" type="number" step="0.01" class="swal2-input" placeholder="Precio Venta $">
                    <input id="prod_stock" type="number" class="swal2-input" placeholder="Stock Inicial">
                    <input id="prod_minimo" type="number" class="swal2-input" placeholder="Stock Mínimo (Alerta)">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const codigo = document.getElementById('prod_codigo').value;
                    const nombre = document.getElementById('prod_nombre').value;
                    if(!codigo || !nombre) {
                        Swal.showValidationMessage('Código y Nombre son obligatorios');
                        return false;
                    }
                    return {
                        codigo: codigo,
                        nombre: nombre,
                        precio_compra: parseFloat(document.getElementById('prod_costo').value) || 0,
                        precio_venta: parseFloat(document.getElementById('prod_venta').value) || 0,
                        stock: parseInt(document.getElementById('prod_stock').value) || 0,
                        stock_minimo: parseInt(document.getElementById('prod_minimo').value) || 5,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas guardar este producto en el inventario?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0066ff',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar'
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            Livewire.dispatch('guardarProducto', [result.value]);
                        }
                    });
                }
            });
        }

        function editarProducto(codigo, nombre, costo, venta, stock, minimo) {
            Swal.fire({
                title: 'Editar Producto',
                html: `
                    <input id="prod_codigo" class="swal2-input" placeholder="Código de Barras/SKU" value="${codigo}" readonly>
                    <input id="prod_nombre" class="swal2-input" placeholder="Nombre del Producto" value="${nombre}" required>
                    <input id="prod_costo" type="number" step="0.01" class="swal2-input" placeholder="Precio Costo $" value="${costo}">
                    <input id="prod_venta" type="number" step="0.01" class="swal2-input" placeholder="Precio Venta $" value="${venta}">
                    <input id="prod_stock" type="number" class="swal2-input" placeholder="Stock Inicial" value="${stock}">
                    <input id="prod_minimo" type="number" class="swal2-input" placeholder="Stock Mínimo (Alerta)" value="${minimo}">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('prod_nombre').value;
                    if(!nombre) {
                        Swal.showValidationMessage('Nombre es obligatorio');
                        return false;
                    }
                    return {
                        codigo: document.getElementById('prod_codigo').value,
                        nombre: nombre,
                        precio_compra: parseFloat(document.getElementById('prod_costo').value) || 0,
                        precio_venta: parseFloat(document.getElementById('prod_venta').value) || 0,
                        stock: parseInt(document.getElementById('prod_stock').value) || 0,
                        stock_minimo: parseInt(document.getElementById('prod_minimo').value) || 5,
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
