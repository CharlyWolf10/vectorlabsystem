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

    <script>
        window.addEventListener('swal:success', event => {
            Swal.fire({
                icon: 'success',
                title: event.detail[0].title,
                text: event.detail[0].text,
            });
        });
        
        window.addEventListener('swal:error', event => {
            Swal.fire({
                icon: 'error',
                title: event.detail[0].title,
                text: event.detail[0].text,
            });
        });

        function nuevoCliente() {
            Swal.fire({
                title: 'Nuevo Cliente',
                width: '900px',
                html: `
                    <div class="flex flex-col gap-4 text-left mt-4">
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre(s)</label>
                            <input id="cli_nombre" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre(s)" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Apellidos</label>
                            <input id="cli_apellidos" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Apellidos" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Teléfono</label>
                            <input id="cli_telefono" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Teléfono">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Correo Electrónico</label>
                            <input id="cli_email" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Correo Electrónico">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Límite Crédito $</label>
                            <input id="cli_limite" type="number" step="0.01" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Límite de Crédito">
                        </div>
                        <div class="flex items-center space-x-6 pt-2 pb-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" id="cli_estudiante" onchange="toggleTipos()" class="form-checkbox text-blue-600 w-5 h-5 rounded">
                                <span class="font-bold text-gray-700">¿Estudiante?</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" id="cli_profesionista" onchange="toggleTipos()" class="form-checkbox text-blue-600 w-5 h-5 rounded">
                                <span class="font-bold text-gray-700">¿Profesionista?</span>
                            </label>
                        </div>
                        
                        <!-- Campos Estudiante -->
                        <div id="estudiante_fields" class="flex flex-col gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100" style="display:none;">
                            <div>
                                <label class="text-sm text-blue-800 font-bold mb-1 block">Matrícula</label>
                                <input id="cli_matricula" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Matrícula">
                            </div>
                            <div>
                                <label class="text-sm text-blue-800 font-bold mb-1 block">Universidad/Escuela</label>
                                <select id="cli_escuela" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" onchange="toggleOtraEscuela()">
                                    <option value="">Seleccione una Escuela</option>
                                    <option value="UDLAP">UDLAP</option>
                                    <option value="UVM">UVM</option>
                                    <option value="UAMP">UAMP</option>
                                    <option value="Tec de Monterrey">Tec de Monterrey</option>
                                    <option value="UNARTE">UNARTE</option>
                                    <option value="BUAP">BUAP</option>
                                    <option value="UPAEP">UPAEP</option>
                                    <option value="Otra">Otra (Especificar)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-blue-800 font-bold mb-1 block invisible" id="lbl_otra_escuela">Otra Escuela</label>
                                <input id="cli_otra_escuela" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Especifique la escuela" style="display:none;">
                            </div>
                        </div>

                        <!-- Campos Profesionista -->
                        <div id="profesionista_fields" class="flex flex-col gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200" style="display:none;">
                            <div>
                                <label class="text-sm text-gray-800 font-bold mb-1 block">Empresa / Trabajo</label>
                                <input id="cli_empresa" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-gray-500 focus:border-gray-500" placeholder="Nombre de empresa o 'Independiente'">
                            </div>
                            <div>
                                <label class="text-sm text-gray-800 font-bold mb-1 block">RFC</label>
                                <input id="cli_rfc" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-gray-500 focus:border-gray-500" placeholder="RFC">
                            </div>
                            <div>
                                <label class="text-sm text-gray-800 font-bold mb-1 block">Constancia Fiscal (Opcional)</label>
                                <input type="file" id="cli_constancia" accept=".pdf,image/*" class="w-full text-sm mt-1">
                            </div>
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('cli_nombre').value;
                    const apellidos = document.getElementById('cli_apellidos').value;
                    if(!nombre || !apellidos) {
                        Swal.showValidationMessage('Nombre y apellidos son obligatorios');
                        return false;
                    }
                    let escuelaVal = document.getElementById('cli_escuela').value;
                    if(escuelaVal === 'Otra') {
                        escuelaVal = document.getElementById('cli_otra_escuela').value;
                    }

                    const isEstudiante = document.getElementById('cli_estudiante').checked;
                    const isProfesionista = document.getElementById('cli_profesionista').checked;
                    let constanciaFile = document.getElementById('cli_constancia').files[0];

                    return new Promise((resolve) => {
                        let data = {
                            nombre: nombre,
                            apellidos: apellidos,
                            es_estudiante: isEstudiante,
                            matricula: isEstudiante ? document.getElementById('cli_matricula').value : null,
                            escuela: isEstudiante ? escuelaVal : null,
                            es_profesionista: isProfesionista,
                            empresa: isProfesionista ? document.getElementById('cli_empresa').value : null,
                            rfc: isProfesionista ? document.getElementById('cli_rfc').value : null,
                            telefono: document.getElementById('cli_telefono').value,
                            email: document.getElementById('cli_email').value,
                            limite_credito: document.getElementById('cli_limite').value || 0,
                            constancia_base64: null
                        };

                        if (constanciaFile && isProfesionista) {
                            let reader = new FileReader();
                            reader.onload = function(e) {
                                data.constancia_base64 = e.target.result;
                                resolve(data);
                            };
                            reader.readAsDataURL(constanciaFile);
                        } else {
                            resolve(data);
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('guardarCliente', [result.value]);
                }
            });
        }

        function editarCliente(id, nombre, apellidos, es_estudiante, matricula, escuela, telefono, email, limite, es_profesionista, empresa, rfc) {
            Swal.fire({
                title: 'Editar Cliente',
                width: '900px',
                html: `
                    <input id="cli_id" type="hidden" value="${id}">
                    <div class="flex flex-col gap-4 text-left mt-4">
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre(s)</label>
                            <input id="cli_nombre" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre(s)" value="${nombre}" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Apellidos</label>
                            <input id="cli_apellidos" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Apellidos" value="${apellidos}" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Teléfono</label>
                            <input id="cli_telefono" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Teléfono" value="${telefono}">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Correo Electrónico</label>
                            <input id="cli_email" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Correo Electrónico" value="${email}">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Límite Crédito $</label>
                            <input id="cli_limite" type="number" step="0.01" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Límite de Crédito" value="${limite}">
                        </div>
                        <div class="flex items-center space-x-6 pt-2 pb-2">
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" id="cli_estudiante" onchange="toggleTipos()" class="form-checkbox text-blue-600 w-5 h-5 rounded" ${es_estudiante ? 'checked' : ''}>
                                <span class="font-bold text-gray-700">¿Estudiante?</span>
                            </label>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" id="cli_profesionista" onchange="toggleTipos()" class="form-checkbox text-blue-600 w-5 h-5 rounded" ${es_profesionista ? 'checked' : ''}>
                                <span class="font-bold text-gray-700">¿Profesionista?</span>
                            </label>
                        </div>
                        
                        <!-- Campos Estudiante -->
                        <div id="estudiante_fields" class="flex flex-col gap-4 p-4 bg-blue-50 rounded-lg border border-blue-100" style="display:${es_estudiante ? 'flex' : 'none'};">
                            <div>
                                <label class="text-sm text-blue-800 font-bold mb-1 block">Matrícula</label>
                                <input id="cli_matricula" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Matrícula" value="${matricula}">
                            </div>
                            <div>
                                <label class="text-sm text-blue-800 font-bold mb-1 block">Universidad/Escuela</label>
                                <select id="cli_escuela" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" onchange="toggleOtraEscuela()">
                                    <option value="">Seleccione una Escuela</option>
                                    <option value="UDLAP" ${escuela == 'UDLAP' ? 'selected' : ''}>UDLAP</option>
                                    <option value="UVM" ${escuela == 'UVM' ? 'selected' : ''}>UVM</option>
                                    <option value="UAMP" ${escuela == 'UAMP' ? 'selected' : ''}>UAMP</option>
                                    <option value="Tec de Monterrey" ${escuela == 'Tec de Monterrey' ? 'selected' : ''}>Tec de Monterrey</option>
                                    <option value="UNARTE" ${escuela == 'UNARTE' ? 'selected' : ''}>UNARTE</option>
                                    <option value="BUAP" ${escuela == 'BUAP' ? 'selected' : ''}>BUAP</option>
                                    <option value="UPAEP" ${escuela == 'UPAEP' ? 'selected' : ''}>UPAEP</option>
                                    <option value="Otra" ${!['UDLAP','UVM','UAMP','Tec de Monterrey','UNARTE','BUAP','UPAEP',''].includes(escuela) ? 'selected' : ''}>Otra (Especificar)</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-sm text-blue-800 font-bold mb-1 block ${!['UDLAP','UVM','UAMP','Tec de Monterrey','UNARTE','BUAP','UPAEP',''].includes(escuela) ? '' : 'invisible'}" id="lbl_otra_escuela">Otra Escuela</label>
                                <input id="cli_otra_escuela" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Especifique la escuela" value="${!['UDLAP','UVM','UAMP','Tec de Monterrey','UNARTE','BUAP','UPAEP',''].includes(escuela) ? escuela : ''}" style="display:${!['UDLAP','UVM','UAMP','Tec de Monterrey','UNARTE','BUAP','UPAEP',''].includes(escuela) ? 'block' : 'none'};">
                            </div>
                        </div>

                        <!-- Campos Profesionista -->
                        <div id="profesionista_fields" class="flex flex-col gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200" style="display:${es_profesionista ? 'flex' : 'none'};">
                            <div>
                                <label class="text-sm text-gray-800 font-bold mb-1 block">Empresa / Trabajo</label>
                                <input id="cli_empresa" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-gray-500 focus:border-gray-500" placeholder="Nombre de empresa o 'Independiente'" value="${empresa}">
                            </div>
                            <div>
                                <label class="text-sm text-gray-800 font-bold mb-1 block">RFC</label>
                                <input id="cli_rfc" oninput="this.value = this.value.toUpperCase()" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-gray-500 focus:border-gray-500" placeholder="RFC" value="${rfc}">
                            </div>
                            <div>
                                <label class="text-sm text-gray-800 font-bold mb-1 block">Reemplazar Constancia (Opcional)</label>
                                <input type="file" id="cli_constancia" accept=".pdf,image/*" class="w-full text-sm mt-1">
                            </div>
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('cli_nombre').value;
                    const apellidos = document.getElementById('cli_apellidos').value;
                    if(!nombre || !apellidos) {
                        Swal.showValidationMessage('Nombre y apellidos son obligatorios');
                        return false;
                    }
                    let escuelaVal = document.getElementById('cli_escuela').value;
                    if(escuelaVal === 'Otra') {
                        escuelaVal = document.getElementById('cli_otra_escuela').value;
                    }

                    const isEstudiante = document.getElementById('cli_estudiante').checked;
                    const isProfesionista = document.getElementById('cli_profesionista').checked;
                    let constanciaFile = document.getElementById('cli_constancia').files[0];

                    return new Promise((resolve) => {
                        let data = {
                            id: document.getElementById('cli_id').value,
                            nombre: nombre,
                            apellidos: apellidos,
                            es_estudiante: isEstudiante,
                            matricula: isEstudiante ? document.getElementById('cli_matricula').value : null,
                            escuela: isEstudiante ? escuelaVal : null,
                            es_profesionista: isProfesionista,
                            empresa: isProfesionista ? document.getElementById('cli_empresa').value : null,
                            rfc: isProfesionista ? document.getElementById('cli_rfc').value : null,
                            telefono: document.getElementById('cli_telefono').value,
                            email: document.getElementById('cli_email').value,
                            limite_credito: document.getElementById('cli_limite').value || 0,
                            constancia_base64: null
                        };

                        if (constanciaFile && isProfesionista) {
                            let reader = new FileReader();
                            reader.onload = function(e) {
                                data.constancia_base64 = e.target.result;
                                resolve(data);
                            };
                            reader.readAsDataURL(constanciaFile);
                        } else {
                            resolve(data);
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('guardarCliente', [result.value]);
                }
            });
        }

        function confirmarAbono(cuentaId, cliente, saldoPendiente) {
            Swal.fire({
                title: `Cobrar Abono a ${cliente}`,
                html: `
                    <p class="mb-2 text-sm text-gray-600">Saldo pendiente: $${parseFloat(saldoPendiente).toFixed(2)}</p>
                    <input id="monto_abono" type="number" step="0.01" max="${saldoPendiente}" class="swal2-input" placeholder="Monto a abonar $">
                    <select id="metodo_abono" class="swal2-input mt-2">
                        <option value="transferencia">Transferencia</option>
                        <option value="efectivo">Efectivo</option>
                    </select>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Sí, aplicar cobro',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const monto = document.getElementById('monto_abono').value;
                    if (!monto || monto <= 0 || parseFloat(monto) > parseFloat(saldoPendiente)) {
                        Swal.showValidationMessage('Ingrese un monto válido (no mayor al saldo)');
                        return false;
                    }
                    return monto;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('aplicarAbono', [cuentaId, parseFloat(result.value)]);
                }
            });
        }

        function eliminarCuenta(id) {
            Swal.fire({
                title: '¿Eliminar deuda del cliente?',
                text: "Se borrará este registro de crédito.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('eliminarCuenta', [id]);
                }
            });
        }

        function eliminarCliente(id) {
            Swal.fire({
                title: '¿Eliminar cliente?',
                text: "Se eliminará el cliente de la base de datos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('eliminarCliente', [id]);
                }
            });
        }

        function toggleTipos() {
            const isEstudiante = document.getElementById('cli_estudiante').checked;
            const isProfesionista = document.getElementById('cli_profesionista').checked;
            
            const estFields = document.getElementById('estudiante_fields');
            const profFields = document.getElementById('profesionista_fields');
            
            if(isEstudiante) {
                estFields.style.display = 'grid';
            } else {
                estFields.style.display = 'none';
                document.getElementById('cli_matricula').value = '';
                document.getElementById('cli_escuela').value = '';
                document.getElementById('cli_otra_escuela').value = '';
                document.getElementById('cli_otra_escuela').style.display = 'none';
                document.getElementById('lbl_otra_escuela').classList.add('invisible');
            }

            if(isProfesionista) {
                profFields.style.display = 'grid';
            } else {
                profFields.style.display = 'none';
                document.getElementById('cli_empresa').value = '';
                document.getElementById('cli_rfc').value = '';
                document.getElementById('cli_constancia').value = '';
            }
        }

        function toggleOtraEscuela() {
            const val = document.getElementById('cli_escuela').value;
            const otra = document.getElementById('cli_otra_escuela');
            const lblOtra = document.getElementById('lbl_otra_escuela');
            if(val === 'Otra') {
                otra.style.display = 'block';
                lblOtra.classList.remove('invisible');
            } else {
                otra.style.display = 'none';
                otra.value = '';
                lblOtra.classList.add('invisible');
            }
        }

        window.addEventListener('abrir-modal-email', event => {
            const count = event.detail[0].count;
            Swal.fire({
                title: 'Campaña de Email',
                width: '800px',
                html: `
                    <p class="mb-4 text-gray-600 text-sm">Se enviará un correo a <strong>${count}</strong> cliente(s).</p>
                    <input id="email_asunto" class="swal2-input w-full mt-2" placeholder="Asunto del correo" style="max-width: 100%; width: 90%;" required>
                    <textarea id="email_mensaje" class="swal2-textarea w-full mt-4" placeholder="Escribe el mensaje aquí..." rows="8" style="max-width: 100%; width: 90%;" required></textarea>
                    
                    <div class="mt-4 text-left px-8">
                        <label class="block text-sm font-medium text-gray-700">Adjuntar archivo (Opcional, JPG o PDF)</label>
                        <input type="file" id="email_adjunto" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Enviar Correos',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const asunto = document.getElementById('email_asunto').value;
                    const mensaje = document.getElementById('email_mensaje').value;
                    const fileInput = document.getElementById('email_adjunto');
                    
                    if(!asunto || !mensaje) {
                        Swal.showValidationMessage('El asunto y mensaje son obligatorios');
                        return false;
                    }

                    return new Promise((resolve) => {
                        if (fileInput.files.length > 0) {
                            const file = fileInput.files[0];
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                resolve({
                                    asunto,
                                    mensaje,
                                    adjunto: {
                                        name: file.name,
                                        mime: file.type,
                                        data: e.target.result.split(',')[1] // Get base64 string
                                    }
                                });
                            };
                            reader.readAsDataURL(file);
                        } else {
                            resolve({ asunto, mensaje, adjunto: null });
                        }
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Enviando...',
                        text: 'Por favor espera. Esto puede tardar unos segundos si hay archivos adjuntos.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    Livewire.dispatch('enviarCampanaEmail', [result.value]);
                }
            });
        });

        window.addEventListener('abrir-modal-whatsapp', event => {
            const clientes = event.detail[0].clientes;
            let listaHtml = '<div class="max-h-60 overflow-y-auto text-left mt-4 border rounded p-4 bg-gray-50">';
            clientes.forEach((c, index) => {
                listaHtml += `
                    <div class="flex justify-between items-center border-b py-3 last:border-b-0">
                        <span class="text-sm font-semibold text-gray-700">${c.nombre} ${c.apellidos} <br><small class="text-gray-500">${c.telefono}</small></span>
                        <button onclick="enviarWhatsApp('${c.telefono}', document.getElementById('wa_mensaje').value)" class="bg-green-500 hover:bg-green-600 text-white text-sm px-4 py-2 rounded">
                            Enviar <i class="fab fa-whatsapp"></i>
                        </button>
                    </div>
                `;
            });
            listaHtml += '</div>';

            Swal.fire({
                title: 'Promoción WhatsApp',
                width: '800px',
                html: `
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 text-left mb-4">
                        <p class="text-sm text-yellow-700">
                            <strong>Nota sobre archivos:</strong> WhatsApp Web <u>no permite</u> adjuntar archivos automáticamente a través de enlaces directos. Deberás arrastrar y soltar tu imagen JPG o PDF directamente en la ventana de WhatsApp Web una vez que se abra.
                        </p>
                    </div>
                    <p class="text-sm text-gray-600 mb-2 text-left px-4">Redacta tu mensaje y haz clic en "Enviar" uno por uno.</p>
                    <textarea id="wa_mensaje" class="swal2-textarea w-full mt-2" placeholder="Escribe tu mensaje aquí..." rows="6" style="max-width: 100%; width: 90%;"></textarea>
                    
                    <div class="mt-4 text-left px-8 mb-4">
                        <label class="block text-sm font-medium text-gray-700">Adjuntar archivo (Solo visual/referencia)</label>
                        <input type="file" id="wa_adjunto" accept=".jpg,.jpeg,.png,.pdf" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    </div>
                    ${listaHtml}
                `,
                showConfirmButton: false,
                showCancelButton: true,
                cancelButtonText: 'Cerrar'
            });
        });

        function enviarWhatsApp(telefono, mensaje) {
            if(!mensaje) {
                Swal.showValidationMessage('Escribe un mensaje antes de enviar.');
                alert('Por favor, escribe un mensaje primero.');
                return;
            }
            let telLimpio = telefono.replace(/\D/g,'');
            if(telLimpio.length === 10) {
                telLimpio = '52' + telLimpio; 
            }
            const url = `https://wa.me/${telLimpio}?text=${encodeURIComponent(mensaje)}`;
            window.open(url, '_blank');
        }
        window.addEventListener('pedirConfirmacionPdf', event => {
            Swal.fire({
                title: '¿Exportar a PDF?',
                text: '¿Estás seguro que quieres exportar los registros seleccionados a PDF?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, exportar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('exportSelected');
                }
            });
        });
    </script>
</div>
