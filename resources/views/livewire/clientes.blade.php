<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Clientes (CRM)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Directorio de Clientes</h2>
                    <button onclick="nuevoCliente()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mr-2">
                        <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                    </button>
                    <a href="{{ route('clientes.export') }}" target="_blank" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow mr-2">
                        <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
                    </a>
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow mr-2">
                        <i class="fas fa-envelope mr-2"></i> Campaña de Email
                    </button>
                    <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fab fa-whatsapp mr-2"></i> Promoción WhatsApp
                    </button>
                </div>
            </div>

            <!-- Panel de Clientes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div class="w-full md:w-1/3">
                        <input type="text" wire:model.live="search" placeholder="Buscar por nombre, correo o matrícula..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200">
                    </div>
                    @if(count($selectedClientes) > 0)
                        <div class="text-sm font-semibold text-blue-600">
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
                                    <input type="checkbox" value="{{ $cliente->id }}" wire:model="selectedClientes" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                                <td class="py-2 px-4">
                                    <span class="font-bold">{{ $cliente->nombre }} {{ $cliente->apellidos }}</span>
                                    @if($cliente->es_estudiante)
                                        <br><span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Estudiante: {{ $cliente->matricula }}</span>
                                        @if($cliente->escuela)
                                            <br><span class="text-xs text-gray-500"><i class="fas fa-university"></i> {{ $cliente->escuela }}</span>
                                        @endif
                                    @else
                                        <br><span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Profesionista</span>
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
                                    <button onclick="editarCliente({{ $cliente->id }}, '{{ $cliente->nombre }}', '{{ $cliente->apellidos }}', {{ $cliente->es_estudiante ? 'true' : 'false' }}, '{{ $cliente->matricula }}', '{{ $cliente->escuela }}', '{{ $cliente->telefono }}', '{{ $cliente->email }}', {{ $cliente->limite_credito }})" class="text-blue-500 hover:text-blue-700 mr-2"><i class="fas fa-edit"></i></button>
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

        function nuevoCliente() {
            Swal.fire({
                title: 'Nuevo Cliente',
                html: `
                    <input id="cli_nombre" class="swal2-input" placeholder="Nombre(s)" required>
                    <input id="cli_apellidos" class="swal2-input" placeholder="Apellidos" required>
                    <div class="mt-3 text-left pl-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="cli_estudiante" onchange="toggleMatricula()" class="form-checkbox text-vl-blue">
                            <span>¿Es estudiante?</span>
                        </label>
                    </div>
                    <div id="estudiante_fields" style="display:none;" class="mt-2 text-left pl-4">
                        <input id="cli_matricula" class="swal2-input !mt-0 w-full" placeholder="Matrícula">
                        <select id="cli_escuela" class="swal2-select w-full mt-2" onchange="toggleOtraEscuela()">
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
                        <input id="cli_otra_escuela" class="swal2-input w-full mt-2" placeholder="Especifique la escuela" style="display:none;">
                    </div>
                    <div id="profesionista_fields" class="mt-2 text-left pl-4">
                        <input id="cli_rfc" class="swal2-input !mt-0 w-full" placeholder="RFC (Para profesionistas)">
                        <label class="block mt-2 text-sm text-gray-600">Constancia de Situación Fiscal (Opcional, PDF o Imagen)</label>
                        <input type="file" id="cli_constancia" accept=".pdf,image/*" class="w-full text-sm mt-1">
                    </div>
                    <input id="cli_telefono" class="swal2-input" placeholder="Teléfono">
                    <input id="cli_email" class="swal2-input" placeholder="Correo Electrónico">
                    <input id="cli_limite" type="number" step="0.01" class="swal2-input" placeholder="Límite de Crédito Autorizado $">
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

                    let constanciaFile = document.getElementById('cli_constancia').files[0];

                    return new Promise((resolve) => {
                        let data = {
                            nombre: nombre,
                            apellidos: apellidos,
                            es_estudiante: isEstudiante,
                            matricula: isEstudiante ? document.getElementById('cli_matricula').value : null,
                            escuela: isEstudiante ? escuelaVal : null,
                            rfc: !isEstudiante ? document.getElementById('cli_rfc').value : null,
                            telefono: document.getElementById('cli_telefono').value,
                            email: document.getElementById('cli_email').value,
                            limite_credito: document.getElementById('cli_limite').value || 0,
                            constancia_base64: null
                        };

                        if (constanciaFile && !isEstudiante) {
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
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas guardar este cliente en la base de datos?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0066ff',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar'
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            Livewire.dispatch('guardarCliente', [result.value]);
                        }
                    });
                }
            });
        }

        function editarCliente(id, nombre, apellidos, es_estudiante, matricula, escuela, telefono, email, limite) {
            Swal.fire({
                title: 'Editar Cliente',
                html: `
                    <input id="cli_id" type="hidden" value="${id}">
                    <input id="cli_nombre" class="swal2-input" placeholder="Nombre(s)" value="${nombre}" required>
                    <input id="cli_apellidos" class="swal2-input" placeholder="Apellidos" value="${apellidos}" required>
                    <div class="mt-3 text-left pl-4">
                        <label class="flex items-center space-x-2 cursor-pointer">
                            <input type="checkbox" id="cli_estudiante" onchange="toggleMatricula()" class="form-checkbox text-vl-blue" ${es_estudiante ? 'checked' : ''}>
                            <span>¿Es estudiante?</span>
                        </label>
                    </div>
                    <div id="estudiante_fields" style="display:${es_estudiante ? 'block' : 'none'};" class="mt-2 text-left pl-4">
                        <input id="cli_matricula" class="swal2-input !mt-0 w-full" placeholder="Matrícula" value="${matricula}">
                        <select id="cli_escuela" class="swal2-select w-full mt-2" onchange="toggleOtraEscuela()">
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
                        <input id="cli_otra_escuela" class="swal2-input w-full mt-2" placeholder="Especifique la escuela" value="${!['UDLAP','UVM','UAMP','Tec de Monterrey','UNARTE','BUAP','UPAEP',''].includes(escuela) ? escuela : ''}" style="display:${!['UDLAP','UVM','UAMP','Tec de Monterrey','UNARTE','BUAP','UPAEP',''].includes(escuela) ? 'block' : 'none'};">
                    <div id="profesionista_fields" style="display:${es_estudiante ? 'none' : 'block'};" class="mt-2 text-left pl-4">
                        <input id="cli_rfc" class="swal2-input !mt-0 w-full" placeholder="RFC (Para profesionistas)" value="">
                        <label class="block mt-2 text-sm text-gray-600">Reemplazar Constancia de Situación Fiscal</label>
                        <input type="file" id="cli_constancia" accept=".pdf,image/*" class="w-full text-sm mt-1">
                    </div>
                    <input id="cli_telefono" class="swal2-input" placeholder="Teléfono" value="${telefono}">
                    <input id="cli_email" class="swal2-input" placeholder="Correo Electrónico" value="${email}">
                    <input id="cli_limite" type="number" step="0.01" class="swal2-input" placeholder="Límite de Crédito Autorizado $" value="${limite}">
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
                    let constanciaFile = document.getElementById('cli_constancia').files[0];

                    return new Promise((resolve) => {
                        let data = {
                            id: document.getElementById('cli_id').value,
                            nombre: nombre,
                            apellidos: apellidos,
                            es_estudiante: isEstudiante,
                            matricula: isEstudiante ? document.getElementById('cli_matricula').value : null,
                            escuela: isEstudiante ? escuelaVal : null,
                            rfc: !isEstudiante ? document.getElementById('cli_rfc').value : null,
                            telefono: document.getElementById('cli_telefono').value,
                            email: document.getElementById('cli_email').value,
                            limite_credito: document.getElementById('cli_limite').value || 0,
                            constancia_base64: null
                        };

                        if (constanciaFile && !isEstudiante) {
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

        function toggleMatricula() {
            const isEstudiante = document.getElementById('cli_estudiante').checked;
            const fields = document.getElementById('estudiante_fields');
            const profFields = document.getElementById('profesionista_fields');
            if(isEstudiante) {
                fields.style.display = 'block';
                if(profFields) profFields.style.display = 'none';
            } else {
                fields.style.display = 'none';
                if(profFields) profFields.style.display = 'block';
                document.getElementById('cli_matricula').value = '';
                document.getElementById('cli_escuela').value = '';
                document.getElementById('cli_otra_escuela').value = '';
                document.getElementById('cli_otra_escuela').style.display = 'none';
            }
        }

        function toggleOtraEscuela() {
            const val = document.getElementById('cli_escuela').value;
            const otra = document.getElementById('cli_otra_escuela');
            if(val === 'Otra') {
                otra.style.display = 'block';
            } else {
                otra.style.display = 'none';
                otra.value = '';
            }
        }
    </script>
</div>
