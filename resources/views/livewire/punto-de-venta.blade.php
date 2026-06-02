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
                    <input type="text" wire:model.live="search" placeholder="Buscar producto o escanear código..." class="w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($productos as $producto)
                    <div wire:click="agregarAlCarrito({{ $producto->id }})" class="border rounded-lg p-4 cursor-pointer hover:bg-blue-50 transition-colors shadow-sm flex flex-col items-center justify-center text-center h-32 relative">
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
                    <select id="select_cliente_venta" wire:model="cliente_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200">
                        <option value="">Cliente Mostrador (Público en General)</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}">{{ $cliente->nombre }} {{ $cliente->apellidos }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex-1 overflow-y-auto p-4 custom-scrollbar bg-gray-50" id="carrito-container">
                    @if(count($carrito) > 0)
                        @foreach($carrito as $index => $item)
                            <div class="flex justify-between items-center bg-white p-3 rounded shadow-sm mb-2 border-l-4 border-blue-500">
                                <div class="flex-1">
                                    <h4 class="font-bold text-sm text-gray-800">{{ $item['nombre'] }}</h4>
                                    <p class="text-xs text-gray-500">${{ number_format($item['precio'], 2) }} x {{ $item['cantidad'] }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <input type="number" wire:model.blur="carrito.{{ $index }}.cantidad" wire:change="actualizarCantidad({{ $index }}, $event.target.value)" value="{{ $item['cantidad'] }}" class="w-16 rounded border-gray-300 p-1 text-sm text-center" min="1">
                                    <div class="font-bold text-blue-600 w-16 text-right">${{ number_format($item['precio'] * $item['cantidad'], 2) }}</div>
                                    <button wire:click="eliminarDelCarrito({{ $index }})" class="text-red-500 hover:text-red-700 ml-2"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center text-gray-400 mt-10">
                            <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                            <p>El carrito está vacío</p>
                            <p class="text-sm">Agregue productos para comenzar</p>
                        </div>
                    @endif
                </div>

                <div class="bg-gray-100 p-4 border-t">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-bold">${{ number_format($total, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4 text-xl">
                        <span class="font-bold text-gray-800">TOTAL</span>
                        <span class="font-bold text-green-600">${{ number_format($total, 2) }}</span>
                    </div>
                    
                    <button onclick="cobrar({{ $total }})" {{ count($carrito) == 0 ? 'disabled' : '' }} class="w-full {{ count($carrito) == 0 ? 'bg-gray-400 cursor-not-allowed' : 'bg-green-500 hover:bg-green-600' }} text-white font-bold py-4 rounded shadow text-xl flex justify-center items-center">
                        <i class="fas fa-money-bill-wave mr-2"></i> COBRAR
                    </button>
                    <div class="grid grid-cols-2 gap-2 mt-2">
                        <button class="bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded shadow">
                            <i class="fas fa-pause mr-1"></i> Espera
                        </button>
                        <button wire:click="cancelarVenta" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 rounded shadow">
                            <i class="fas fa-trash mr-1"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('swal:toast', event => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: event.detail[0].title,
                showConfirmButton: false,
                timer: 1500
            });
        });

        window.addEventListener('swal:success', event => {
            Swal.fire({
                icon: 'success',
                title: event.detail[0].title,
                text: event.detail[0].text,
            });
        });

        function cobrar(total) {
            if (total <= 0) return;
            
            Swal.fire({
                title: 'Cobrar Venta',
                html: `
                    <h2 id="total_display" class="text-3xl text-green-600 font-bold mb-4">$${total.toFixed(2)}</h2>
                    <select id="metodo_cobro" class="swal2-input mb-4"><option value="efectivo">Efectivo</option><option value="tarjeta">Tarjeta</option><option value="transferencia">Transferencia</option><option value="credito">Crédito a Cliente</option></select>
                    <select id="descuento" class="swal2-input mb-4" onchange="actualizarTotalCobro(${total})">
                        <option value="0">Sin Descuento (0%)</option>
                        <option value="5">Descuento 5%</option>
                        <option value="10">Descuento 10%</option>
                        <option value="15">Descuento 15%</option>
                        <option value="20">Descuento 20%</option>
                    </select>
                    <input id="pago_con" type="number" step="0.01" class="swal2-input" placeholder="Pagó con (Ej: 500)">
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirmar Pago',
                confirmButtonColor: '#10b981',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const metodo = document.getElementById('metodo_cobro').value;
                    const clienteId = document.getElementById('select_cliente_venta').value;
                    const descuento = document.getElementById('descuento').value;
                    
                    if (metodo === 'credito' && !clienteId) {
                        Swal.showValidationMessage('Debe seleccionar un cliente para vender a crédito');
                        return false;
                    }
                    
                    return { metodo: metodo, clienteId: clienteId, descuento: descuento };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('registrarVenta', [result.value.metodo, result.value.clienteId, result.value.descuento]);
                }
            });
        }

        function actualizarTotalCobro(totalBase) {
            const desc = document.getElementById('descuento').value;
            const nuevoTotal = totalBase - (totalBase * (desc / 100));
            document.getElementById('total_display').innerText = '$' + nuevoTotal.toFixed(2);
        }
    </script>
</div>
