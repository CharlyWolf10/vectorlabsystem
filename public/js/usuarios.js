/* =========================================
   Controlador JavaScript de Usuarios
========================================= */

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
                    Livewire.dispatch('guardarUsuario', [result.value]);
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
