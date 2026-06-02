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
            <a href="/compras/export" target="_blank" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded shadow">
                <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
            </a>
        </div>
    </div>

    <!-- Panel de Proveedores -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Directorio de Proveedores</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead class="bg-gray-100 text-gray-600">
                    <tr>
                        <th class="py-2 px-4 text-left">Proveedor</th>
                        <th class="py-2 px-4 text-left">Datos Bancarios</th>
                        <th class="py-2 px-4 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proveedores as $proveedor)
                    <tr class="border-b">
                        <td class="py-2 px-4">
                            <div class="font-bold">{{ $proveedor->nombre }}</div>
                            <div class="text-sm text-gray-500">{{ $proveedor->telefono }} | {{ $proveedor->email }}</div>
                        </td>
                        <td class="py-2 px-4 text-sm">
                            <span class="font-semibold">{{ $proveedor->banco }}</span><br>
                            Cuenta: {{ $proveedor->num_cuenta }}<br>
                            CLABE: {{ $proveedor->clabe }}
                        </td>
                        <td class="py-2 px-4 text-center">
                            <button onclick="nuevoGasto({{ $proveedor->id }})" class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-3 rounded">
                                Registrar Gasto
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-4 text-center text-gray-500">No hay proveedores registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Panel de Cuentas por Pagar -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold border-b pb-2 mb-4 text-red-600">Cuentas por Pagar Activas</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($cuentasPorPagar as $cuenta)
            <div class="border rounded p-4 shadow-sm relative">
                <div class="absolute top-2 right-2 bg-yellow-100 text-yellow-800 text-xs font-bold px-2 py-1 rounded">
                    {{ strtoupper($cuenta->estado) }}
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

        function nuevoProveedor() {
            Swal.fire({
                title: 'Nuevo Proveedor',
                html: `
                    <input id="prov_nombre" class="swal2-input" placeholder="Nombre completo o Empresa" required>
                    <input id="prov_telefono" class="swal2-input" placeholder="Teléfono">
                    <input id="prov_email" class="swal2-input" placeholder="Correo Electrónico">
                    <h4 class="mt-4 font-bold text-gray-700 text-left px-2">Datos Bancarios</h4>
                    <input id="prov_banco" class="swal2-input" placeholder="Banco (Ej. BBVA)">
                    <input id="prov_clabe" class="swal2-input" placeholder="CLABE Interbancaria">
                    <input id="prov_cuenta" class="swal2-input" placeholder="Número de Cuenta">
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
                        banco: document.getElementById('prov_banco').value,
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
                            Livewire.dispatch('guardarProveedor', { data: result.value });
                        }
                    });
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
                            Livewire.dispatch('registrarGasto', { proveedorId: proveedorId, data: result.value });
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
                    Livewire.dispatch('aplicarAbono', { cuentaId: cuentaId, monto: result.value });
                }
            });
        }
    </script>
        </div>
    </div>
</div>
