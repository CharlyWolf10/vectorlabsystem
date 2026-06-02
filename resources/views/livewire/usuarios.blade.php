<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Control de Usuarios y Cajeros') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-800">Directorio de Empleados</h2>
                <div>
                    <button onclick="nuevoUsuario()" class="bg-vl-blue hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow">
                        <i class="fas fa-user-plus mr-2"></i> Nuevo Empleado
                    </button>
                </div>
            </div>

            <!-- Panel de Usuarios -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-2 px-4 text-left">Nombre</th>
                                <th class="py-2 px-4 text-left">Correo Electrónico</th>
                                <th class="py-2 px-4 text-center">Rol</th>
                                <th class="py-2 px-4 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($usuarios as $usuario)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-2 px-4 font-bold">{{ $usuario->name }}</td>
                                <td class="py-2 px-4 text-gray-600">{{ $usuario->email }}</td>
                                <td class="py-2 px-4 text-center">
                                    @if($usuario->role === 'admin')
                                        <span class="bg-purple-100 text-purple-800 text-xs font-bold px-2 py-1 rounded uppercase">Administrador</span>
                                    @else
                                        <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded uppercase">Cajero</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <button class="text-blue-500 hover:text-blue-700 mr-2" title="Editar"><i class="fas fa-edit"></i></button>
                                    @if(auth()->id() !== $usuario->id)
                                    <button onclick="confirmarEliminar({{ $usuario->id }})" class="text-red-500 hover:text-red-700" title="Eliminar"><i class="fas fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-500">No hay usuarios registrados.</td>
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
        window.addEventListener('swal:error', event => {
            Swal.fire({
                icon: 'error',
                title: event.detail[0].title,
                text: event.detail[0].text,
            });
        });

        function nuevoUsuario() {
            Swal.fire({
                title: 'Nuevo Empleado',
                html: `
                    <input id="usr_nombre" class="swal2-input" placeholder="Nombre completo" required>
                    <input id="usr_email" type="email" class="swal2-input" placeholder="Correo Electrónico" required>
                    <input id="usr_password" type="password" class="swal2-input" placeholder="Contraseña temporal" required>
                    <select id="usr_role" class="swal2-input">
                        <option value="cajero">Cajero</option>
                        <option value="admin">Administrador</option>
                    </select>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('usr_nombre').value;
                    const email = document.getElementById('usr_email').value;
                    const password = document.getElementById('usr_password').value;
                    if(!nombre || !email || !password) {
                        Swal.showValidationMessage('Todos los campos son obligatorios');
                        return false;
                    }
                    return {
                        nombre: nombre,
                        email: email,
                        password: password,
                        role: document.getElementById('usr_role').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas crear esta cuenta de acceso al sistema?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0066ff',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, crear usuario',
                        cancelButtonText: 'Cancelar'
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            Livewire.dispatch('guardarUsuario', { data: result.value });
                        }
                    });
                }
            });
        }

        function confirmarEliminar(id) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer y el usuario perderá acceso al sistema.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('eliminarUsuario', { id: id });
                }
            });
        }
    </script>
</div>
