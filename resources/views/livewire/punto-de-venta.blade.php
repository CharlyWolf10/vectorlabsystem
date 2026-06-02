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

        window.addEventListener('swal:ticket', event => {
            const t = event.detail[0];
            let ticketHtml = `
                <div class="text-left font-mono text-sm">
                    <p class="text-center font-bold text-xl mb-2">VECTOR LAB</p>
                    <p class="text-center mb-4">Ticket #${t.id} - ${t.fecha}</p>
                    <hr class="border-dashed my-2 border-gray-400">
                    <p>Subtotal: <span class="float-right">$${parseFloat(t.subtotal).toFixed(2)}</span></p>
                    <p>Descuento (${t.descuento_porcentaje}%): <span class="float-right text-red-500">-$${parseFloat(t.descuento_monto).toFixed(2)}</span></p>
                    <p class="font-bold text-lg mt-2">TOTAL: <span class="float-right">$${parseFloat(t.total).toFixed(2)}</span></p>
                    <hr class="border-dashed my-2 border-gray-400">
                    <p>Método de Pago: <span class="float-right uppercase">${t.metodo.replace('_', ' ')}</span></p>
            `;
            
            if (t.metodo === 'efectivo') {
                ticketHtml += `
                    <p>Pagó con: <span class="float-right">$${parseFloat(t.pago_con).toFixed(2)}</span></p>
                    <p>Cambio: <span class="float-right">$${parseFloat(t.cambio).toFixed(2)}</span></p>
                `;
            }

            ticketHtml += `
                    <p>Requiere Factura: <span class="float-right">${t.factura ? 'SÍ' : 'NO'}</span></p>
                    <hr class="border-dashed my-2 border-gray-400">
                    <p class="text-center mt-4">¡Gracias por su compra!</p>
                </div>
            `;

            Swal.fire({
                title: 'Venta Registrada',
                html: ticketHtml,
                icon: 'success',
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#10b981'
            });
        });

        function cobrar(total) {
            if (total <= 0) return;
            
            Swal.fire({
                title: 'Cobrar Venta',
                html: `
                    <h2 id="total_display" class="text-3xl text-green-600 font-bold mb-4" data-total="${total}">$${total.toFixed(2)}</h2>
                    <select id="metodo_cobro" class="swal2-select w-full mb-4" onchange="toggleEfectivo()">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta_credito">Tarjeta de Crédito</option>
                        <option value="tarjeta_debito">Tarjeta de Débito</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="credito">Crédito a Cliente</option>
                    </select>
                    
                    <select id="descuento" class="swal2-select w-full mb-4" onchange="actualizarTotalCobro()">
                        <option value="0">Sin Descuento (0%)</option>
                        <option value="5">Descuento 5%</option>
                        <option value="10">Descuento 10%</option>
                        <option value="15">Descuento 15%</option>
                        <option value="20">Descuento 20%</option>
                    </select>

                    <div id="efectivo_fields">
                        <input id="pago_con" type="number" step="0.01" class="swal2-input w-full" placeholder="Pagó con (Ej: 500)" onkeyup="calcularCambio()">
                        <p class="text-right text-gray-600 font-bold mt-2 text-lg">Cambio: <span id="cambio_display" class="text-blue-600">$0.00</span></p>
                    </div>

                    <div class="mt-4 text-left border-t pt-4">
                        <label class="flex items-center space-x-2 cursor-pointer font-bold">
                            <input type="checkbox" id="req_factura" class="form-checkbox text-blue-600 w-5 h-5">
                            <span>¿Requiere Factura?</span>
                        </label>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Confirmar Pago',
                confirmButtonColor: '#10b981',
                cancelButtonText: 'Cancelar',
                didOpen: () => { toggleEfectivo(); },
                preConfirm: () => {
                    const metodo = document.getElementById('metodo_cobro').value;
                    const clienteId = document.getElementById('select_cliente_venta').value;
                    const descuento = document.getElementById('descuento').value;
                    const reqFactura = document.getElementById('req_factura').checked;
                    const pagoCon = document.getElementById('pago_con').value;
                    
                    const totalBase = parseFloat(document.getElementById('total_display').getAttribute('data-total'));
                    const descMonto = totalBase * (descuento / 100);
                    const totalFinal = totalBase - descMonto;
                    
                    const pagoConFloat = parseFloat(pagoCon) || 0;
                    let cambio = 0;

                    if ((metodo === 'credito' || reqFactura) && !clienteId) {
                        Swal.showValidationMessage('Debe seleccionar un cliente del panel derecho para Crédito o Factura');
                        return false;
                    }

                    if (metodo === 'efectivo') {
                        if (pagoConFloat < totalFinal) {
                            Swal.showValidationMessage('El monto "Pagó con" debe ser mayor o igual al total');
                            return false;
                        }
                        cambio = pagoConFloat - totalFinal;
                    }

                    return { 
                        metodo: metodo, 
                        clienteId: clienteId, 
                        descuento: descuento,
                        requiere_factura: reqFactura,
                        pago_con: pagoConFloat,
                        cambio: cambio
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('registrarVenta', [result.value]);
                }
            });
        }

        function toggleEfectivo() {
            const metodo = document.getElementById('metodo_cobro').value;
            const fields = document.getElementById('efectivo_fields');
            if (metodo === 'efectivo') {
                fields.style.display = 'block';
            } else {
                fields.style.display = 'none';
                document.getElementById('pago_con').value = '';
                document.getElementById('cambio_display').innerText = '$0.00';
            }
        }

        function actualizarTotalCobro() {
            const totalBase = parseFloat(document.getElementById('total_display').getAttribute('data-total'));
            const desc = document.getElementById('descuento').value;
            const nuevoTotal = totalBase - (totalBase * (desc / 100));
            document.getElementById('total_display').innerText = '$' + nuevoTotal.toFixed(2);
            calcularCambio();
        }

        function calcularCambio() {
            const totalBase = parseFloat(document.getElementById('total_display').getAttribute('data-total'));
            const desc = document.getElementById('descuento').value;
            const totalFinal = totalBase - (totalBase * (desc / 100));
            
            const pagoCon = parseFloat(document.getElementById('pago_con').value) || 0;
            const display = document.getElementById('cambio_display');
            
            if (pagoCon >= totalFinal) {
                const cambio = pagoCon - totalFinal;
                display.innerText = '$' + cambio.toFixed(2);
                display.classList.remove('text-red-600');
                display.classList.add('text-blue-600');
            } else {
                display.innerText = 'Faltan $' + (totalFinal - pagoCon).toFixed(2);
                display.classList.remove('text-blue-600');
                display.classList.add('text-red-600');
            }
        }
    </script>
</div>
