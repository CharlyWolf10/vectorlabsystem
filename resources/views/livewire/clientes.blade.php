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
                    <button class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-envelope mr-2"></i> Campaña de Email
                    </button>
                </div>
            </div>

            <!-- Panel de Clientes -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-2 px-4 text-left">Nombre</th>
                                <th class="py-2 px-4 text-left">Contacto</th>
                                <th class="py-2 px-4 text-right">Límite Crédito</th>
                                <th class="py-2 px-4 text-right">Saldo Deudor</th>
                                <th class="py-2 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clientes as $cliente)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4">
                                    <span class="font-bold">{{ $cliente->nombre }} {{ $cliente->apellidos }}</span>
                                    @if($cliente->es_estudiante)
                                        <br><span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Estudiante: {{ $cliente->matricula }}</span>
                                    @else
                                        <br><span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">Profesionista</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-sm">{{ $cliente->telefono }} <br> <span class="text-gray-500">{{ $cliente->email }}</span></td>
                                <td class="py-2 px-4 text-right">${{ number_format($cliente->limite_credito, 2) }}</td>
                                <td class="py-2 px-4 text-right text-red-600 font-bold">${{ number_format($cliente->saldo_pendiente, 2) }}</td>
                                <td class="py-2 px-4 text-center">
                                    <button class="text-blue-500 hover:text-blue-700"><i class="fas fa-edit"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-4 text-center text-gray-500">No hay clientes registrados.</td>
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
                    <input id="cli_matricula" class="swal2-input" placeholder="Matrícula" style="display:none;">
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
                    return {
                        nombre: nombre,
                        apellidos: apellidos,
                        es_estudiante: document.getElementById('cli_estudiante').checked,
                        matricula: document.getElementById('cli_matricula').value,
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
                            Livewire.dispatch('guardarCliente', { data: result.value });
                        }
                    });
                }
            });
        }

        function toggleMatricula() {
            const isEstudiante = document.getElementById('cli_estudiante').checked;
            const matriculaInput = document.getElementById('cli_matricula');
            if(isEstudiante) {
                matriculaInput.style.display = 'block';
                matriculaInput.required = true;
            } else {
                matriculaInput.style.display = 'none';
                matriculaInput.required = false;
                matriculaInput.value = '';
            }
        }
    </script>
</div>
