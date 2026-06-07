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
            <button onclick="nuevoProveedor()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mr-2">
                <i class="fas fa-plus mr-2"></i> Nuevo Proveedor
            </button>
            <button wire:click="attemptExport" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow">
                <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
            </button>
        </div>
    </div>

    <!-- Panel de Proveedores -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex flex-col md:flex-row justify-between items-center border-b pb-2 mb-4 gap-4">
            <h3 class="text-lg font-semibold w-full md:w-1/3">Directorio de Proveedores</h3>
            <div class="w-full md:w-1/3">
                <input type="text" wire:model.live="searchProveedores" placeholder="Buscar proveedor..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
            </div>
            @if(count($selectedProveedores) > 0)
                <div class="text-sm font-semibold text-blue-600">
                    {{ count($selectedProveedores) }} seleccionado(s)
                </div>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="py-2 px-4 text-center w-12"><input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></th>
                        <th class="py-2 px-4 text-left">Proveedor</th>
                        <th class="py-2 px-4 text-left">Datos Bancarios</th>
                        <th class="py-2 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-4 text-center">
                            <input type="checkbox" value="{{ $proveedor->id }}" wire:model="selectedProveedores" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </td>
                        <td class="py-2 px-4">
                            <div class="font-bold">{{ $proveedor->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ $proveedor->telefono }} | {{ $proveedor->email }}</div>
                        </td>
                        <td class="py-2 px-4 text-sm">
                            <span class="font-semibold">{{ $proveedor->banco }}</span><br>
                            @if($proveedor->titular_cuenta) Titular: {{ $proveedor->titular_cuenta }}<br> @endif
                            Cuenta: {{ $proveedor->num_cuenta }}<br>
                            CLABE: {{ $proveedor->clabe }}
                        </td>
                        <td class="py-2 px-4 text-center">
                            <button onclick="nuevoGasto({{ $proveedor->id }})" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-3 rounded block w-full mb-1">
                                Registrar Gasto
                            </button>
                            <button onclick="editarProveedor({{ $proveedor->id }}, '{{ addslashes($proveedor->nombre) }}', '{{ $proveedor->telefono }}', '{{ $proveedor->email }}', '{{ $proveedor->direccion }}', '{{ $proveedor->rfc }}', '{{ $proveedor->banco }}', '{{ $proveedor->clabe }}', '{{ $proveedor->num_cuenta }}', '{{ addslashes($proveedor->titular_cuenta) }}')" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>
                            <button onclick="eliminarProveedor({{ $proveedor->id }})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-4 text-center text-gray-500">No hay proveedores registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
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
