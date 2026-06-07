/* =========================================
   Controlador JavaScript de Compras y Pagos
========================================= */

/**
 * Escucha los eventos globales emitidos por Livewire para notificaciones exitosas
 */
window.addEventListener('swal:success', event => {
    Swal.fire({
        icon: 'success',
        title: event.detail[0].title,
        text: event.detail[0].text,
    });
});

/**
 * Escucha los eventos globales emitidos por Livewire para errores
 */
window.addEventListener('swal:error', event => {
    Swal.fire({
        icon: 'error',
        title: event.detail[0].title,
        text: event.detail[0].text,
    });
});

/**
 * Abre el modal de SweetAlert para registrar un nuevo proveedor.
 * Captura los datos y envía la petición a Livewire.
 */
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

                <div class="flex items-end justify-center mb-1">
                    <h4 class="font-bold text-gray-800 uppercase tracking-wide text-center border-b-2 border-gray-300 pb-1 w-3/4">Datos Bancarios</h4>
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

/**
 * Abre el modal para editar la información de un proveedor existente.
 * Recibe todos los parámetros actuales y los inyecta en los inputs HTML.
 */
function editarProveedor(id, nombre, telefono, email, direccion, rfc, banco, clabe, cuenta, titular) {
    Swal.fire({
        title: 'Editar Proveedor',
        width: '900px',
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

                <div class="flex items-end justify-center mb-1">
                    <h4 class="font-bold text-gray-800 uppercase tracking-wide text-center border-b-2 border-gray-300 pb-1 w-3/4">Datos Bancarios</h4>
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

/**
 * Pide confirmación al usuario antes de eliminar un proveedor
 */
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

/**
 * Abre el modal para registrar un nuevo gasto asociado a un proveedor específico.
 */
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

/**
 * Permite abonar a una cuenta por pagar (deuda)
 */
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

/**
 * Elimina un registro de deuda o cuenta por pagar.
 */
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

/**
 * Genera el modal para crear una nueva cuenta por pagar suelta.
 * Usa la lista global de proveedores inyectada desde Blade.
 */
function nuevaCuentaPorPagar() {
    let proveedoresHtml = '<select id="nuevo_prov_id" class="swal2-input">';
    if (window.comprasProveedores && Array.isArray(window.comprasProveedores)) {
        window.comprasProveedores.forEach(prov => {
            proveedoresHtml += `<option value="${prov.id}">${prov.nombre}</option>`;
        });
    }
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

/**
 * Escucha un evento de solicitud de PDF específico (opcional/alternativo)
 */
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

/**
 * Pide confirmación para exportar (PDF directo).
 */
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
