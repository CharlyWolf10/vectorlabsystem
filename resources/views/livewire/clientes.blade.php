<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clientes (CRM)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-wrap gap-2 justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Directorio de Clientes</h2>
                <div class="flex flex-wrap gap-2 mt-2 md:mt-0">
                    <button onclick="nuevoCliente()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                    </button>
                    <button wire:click="attemptExport" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
                    </button>
                    <button wire:click="abrirModalEmail" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-envelope mr-2"></i> Campaña de Email
                    </button>
                    <button wire:click="abrirModalWhatsapp" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fab fa-whatsapp mr-2"></i> Promoción WhatsApp
                    </button>
                </div>
            </div>

            <!-- Panel de Clientes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4 flex flex-col justify-between items-start gap-4 border-b pb-4">
                    <div class="w-full flex flex-col md:flex-row gap-4 items-center">
                        <div class="w-full md:w-1/3">
                            <input type="text" wire:model.live="search" placeholder="Buscar por nombre, correo, etc..." class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:border-blue-500 focus:ring focus:ring-blue-200">
                        </div>
                        <div class="w-full md:w-2/3 flex flex-wrap gap-2 items-center">
                            <select wire:model.live="filterEstudiante" class="border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
                                <option value="">¿Es estudiante? (Todos)</option>
                                <option value="1">Sí (Estudiantes)</option>
                                <option value="0">No (Profesionistas/Otros)</option>
                            </select>
                            <select wire:model.live="filterEscuela" class="border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
                                <option value="">Filtrar por Universidad (Todas)</option>
                                <option value="UDLAP">UDLAP</option>
                                <option value="UVM">UVM</option>
                                <option value="UAMP">UAMP</option>
                                <option value="Tec de Monterrey">Tec de Monterrey</option>
                                <option value="UNARTE">UNARTE</option>
                                <option value="BUAP">BUAP</option>
                                <option value="UPAEP">UPAEP</option>
                            </select>
                            <select wire:model.live="filterProfesionista" class="border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
                                <option value="">¿Es profesionista? (Todos)</option>
                                <option value="1">Sí (Profesionistas)</option>
                                <option value="0">No (Estudiantes/Otros)</option>
                            </select>
                            <input type="text" wire:model.live="filterEmpresa" placeholder="Filtrar por Empresa" class="border border-gray-300 rounded-md shadow-sm px-3 py-2 text-sm">
                        </div>
                    </div>
                    @if(count($selectedClientes) > 0)
                        <div class="text-sm font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded">
                            {{ count($selectedClientes) }} cliente(s) seleccionado(s)
                        </div>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-2 px-4 text-center w-12"><input type="checkbox" wire:model.live="selectAll" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></th>
                                <th class="py-2 px-4 text-left">Nombre Completo</th>
                                <th class="py-2 px-4 text-left">Contacto</th>
                                <th class="py-2 px-4 text-right">Límite Crédito</th>
                                <th class="py-2 px-4 text-right">Saldo Deudor</th>
                                <th class="py-2 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4 text-center">
                                    <input type="checkbox" value="{{ $cliente->id }}" wire:model.live="selectedClientes" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                                <td class="py-2 px-4">
                                    <span class="font-bold">{{ $cliente->nombre }} {{ $cliente->apellidos }}</span>
                                    @if($cliente->es_estudiante)
                                        <br><span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded mt-1 inline-block">Estudiante: {{ $cliente->matricula }}</span>
                                        @if($cliente->escuela)
                                            <br><span class="text-xs text-gray-500"><i class="fas fa-university"></i> {{ $cliente->escuela }}</span>
                                        @endif
                                    @endif
                                    @if($cliente->es_profesionista)
                                        <br><span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded mt-1 inline-block">Profesionista</span>
                                        @if($cliente->empresa)
                                            <br><span class="text-xs text-gray-500"><i class="fas fa-briefcase"></i> {{ $cliente->empresa }}</span>
                                        @endif
                                        @if($cliente->rfc)
                                            <br><span class="text-xs text-gray-500 font-mono">RFC: {{ $cliente->rfc }}</span>
                                        @endif
                                        @if($cliente->constancia_fiscal)
                                            <br><a href="{{ asset('storage/' . $cliente->constancia_fiscal) }}" target="_blank" class="text-xs text-blue-500 hover:underline"><i class="fas fa-file-pdf"></i> Constancia</a>
                                        @endif
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-sm">{{ $cliente->telefono }} <br> <span class="text-gray-500">{{ $cliente->email }}</span></td>
                                <td class="py-2 px-4 text-right">${{ number_format($cliente->limite_credito, 2) }}</td>
                                <td class="py-2 px-4 text-right text-red-600 font-bold">${{ number_format($cliente->saldo_pendiente, 2) }}</td>
                                <td class="py-2 px-4 text-center">
                                    <button onclick="editarCliente({{ $cliente->id }}, '{{ addslashes($cliente->nombre) }}', '{{ addslashes($cliente->apellidos) }}', {{ $cliente->es_estudiante ? 'true' : 'false' }}, '{{ $cliente->matricula }}', '{{ $cliente->escuela }}', '{{ $cliente->telefono }}', '{{ $cliente->email }}', {{ $cliente->limite_credito ?? 0 }}, {{ $cliente->es_profesionista ? 'true' : 'false' }}, '{{ addslashes($cliente->empresa) }}', '{{ $cliente->rfc }}')" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>
                                    <button onclick="eliminarCliente({{ $cliente->id }})" class="text-red-500 hover:text-red-700"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-4 text-center text-gray-500">No hay clientes registrados en la base de datos.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Panel de Cuentas por Cobrar -->
            <div class="bg-white rounded-lg shadow-md p-6 mt-6">
                <div class="flex justify-between items-center border-b pb-2 mb-4">
                    <h3 class="text-lg font-semibold text-red-600">Cuentas por Cobrar Activas (Crédito a Clientes)</h3>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($cuentasPorCobrar as $cuenta)
                    <div class="border rounded p-4 shadow-sm relative">
                        <div class="absolute top-2 right-2 flex space-x-2">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">
                                {{ strtoupper($cuenta->estado) }}
                            </span>
                            <button onclick="eliminarCuenta({{ $cuenta->id }})" class="text-red-500 hover:text-red-700 bg-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                        <h4 class="font-bold text-gray-800">{{ $cuenta->cliente->nombre }} {{ $cuenta->cliente->apellidos }}</h4>
                        <p class="text-sm text-gray-600 mt-1">Crédito Original: ${{ number_format($cuenta->monto_total, 2) }}</p>
                        <p class="text-lg font-bold text-red-500 mt-2">Saldo a Cobrar: ${{ number_format($cuenta->saldo_pendiente, 2) }}</p>
                        <div class="mt-4">
                            <button onclick="confirmarAbono({{ $cuenta->id }}, '{{ $cuenta->cliente->nombre }}', {{ $cuenta->saldo_pendiente }})" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-2 rounded">
                                Registrar Abono/Cobro
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full py-4 text-center text-gray-500">
                        No hay créditos pendientes registrados.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/clientes.js') }}"></script>
</div>
