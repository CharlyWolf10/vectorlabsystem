/* =========================================
   Controlador JavaScript de Proveedores
========================================= */

window.addEventListener('swal:success', event => {
    Swal.fire({
        icon: 'success',
        title: event.detail[0].title,
        text: event.detail[0].text,
        timer: 3000,
        timerProgressBar: true,
        showConfirmButton: true,
        didOpen: () => {
            const container = Swal.getContainer();
            if (container) container.style.zIndex = '1080';
        }
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
        showCloseButton: true,
        showCancelButton: true,
        customClass: {
            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 ml-2',
            cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 mr-2'
        },
        buttonsStyling: false,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            const popup = Swal.getPopup();
            popup.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    Swal.clickConfirm();
                }
            });
        },
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
            Livewire.dispatch('guardarProveedor', [result.value]);
        }
    });
}

function editarProveedor(id, nombre, telefono, email, direccion, rfc, banco, clabe, num_cuenta, titular_cuenta) {
    Swal.fire({
        title: 'Editar Proveedor',
        width: '900px',
        html: `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre completo o Empresa</label>
                    <input id="prov_nombre" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre completo o Empresa" value="${nombre}" oninput="this.value = this.value.toUpperCase()" required>
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
                    <input id="prov_titular" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Titular de la Cuenta" value="${titular_cuenta}" oninput="this.value = this.value.toUpperCase()">
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">CLABE Interbancaria</label>
                    <input id="prov_clabe" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="CLABE Interbancaria" value="${clabe}">
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Número de Cuenta</label>
                    <input id="prov_cuenta" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Número de Cuenta" value="${num_cuenta}">
                </div>
            </div>
        `,
        focusConfirm: false,
        showCloseButton: true,
        showCancelButton: true,
        customClass: {
            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 ml-2',
            cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 mr-2'
        },
        buttonsStyling: false,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            const popup = Swal.getPopup();
            popup.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    Swal.clickConfirm();
                }
            });
        },
        preConfirm: () => {
            const nombreEdit = document.getElementById('prov_nombre').value;
            if(!nombreEdit) {
                Swal.showValidationMessage('El nombre es obligatorio');
                return false;
            }
            return {
                id: id,
                nombre: nombreEdit,
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
        customClass: {
            confirmButton: 'bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 ml-2',
            cancelButton: 'bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 mr-2'
        },
        buttonsStyling: false,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('eliminarProveedor', [id]);
        }
    });
}
