<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Punto de Venta') }}
        </h2>
    </x-slot>

    <div class="py-6 h-[calc(100vh-100px)] flex flex-col">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 w-full flex-1 flex flex-col md:flex-row gap-6">
            
            <!-- Panel de Productos -->
            <div class="w-full md:w-2/3 bg-white rounded-lg shadow-md p-6 flex flex-col h-full">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-xl font-bold text-gray-800">Caja Registradora</h3>
                    <input type="text" placeholder="Buscar producto o escanear código..." class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($productos as $producto)
                    <div onclick="agregarAlCarrito({{ $producto->id }}, '{{ $producto->nombre }}', {{ $producto->precio_venta }})" class="border rounded-lg p-4 cursor-pointer hover:bg-blue-50 transition-colors shadow-sm flex flex-col items-center justify-center text-center h-32 relative">
                        <span class="absolute top-2 right-2 text-xs font-bold text-gray-500">Stk: {{ $producto->stock }}</span>
                        <div class="text-3xl text-blue-500 mb-2"><i class="fas fa-box"></i></div>
                        <h4 class="font-semibold text-sm line-clamp-2 leading-tight">{{ $producto->nombre }}</h4>
                        <p class="text-green-600 font-bold mt-1">${{ number_format($producto->precio_venta, 2) }}</p>
                    </div>
                    @empty
                    <div class="col-span-4 text-center text-gray-500 py-10">
                        No hay productos en inventario.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Panel de Venta / Ticket -->
            <div class="w-full md:w-1/3 bg-white rounded-lg shadow-md flex flex-col h-full overflow-hidden">
                <div class="bg-gray-800 text-white p-4 text-center font-bold text-lg">
                    Ticket de Venta
                </div>
                
                <div class="p-4 border-b">
                    <select class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <option value="">Cliente Mostrador (Público en General)</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-gray-50" id="carrito-container">
                    <div class="text-center text-gray-400 mt-10">
                        <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                        <p>El carrito está vacío</p>
                        <p class="text-sm">Agregue productos para comenzar</p>
                    </div>
                    <!-- Los items se renderizarán vía Livewire o JS -->
                </div>

                <div class="bg-gray-100 p-4 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-bold">$0.00</span>
                    </div>
                    <div class="flex justify-between items-center mb-4 text-xl">
                        <span class="font-bold text-gray-800">TOTAL</span>
                        <span class="font-bold text-green-600">$0.00</span>
                    </div>
                    
                    <button onclick="cobrar()" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded shadow text-xl flex justify-center items-center">
                        <i class="fas fa-money-bill-wave mr-2"></i> COBRAR
                    </button>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded shadow">
                            <i class="fas fa-pause mr-1"></i> Espera
                        </button>
                        <button class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 rounded shadow">
                            <i class="fas fa-trash mr-1"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function agregarAlCarrito(id, nombre, precio) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: nombre + ' agregado.',
                showConfirmButton: false,
                timer: 1500
            });
            // Lógica Livewire pendiente
        }

        function cobrar() {
            Swal.fire({
                title: 'Cobrar Venta',
                html: `
                    <h2 class="text-3xl text-green-600 font-bold mb-4">$0.00</h2>
                    <select id="metodo_cobro" class="swal2-input mb-4"><option value="efectivo">Efectivo</option><option value="tarjeta">Tarjeta</option><option value="credito">Crédito a Cliente</option></select>
                    <input id="pago_con" type="number" step="0.01" class="swal2-input" placeholder="Pagó con (Ej: 500)">
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirmar Pago',
                confirmButtonColor: '#10b981',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Venta Registrada', 'La venta se completó correctamente.', 'success');
                }
            });
        }
    </script>
</div>
