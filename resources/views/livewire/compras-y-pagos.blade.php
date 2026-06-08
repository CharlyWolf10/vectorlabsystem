<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Compras y Cuentas por Pagar') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-gray-800">Módulo de Compras y Cuentas por Pagar</h2>
        <div>
            <button wire:click="attemptExport" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow">
                <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
            </button>
        </div>
    </div>



    <!-- Panel de Cuentas por Pagar -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center border-b pb-2 mb-4">
            <h3 class="text-lg font-semibold text-red-600">Cuentas por Pagar Activas</h3>
            <button onclick="nuevaCuentaPorPagar()" class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-1 px-3 rounded text-sm shadow">
                <i class="fas fa-file-invoice-dollar mr-1"></i> Añadir Deuda Manual
            </button>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($cuentasPorPagar as $cuenta)
            <div class="border rounded p-4 shadow-sm relative">
                <div class="absolute top-2 right-2 flex space-x-2">
                    <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">
                        {{ strtoupper($cuenta->estado) }}
                    </span>
                    <button onclick="eliminarCuenta({{ $cuenta->id }})" class="text-red-500 hover:text-red-700 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
                <h4 class="font-bold text-gray-800">{{ $cuenta->proveedor->nombre }}</h4>
                <p class="text-sm text-gray-600 mt-1">Deuda Original: ${{ number_format($cuenta->monto_total, 2) }}</p>
                <p class="text-lg font-bold text-red-500 mt-2">Saldo: ${{ number_format($cuenta->saldo_pendiente, 2) }}</p>
                <div class="mt-4">
                    <button onclick="confirmarAbono({{ $cuenta->id }}, '{{ $cuenta->proveedor->nombre }}', {{ $cuenta->saldo_pendiente }})" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 rounded">
                        Registrar Abono/Pago
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full py-4 text-center text-gray-500">
                No hay deudas pendientes registradas en este momento.
            </div>
            @endforelse
        </div>
    </div>

    <!-- Script para SweetAlert2 -->
    <script>
        // Inyectar datos de proveedores para usar en la creación de Cuentas por Pagar (JS externo)
        window.comprasProveedores = @json($proveedores);
    </script>
    <script src="{{ asset('js/compras-y-pagos.js') }}"></script>
        </div>
    </div>
</div>
