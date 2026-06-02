<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Arqueo y Corte de Caja') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                
                <!-- Abrir Caja -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-vl-blue">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Apertura de Caja</h3>
                    <p class="text-gray-600 mb-4">Inicia tu turno de caja registrando el fondo base en efectivo.</p>
                    <button onclick="abrirCaja()" class="w-full bg-vl-blue hover:bg-blue-700 text-white font-bold py-3 rounded shadow">
                        <i class="fas fa-lock-open mr-2"></i> Abrir Caja
                    </button>
                </div>

                <!-- Cerrar Caja -->
                <div class="bg-white rounded-lg shadow-md p-6 border-t-4 border-red-500">
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Corte y Arqueo (Cierre)</h3>
                    <p class="text-gray-600 mb-4">Cierra tu turno y registra el conteo físico de dinero.</p>
                    <button onclick="cerrarCaja()" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-3 rounded shadow">
                        <i class="fas fa-cash-register mr-2"></i> Realizar Corte de Caja
                    </button>
                </div>

            </div>

            <!-- Historial de Arqueos -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Historial de Turnos y Cortes</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-2 px-4 text-left">Fecha</th>
                                <th class="py-2 px-4 text-left">Cajero</th>
                                <th class="py-2 px-4 text-right">Fondo Inicial</th>
                                <th class="py-2 px-4 text-right">Sistema (Total)</th>
                                <th class="py-2 px-4 text-right">Físico (Conteo)</th>
                                <th class="py-2 px-4 text-right">Diferencia</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($arqueos as $arqueo)
                            <tr class="border-b">
                                <td class="py-2 px-4">{{ $arqueo->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-2 px-4 font-bold">{{ optional($arqueo->user)->name ?? 'Desconocido' }}</td>
                                <td class="py-2 px-4 text-right">${{ number_format($arqueo->fondo_inicial, 2) }}</td>
                                <td class="py-2 px-4 text-right">${{ number_format($arqueo->total_registrado_sistema, 2) }}</td>
                                <td class="py-2 px-4 text-right font-bold text-blue-600">${{ number_format($arqueo->total_calculado, 2) }}</td>
                                <td class="py-2 px-4 text-right">
                                    @if($arqueo->diferencia == 0)
                                        <span class="text-green-600 font-bold">Cuadrado <i class="fas fa-check-circle"></i></span>
                                    @elseif($arqueo->diferencia > 0)
                                        <span class="text-blue-500 font-bold">+${{ number_format($arqueo->diferencia, 2) }} (Sobra)</span>
                                    @else
                                        <span class="text-red-500 font-bold">-${{ number_format(abs($arqueo->diferencia), 2) }} (Falta)</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">No hay arqueos registrados.</td>
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

        function abrirCaja() {
            Swal.fire({
                title: 'Apertura de Caja',
                text: 'Ingresa el fondo inicial de cambio:',
                input: 'number',
                inputAttributes: {
                    min: 0,
                    step: 0.01
                },
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                preConfirm: (value) => {
                    if(!value || value < 0) {
                        Swal.showValidationMessage('Debes ingresar un fondo inicial válido (0 o mayor)');
                    }
                    return value;
                }
            }).then((result) => {
                if(result.isConfirmed) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Se registrará este monto como tu fondo inicial del turno.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0066ff',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, abrir caja'
                    }).then((confirmResult) => {
                        if(confirmResult.isConfirmed){
                            Livewire.dispatch('abrirCaja', [parseFloat(result.value)]);
                        }
                    });
                }
            });
        }

        function cerrarCaja() {
            Swal.fire({
                title: 'Conteo Físico de Billetes y Monedas',
                width: 600,
                html: `
                    <div class="grid grid-cols-2 gap-4 text-left">
                        <div>
                            <h4 class="font-bold text-gray-700 mb-2">Billetes</h4>
                            <div class="flex justify-between items-center mb-1"><label>$500</label> <input id="b_500" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$200</label> <input id="b_200" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$100</label> <input id="b_100" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$50</label> <input id="b_50" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-700 mb-2">Monedas</h4>
                            <div class="flex justify-between items-center mb-1"><label>$20</label> <input id="m_20" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$10</label> <input id="m_10" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$5</label> <input id="m_5" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$2</label> <input id="m_2" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$1</label> <input id="m_1" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                            <div class="flex justify-between items-center mb-1"><label>$0.50</label> <input id="m_50c" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-300 text-right">
                        <span class="text-xl">Total Físico: </span>
                        <span class="text-2xl font-bold text-green-600" id="total_arqueo">$0.00</span>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Continuar con el Cierre',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const b500 = parseInt(document.getElementById('b_500').value) || 0;
                    const b200 = parseInt(document.getElementById('b_200').value) || 0;
                    const b100 = parseInt(document.getElementById('b_100').value) || 0;
                    const b50 = parseInt(document.getElementById('b_50').value) || 0;
                    const m20 = parseInt(document.getElementById('m_20').value) || 0;
                    const m10 = parseInt(document.getElementById('m_10').value) || 0;
                    const m5 = parseInt(document.getElementById('m_5').value) || 0;
                    const m2 = parseInt(document.getElementById('m_2').value) || 0;
                    const m1 = parseInt(document.getElementById('m_1').value) || 0;
                    const m50c = parseInt(document.getElementById('m_50c').value) || 0;
                    
                    const total = (b500 * 500) + (b200 * 200) + (b100 * 100) + (b50 * 50) +
                                  (m20 * 20) + (m10 * 10) + (m5 * 5) + (m2 * 2) + (m1 * 1) + (m50c * 0.5);

                    return {
                        b_500: b500, b_200: b200, b_100: b100, b_50: b50,
                        m_20: m20, m_10: m10, m_5: m5, m_2: m2, m_1: m1, m_50c: m50c,
                        total: total
                    }
                }
            }).then((result) => {
                if(result.isConfirmed) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "Se cerrará el turno y se registrará este arqueo en la base de datos.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0066ff',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, cerrar caja'
                    }).then((confirmResult) => {
                        if(confirmResult.isConfirmed){
                            Livewire.dispatch('cerrarCaja', [result.value]);
                        }
                    });
                }
            });
        }

        // Available globally for the modal
        window.calcTotal = function() {
            const b500 = parseInt(document.getElementById('b_500').value) || 0;
            const b200 = parseInt(document.getElementById('b_200').value) || 0;
            const b100 = parseInt(document.getElementById('b_100').value) || 0;
            const b50 = parseInt(document.getElementById('b_50').value) || 0;
            const m20 = parseInt(document.getElementById('m_20').value) || 0;
            const m10 = parseInt(document.getElementById('m_10').value) || 0;
            const m5 = parseInt(document.getElementById('m_5').value) || 0;
            const m2 = parseInt(document.getElementById('m_2').value) || 0;
            const m1 = parseInt(document.getElementById('m_1').value) || 0;
            const m50c = parseInt(document.getElementById('m_50c').value) || 0;
            
            const total = (b500 * 500) + (b200 * 200) + (b100 * 100) + (b50 * 50) +
                          (m20 * 20) + (m10 * 10) + (m5 * 5) + (m2 * 2) + (m1 * 1) + (m50c * 0.5);
            
            document.getElementById('total_arqueo').innerText = "$" + total.toFixed(2);
        }
    </script>
</div>
