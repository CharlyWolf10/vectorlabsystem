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

            <!-- Resumen del Día Actual (Si hay caja abierta) -->
            @if($arqueoActivo)
            <div class="bg-white rounded-lg shadow-md p-6 mb-8 border border-gray-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2"><i class="fas fa-chart-pie text-blue-500 mr-2"></i> Resumen del Turno Actual</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-blue-50 rounded p-4">
                        <h4 class="font-bold text-blue-800 mb-2 border-b border-blue-200 pb-1">Ingresos (Ventas)</h4>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">Efectivo:</span> <strong>$ {{ number_format($ventasEfectivo, 2) }}</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">Tarjeta:</span> <strong>$ {{ number_format($ventasTarjeta, 2) }}</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">Transferencia:</span> <strong>$ {{ number_format($ventasTransferencia, 2) }}</strong></div>
                        <div class="flex justify-between text-base font-bold mt-2 pt-2 border-t border-blue-200"><span class="text-blue-900">Total:</span> <span class="text-blue-900">$ {{ number_format($totalVentasHoy, 2) }}</span></div>
                    </div>
                    <div class="bg-red-50 rounded p-4">
                        <h4 class="font-bold text-red-800 mb-2 border-b border-red-200 pb-1">Egresos (Compras/Gastos)</h4>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">Efectivo:</span> <strong>$ {{ number_format($comprasEfectivo, 2) }}</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">Tarjeta:</span> <strong>$ {{ number_format($comprasTarjeta, 2) }}</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">Transferencia:</span> <strong>$ {{ number_format($comprasTransferencia, 2) }}</strong></div>
                        <div class="flex justify-between text-base font-bold mt-2 pt-2 border-t border-red-200"><span class="text-red-900">Total:</span> <span class="text-red-900">$ {{ number_format($totalComprasHoy, 2) }}</span></div>
                    </div>
                    <div class="bg-green-50 rounded p-4">
                        <h4 class="font-bold text-green-800 mb-2 border-b border-green-200 pb-1">Caja (Efectivo Físico Esperado)</h4>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">Fondo Inicial:</span> <strong>$ {{ number_format($arqueoActivo->fondo_inicial, 2) }}</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">+ Ventas Efectivo:</span> <strong class="text-green-600">$ {{ number_format($ventasEfectivo, 2) }}</strong></div>
                        <div class="flex justify-between text-sm"><span class="text-gray-600">- Gastos Efectivo:</span> <strong class="text-red-600">$ {{ number_format($comprasEfectivo, 2) }}</strong></div>
                        <div class="flex justify-between text-xl font-bold mt-2 pt-2 border-t border-green-200"><span class="text-green-900">Esperado:</span> <span class="text-green-900">$ {{ number_format($arqueoActivo->fondo_inicial + $ventasEfectivo - $comprasEfectivo, 2) }}</span></div>
                    </div>
                </div>
            </div>
            @endif

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

    <script src="{{ asset('js/arqueos.js') }}"></script>
</div>
