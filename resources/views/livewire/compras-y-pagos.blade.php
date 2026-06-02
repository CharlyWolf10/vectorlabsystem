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
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal === 'undefined') {
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                document.head.appendChild(script);
            }
        });

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

        function nuevoProveedor() {
            Swal.fire({
                title: 'Nuevo Proveedor',
                width: '900px',
                html: `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre completo o Empresa</label>
                            <input id="prov_nombre" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre completo o Empresa" oninput="this.value = this.value.toUpperCase()" required>
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Teléfono</label>
                            <input id="prov_telefono" type="text" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Teléfono">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Dirección</label>
                            <input id="prov_direccion" type="text" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Dirección">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">RFC</label>
                            <input id="prov_rfc" type="text" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="RFC" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Correo Electrónico</label>
                            <input id="prov_email" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Correo Electrónico">
                        </div>

                        <div class="flex items-end mb-2">
                            <h4 class="w-full font-bold text-gray-800 uppercase tracking-wide text-center border-b-2 border-gray-300 pb-1">Datos Bancarios</h4>
                        </div>
                        
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Banco (Ej. BBVA)</label>
                            <input id="prov_banco" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Banco (Ej. BBVA)" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Titular de la Cuenta</label>
                            <input id="prov_titular" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Titular de la Cuenta" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">CLABE Interbancaria</label>
                            <input id="prov_clabe" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="CLABE Interbancaria">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Número de Cuenta</label>
                            <input id="prov_cuenta" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Número de Cuenta">
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('prov_nombre').value;
                    if(!nombre) {
                        Swal.showValidationMessage('El nombre es obligatorio');
                        return false;
                    }
                    return {
                        nombre: nombre,
                        telefono: document.getElementById('prov_telefono').value,
                        email: document.getElementById('prov_email').value,
                        direccion: document.getElementById('prov_direccion').value,
                        rfc: document.getElementById('prov_rfc').value,
                        banco: document.getElementById('prov_banco').value,
                        titular_cuenta: document.getElementById('prov_titular').value,
                        clabe: document.getElementById('prov_clabe').value,
                        num_cuenta: document.getElementById('prov_cuenta').value,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Deseas guardar este proveedor?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0066ff',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, guardar',
                        cancelButtonText: 'Cancelar'
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            Livewire.dispatch('guardarProveedor', [result.value]);
                        }
                    });
                }
            });
        }

        function editarProveedor(id, nombre, telefono, email, direccion, rfc, banco, clabe, cuenta, titular) {
            Swal.fire({
                title: 'Editar Proveedor',
                html: `
                    <input id="prov_id" type="hidden" value="${id}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left mt-2">
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre completo o Empresa</label>
                            <input id="prov_nombre" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre completo o Empresa" value="${nombre}" required oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Teléfono</label>
                            <input id="prov_telefono" type="text" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Teléfono" value="${telefono}">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Dirección</label>
                            <input id="prov_direccion" type="text" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Dirección" value="${direccion}">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">RFC</label>
                            <input id="prov_rfc" type="text" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="RFC" value="${rfc}" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Correo Electrónico</label>
                            <input id="prov_email" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Correo Electrónico" value="${email}">
                        </div>

                        <div class="flex items-end mb-2">
                            <h4 class="w-full font-bold text-gray-800 uppercase tracking-wide text-center border-b-2 border-gray-300 pb-1">Datos Bancarios</h4>
                        </div>
                        
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Banco (Ej. BBVA)</label>
                            <input id="prov_banco" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Banco (Ej. BBVA)" value="${banco}" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Titular de la Cuenta</label>
                            <input id="prov_titular" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Titular de la Cuenta" value="${titular || ''}" oninput="this.value = this.value.toUpperCase()">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">CLABE Interbancaria</label>
                            <input id="prov_clabe" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="CLABE Interbancaria" value="${clabe}">
                        </div>
                        <div>
                            <label class="text-sm text-gray-600 font-bold mb-1 block">Número de Cuenta</label>
                            <input id="prov_cuenta" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Número de Cuenta" value="${cuenta}">
                        </div>
                    </div>
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Guardar Cambios',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const nombre = document.getElementById('prov_nombre').value;
                    if(!nombre) {
                        Swal.showValidationMessage('El nombre es obligatorio');
                        return false;
                    }
                    return {
                        id: document.getElementById('prov_id').value,
                        nombre: nombre,
                        telefono: document.getElementById('prov_telefono').value,
                        email: document.getElementById('prov_email').value,
                        direccion: document.getElementById('prov_direccion').value,
                        rfc: document.getElementById('prov_rfc').value,
                        banco: document.getElementById('prov_banco').value,
                        titular_cuenta: document.getElementById('prov_titular').value,
                        clabe: document.getElementById('prov_clabe').value,
                        num_cuenta: document.getElementById('prov_cuenta').value,
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('guardarProveedor', [result.value]);
                }
            });
        }

        function eliminarProveedor(id) {
            Swal.fire({
                title: '¿Eliminar proveedor?',
                text: "Se eliminará el proveedor de la base de datos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('eliminarProveedor', [id]);
                }
            });
        }

        function nuevoGasto(proveedorId) {
            Swal.fire({
                title: 'Registrar Nuevo Gasto/Compra',
                html:
                    '<input id="concepto" class="swal2-input" placeholder="Concepto (Ej. Insumos)">' +
                    '<input id="monto" type="number" step="0.01" class="swal2-input" placeholder="Monto Total $">' +
                    '<select id="metodo" class="swal2-input"><option value="transferencia">Transferencia</option><option value="efectivo">Efectivo</option><option value="tarjeta">Tarjeta</option><option value="credito">Crédito (Añadir a Cuentas por Pagar)</option></select>',
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar',
                preConfirm: () => {
                    const concepto = document.getElementById('concepto').value;
                    const monto = document.getElementById('monto').value;
                    if (!concepto || !monto || monto <= 0) {
                        Swal.showValidationMessage('Concepto y Monto válido son obligatorios');
                        return false;
                    }
                    return {
                        concepto: concepto,
                        monto: monto,
                        metodo: document.getElementById('metodo').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¿Confirmas el registro de este gasto?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#0066ff',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Sí, registrar',
                        cancelButtonText: 'Cancelar'
                    }).then((confirmResult) => {
                        if (confirmResult.isConfirmed) {
                            Livewire.dispatch('registrarGasto', [proveedorId, result.value]);
                        }
                    });
                }
            });
        }

        function confirmarAbono(cuentaId, proveedor, saldoPendiente) {
            Swal.fire({
                title: `Abonar a ${proveedor}`,
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
                confirmButtonText: 'Sí, aplicar pago',
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
                title: '¿Eliminar deuda?',
                text: "Se eliminará esta cuenta por pagar.",
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

        function nuevaCuentaPorPagar() {
            let proveedoresHtml = '<select id="nuevo_prov_id" class="swal2-input">';
            @foreach($proveedores as $prov)
                proveedoresHtml += `<option value="{{ $prov->id }}">{{ $prov->nombre }}</option>`;
            @endforeach
            proveedoresHtml += '</select>';

            Swal.fire({
                title: 'Nueva Cuenta por Pagar',
                html: `
                    <p class="mb-2 text-sm text-gray-600">Seleccione el proveedor y defina el monto de la deuda.</p>
                    ${proveedoresHtml}
                    <input id="nuevo_monto_deuda" type="number" step="0.01" class="swal2-input" placeholder="Monto total de la deuda $">
                `,
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#0066ff',
                cancelButtonText: 'Cancelar',
                confirmButtonText: 'Guardar Deuda',
                preConfirm: () => {
                    const provId = document.getElementById('nuevo_prov_id').value;
                    const monto = document.getElementById('nuevo_monto_deuda').value;
                    if (!provId || !monto || parseFloat(monto) <= 0) {
                        Swal.showValidationMessage('Ingrese un proveedor y monto válido');
                        return false;
                    }
                    return { proveedorId: provId, monto: parseFloat(monto) };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('crearCuentaPorPagar', [result.value.proveedorId, result.value.monto]);
                }
            });
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

        function confirmarExportacion(btn, metodo) {
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
                    Livewire.dispatch(metodo);
                }
            });
        }
    </script>
        </div>
    </div>
</div>
