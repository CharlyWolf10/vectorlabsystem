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
                <div>
                    <button onclick="nuevoCliente()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mr-2">
                        <i class="fas fa-user-plus mr-2"></i> Nuevo Cliente
                    </button>
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
                                <th class="py-2 px-4 text-center w-12"><input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></th>
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

                    return {
                        nombre: nombre,
                        apellidos: apellidos,
                        es_estudiante: document.getElementById('cli_estudiante').checked,
                        matricula: document.getElementById('cli_matricula').value,
                        escuela: escuelaVal,
                        telefono: document.getElementById('cli_telefono').value,
                        email: document.getElementById('cli_email').value,
                        limite_credito: document.getElementById('cli_limite').value || 0,
                    }
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

                    return {
                        id: document.getElementById('cli_id').value,
                        nombre: nombre,
                        apellidos: apellidos,
                        es_estudiante: document.getElementById('cli_estudiante').checked,
                        matricula: document.getElementById('cli_matricula').value,
                        escuela: escuelaVal,
                        telefono: document.getElementById('cli_telefono').value,
                        email: document.getElementById('cli_email').value,
                        limite_credito: document.getElementById('cli_limite').value || 0,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('guardarCliente', [result.value]);
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
            if(isEstudiante) {
                fields.style.display = 'block';
            } else {
                fields.style.display = 'none';
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
