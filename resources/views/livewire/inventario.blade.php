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
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
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
                            <tr class="border-b">
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
                                    <button class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">No hay productos en el inventario.</td>
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
                        precio_compra: document.getElementById('prod_costo').value || 0,
                        precio_venta: document.getElementById('prod_venta').value || 0,
                        stock: document.getElementById('prod_stock').value || 0,
                        stock_minimo: document.getElementById('prod_minimo').value || 5,
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
                            Livewire.dispatch('guardarProducto', { data: result.value });
                        }
                    });
                }
            });
        }
    </script>
</div>
