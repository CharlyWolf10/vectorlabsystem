/* =========================================
   Controlador JavaScript de Inventario
========================================= */

/**
 * Genera el código HTML para el combobox (selector) de proveedores.
 * Utiliza los datos inyectados desde Laravel (window.inventarioProveedores).
 * @param {string|null} selectedId ID del proveedor que debe estar seleccionado por defecto.
 * @returns {string} HTML del <select>
 */
function generarHtmlProveedores(proveedorSeleccionado) {
    if (!window.inventarioProveedores || window.inventarioProveedores.length === 0) {
        return `<select id="prod_proveedor" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Sin proveedores (agregar uno primero)</option>
                </select>`;
    }

    let html = `<select id="prod_proveedor" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Ninguno</option>`;
    
    window.inventarioProveedores.forEach(prov => {
        const isSelected = (proveedorSeleccionado == prov.id) ? 'selected' : '';
        html += `<option value="${prov.id}" ${isSelected}>${prov.nombre}</option>`;
    });

    html += `</select>`;
    return html;
}

/**
 * Genera el HTML para el select de categorías.
 */
function generarHtmlCategorias(categoriaSeleccionada) {
    let html = `<select id="prod_categoria" onchange="if(this.value === '_nuevo') { document.getElementById('prod_nueva_categoria').style.display = 'block'; document.getElementById('prod_nueva_categoria').focus(); } else { document.getElementById('prod_nueva_categoria').style.display = 'none'; }" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Ninguna / General</option>`;
    
    if (window.inventarioCategorias && window.inventarioCategorias.length > 0) {
        window.inventarioCategorias.forEach(cat => {
            const isSelected = (categoriaSeleccionada == cat.nombre) ? 'selected' : '';
            html += `<option value="${cat.nombre}" ${isSelected}>${cat.nombre}</option>`;
        });
    }

    const isNuevoSelected = (categoriaSeleccionada === '_nuevo') ? 'selected' : '';
    html += `<option value="_nuevo" class="font-bold text-blue-600" ${isNuevoSelected}>+ Añadir nueva categoría...</option>
             </select>`;
    return html;
}

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
 * Abre el modal de SweetAlert para crear un producto nuevo.
 * Captura los datos y llama a Livewire.
 */
function nuevoProducto() {
    let proveedoresHtml = generarHtmlProveedores(null);

    Swal.fire({
        title: 'Nuevo Producto',
        width: '900px',
        html: `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left mt-4">
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Código/SKU</label>
                    <input id="prod_codigo" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Dejar en blanco para autogenerar, o escanea código">
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre</label>
                    <input id="prod_nombre" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre del Producto" oninput="this.value = this.value.toUpperCase()" required>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Proveedor</label>
                    ${proveedoresHtml}
                </div>
                <div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Categoría / Tipo</label>
                    ${generarHtmlCategorias(null)}
                    <input id="prod_nueva_categoria" type="text" style="display:none;" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 mt-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe la nueva categoría...">
                </div>

                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Costo</label>
                    <input id="prod_compra" type="number" step="0.01" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Precio de Compra $">
                </div>
                <div>
                    <label id="label_prod_minimo" class="text-sm text-gray-600 font-bold mb-1 block">Stock Mínimo</label>
                    <input id="prod_minimo" type="number" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Stock Mínimo (Alerta)">
                </div>
                ${generarHtmlCalculadoraStock(0, 'unidad', 1, 1)}
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            actualizarCalculadoraStock();
        },
        preConfirm: () => {
            const codigo = document.getElementById('prod_codigo').value;
            const nombre = document.getElementById('prod_nombre').value;
            if (!nombre) {
                Swal.showValidationMessage('El nombre es obligatorio');
                return false;
            }
            const ingresoTipo = document.getElementById('prod_ingreso_tipo') ? document.getElementById('prod_ingreso_tipo').value : 'unidad';
            const ingresoPaquetes = document.getElementById('prod_ingreso_paquetes') ? document.getElementById('prod_ingreso_paquetes').value : 1;
            const ingresoUnidades = document.getElementById('prod_ingreso_unidades_paquete') ? document.getElementById('prod_ingreso_unidades_paquete').value : 1;
            
            let minimoIngresado = document.getElementById('prod_minimo').value || 0;
            let minimoCalculado = minimoIngresado;
            if (ingresoTipo === 'caja') {
                minimoCalculado = minimoIngresado * (ingresoPaquetes * ingresoUnidades);
            } else if (ingresoTipo === 'paquete') {
                minimoCalculado = minimoIngresado * ingresoUnidades;
            }

            return {
                codigo: codigo,
                nombre: nombre,
                precio_compra: document.getElementById('prod_compra').value || 0,
                precio_venta: 0,
                stock: document.getElementById('prod_stock').value || 0,
                stock_minimo: minimoCalculado,
                proveedor_id: document.getElementById('prod_proveedor').value || null,
                categoria: document.getElementById('prod_categoria').value === '_nuevo' ? document.getElementById('prod_nueva_categoria').value : (document.getElementById('prod_categoria').value || null),
                ingreso_tipo_default: ingresoTipo,
                ingreso_paquetes_default: ingresoPaquetes,
                ingreso_unidades_default: ingresoUnidades
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('guardarProducto', [result.value]);
        }
    });
}

/**
 * Abre el modal de SweetAlert para editar un producto existente.
 * Pre-llena los campos con la información recibida.
 */
function editarProducto(id, codigo, nombre, compra, stock, minimo, proveedor_id, categoria, ingreso_tipo = 'unidad', ingreso_paquetes = 1, ingreso_unidades = 1) {
    let proveedoresHtml = generarHtmlProveedores(proveedor_id);

    Swal.fire({
        title: 'Editar Producto',
        width: '900px',
        html: `
            <input id="prod_id" type="hidden" value="${id}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left mt-4">
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Código/SKU</label>
                    <input id="prod_codigo" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100" placeholder="Código de Barras/SKU" value="${codigo}" required readonly>
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre</label>
                    <input id="prod_nombre" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre del Producto" oninput="this.value = this.value.toUpperCase()" value="${nombre}" required>
                </div>
                <div class="md:col-span-2">
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Proveedor</label>
                    ${proveedoresHtml}
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Categoría / Tipo</label>
                    ${generarHtmlCategorias(categoria)}
                    <input id="prod_nueva_categoria" type="text" style="display:none;" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 mt-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe la nueva categoría...">
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Costo</label>
                    <input id="prod_compra" type="number" step="0.01" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Precio de Compra $" value="${compra}">
                </div>
                <div>
                    <label id="label_prod_minimo" class="text-sm text-gray-600 font-bold mb-1 block">Stock Mínimo</label>
                    <input id="prod_minimo" type="number" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Stock Mínimo (Alerta)" value="${minimo}">
                </div>
                ${generarHtmlCalculadoraStock(stock, ingreso_tipo, ingreso_paquetes, ingreso_unidades)}
            </div>
        `,
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            actualizarCalculadoraStock();
            
            // Convertir minimo en piezas a la unidad visual para que el usuario la vea correctamente
            const tipo = document.getElementById('prod_ingreso_tipo').value;
            const paquetes = parseInt(document.getElementById('prod_ingreso_paquetes').value) || 1;
            const udsPorPaquete = parseInt(document.getElementById('prod_ingreso_unidades_paquete').value) || 1;
            let minimoReal = parseInt('${minimo}') || 0;
            if (tipo === 'caja') {
                document.getElementById('prod_minimo').value = (minimoReal / (paquetes * udsPorPaquete)) || 0;
            } else if (tipo === 'paquete') {
                document.getElementById('prod_minimo').value = (minimoReal / udsPorPaquete) || 0;
            } else {
                document.getElementById('prod_minimo').value = minimoReal;
            }
        },
        preConfirm: () => {
            const nombre = document.getElementById('prod_nombre').value;
            if (!nombre) {
                Swal.showValidationMessage('El nombre es obligatorio');
                return false;
            }
            const ingresoTipo = document.getElementById('prod_ingreso_tipo') ? document.getElementById('prod_ingreso_tipo').value : 'unidad';
            const ingresoPaquetes = document.getElementById('prod_ingreso_paquetes') ? document.getElementById('prod_ingreso_paquetes').value : 1;
            const ingresoUnidades = document.getElementById('prod_ingreso_unidades_paquete') ? document.getElementById('prod_ingreso_unidades_paquete').value : 1;
            
            let minimoIngresado = document.getElementById('prod_minimo').value || 0;
            let minimoCalculado = minimoIngresado;
            if (ingresoTipo === 'caja') {
                minimoCalculado = minimoIngresado * (ingresoPaquetes * ingresoUnidades);
            } else if (ingresoTipo === 'paquete') {
                minimoCalculado = minimoIngresado * ingresoUnidades;
            }

            return {
                id: document.getElementById('prod_id').value,
                codigo: document.getElementById('prod_codigo').value,
                nombre: nombre,
                precio_compra: document.getElementById('prod_compra').value || 0,
                precio_venta: 0,
                stock: document.getElementById('prod_stock').value || 0,
                stock_minimo: minimoCalculado,
                proveedor_id: document.getElementById('prod_proveedor').value || null,
                categoria: document.getElementById('prod_categoria').value === '_nuevo' ? document.getElementById('prod_nueva_categoria').value : (document.getElementById('prod_categoria').value || null),
                ingreso_tipo_default: ingresoTipo,
                ingreso_paquetes_default: ingresoPaquetes,
                ingreso_unidades_default: ingresoUnidades
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('guardarProducto', [result.value]);
        }
    });
}

/**
 * Pide confirmación al usuario antes de eliminar permanentemente un producto
 */
function eliminarProducto(id) {
    Swal.fire({
        title: '¿Eliminar producto?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('eliminarProducto', [id]);
        }
    });
}

/**
 * Pide confirmación y ejecuta un método Livewire para exportar a PDF (Directo)
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

/**
 * Escucha el evento emitido para abrir el selector múltiple de exportaciones (WhatsApp, Email, Descarga directa)
 */
window.addEventListener('abrirOpcionesExportacion', event => {
    Swal.fire({
        title: 'Exportar Inventario',
        text: '¿Cómo deseas exportar los registros seleccionados?',
        icon: 'question',
        showCancelButton: true,
        showDenyButton: true,
        confirmButtonColor: '#3085d6',
        denyButtonColor: '#25D366',
        cancelButtonColor: '#d33',
        confirmButtonText: '<i class="fas fa-file-pdf"></i> Descargar PDF',
        denyButtonText: '<i class="fab fa-whatsapp"></i> WhatsApp',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Opción 1: Descarga directa PDF
            Livewire.dispatch('exportSelected');
        } else if (result.isDenied) {
            // Opción 2: Enviar por WhatsApp
            Swal.fire({
                title: 'Enviar Alerta por WhatsApp',
                html: `
                    <div class="text-left px-4 mt-4" style="min-height: 80px;">
                        <label class="text-sm text-gray-600 font-bold mb-1 block">Número (10 dígitos)</label>
                        <input id="swal-phone" type="tel" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej: 5512345678">
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Enviar',
                didOpen: () => {
                    const input = document.getElementById("swal-phone");
                    // Inicializamos intl-tel-input para mostrar banderas con búsqueda
                    window.iti = window.intlTelInput(input, {
                        initialCountry: "mx",
                        preferredCountries: ["mx", "us", "co", "ar", "es"],
                        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    });
                    
                    // Aseguramos que solo puedan teclear hasta 10 números
                    input.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
                    });
                },
                preConfirm: () => {
                    const input = document.getElementById('swal-phone');
                    const number = input.value;
                    // Validación final de que sean estrictamente 10 números
                    if (number.length !== 10) {
                        Swal.showValidationMessage('El número debe tener exactamente 10 dígitos numéricos');
                        return false;
                    }
                    // Obtenemos el número completo (con código de país ej: +52)
                    return window.iti.getNumber();
                }
            }).then((phoneResult) => {
                if (phoneResult.isConfirmed && phoneResult.value) {
                    // Evita el bloqueo de popups abriendo la URL directamente aquí
                    const cleanNumber = phoneResult.value.replace(/[^0-9]/g, '');
                    const mensaje = "TAL PARECE QUE HAY UN FALTANTE EN VECTOR LAB PORFAVOR CONSULTA AL ADMIN PARA SABER CUAL";
                    const url = `https://wa.me/${cleanNumber}?text=${encodeURIComponent(mensaje)}`;
                    window.open(url, '_blank');
                }
            });
        }
    });
    
    // Agregamos inyectado el "Tercer" botón (Correo) en la ventana actual Swal
    const swalPopup = Swal.getPopup();
    const btnEmail = document.createElement('button');
    btnEmail.innerHTML = '<i class="fas fa-envelope"></i> Correo';
    btnEmail.className = 'swal2-confirm swal2-styled';
    btnEmail.style.backgroundColor = '#ea4335';
    btnEmail.onclick = () => {
        Swal.close();
        Swal.fire({
            title: 'Enviar por Correo',
            input: 'email',
            inputLabel: 'Dirección de correo electrónico',
            inputPlaceholder: 'correo@ejemplo.com',
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            confirmButtonColor: '#ea4335'
        }).then((emailResult) => {
            if (emailResult.isConfirmed && emailResult.value) {
                Livewire.dispatch('sendPdfEmail', [emailResult.value]);
            }
        });
    };
    
    // Insertamos el botón extra entre Confirmar y Cancelar
    const actions = swalPopup.querySelector('.swal2-actions');
    actions.insertBefore(btnEmail, actions.children[1]);
});

/**
 * Al recibir confirmación del backend, abre una pestaña nueva
 * hacia la API Web de WhatsApp con el número y mensaje.
 */
window.addEventListener('openWhatsApp', event => {
    const data = event.detail[0];
    const cleanNumber = data.telefono.replace(/[^0-9]/g, '');
    const url = `https://wa.me/${cleanNumber}?text=${encodeURIComponent(data.mensaje)}`;
    window.open(url, '_blank');
});

/**
 * Genera el HTML para la calculadora de stock híbrida.
 */
function generarHtmlCalculadoraStock(stockAnterior = 0, ingresoTipo = 'unidad', ingresoPaquetes = 1, ingresoUnidades = 1) {
    const isEdit = stockAnterior > 0;
    const title = isEdit ? 'Ingreso de Stock Adicional' : 'Ingreso de Stock Inicial';
    
    return `
        <div class="md:col-span-2 mt-2 pt-4 border-t border-gray-200">
            <h4 class="font-bold text-gray-800 uppercase tracking-wide text-center border-b-2 border-gray-300 pb-1 w-3/4 mx-auto mb-4">${title}</h4>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Forma de Ingreso</label>
                    <select id="prod_ingreso_tipo" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" onchange="actualizarCalculadoraStock()">
                        <option value="unidad" ${ingresoTipo === 'unidad' ? 'selected' : ''}>Por Unidad</option>
                        <option value="paquete" ${ingresoTipo === 'paquete' ? 'selected' : ''}>Por Paquete</option>
                        <option value="caja" ${ingresoTipo === 'caja' ? 'selected' : ''}>Por Caja</option>
                    </select>
                </div>
                
                <div id="div_ingreso_cajas" style="display: ${ingresoTipo === 'caja' ? 'block' : 'none'};">
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Cant. Cajas Nuevas</label>
                    <input id="prod_ingreso_cajas" type="number" value="0" min="0" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" oninput="actualizarCalculadoraStock()">
                </div>

                <div id="div_ingreso_paquetes" style="display: ${ingresoTipo === 'caja' || ingresoTipo === 'paquete' ? 'block' : 'none'};">
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Paquetes ${ingresoTipo === 'caja' ? '(por caja)' : 'Nuevos'}</label>
                    <input id="prod_ingreso_paquetes" type="number" value="${ingresoTipo === 'caja' ? ingresoPaquetes : (ingresoTipo === 'paquete' ? 0 : ingresoPaquetes)}" min="1" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" oninput="actualizarCalculadoraStock()">
                </div>

                <div id="div_ingreso_unidades_paquete" style="display: ${ingresoTipo === 'caja' || ingresoTipo === 'paquete' ? 'block' : 'none'};">
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Uds. por Paquete</label>
                    <input id="prod_ingreso_unidades_paquete" type="number" value="${ingresoUnidades}" min="1" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" oninput="actualizarCalculadoraStock()">
                </div>
                
                <div id="div_ingreso_unidades" style="display: ${ingresoTipo === 'unidad' ? 'block' : 'none'};">
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Cant. Uds. Nuevas</label>
                    <input id="prod_ingreso_unidades" type="number" value="0" min="0" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" oninput="actualizarCalculadoraStock()">
                </div>
            </div>
            
            <div class="mt-4 flex justify-between items-center bg-blue-50 p-3 rounded-lg border border-blue-200">
                <span class="text-sm text-blue-800 font-medium">Stock anterior: <span id="span_stock_anterior" class="font-bold">${stockAnterior}</span></span>
                <div class="text-right flex items-center">
                    <span class="text-sm text-blue-800 font-medium mr-2">A sumar: <span id="span_nuevas_piezas" class="font-bold text-lg text-blue-600">0</span> piezas</span>
                    <span class="text-sm text-green-800 font-medium ml-4 mr-2">Nuevo Stock Real: </span>
                    <input id="prod_stock" type="number" class="w-24 border border-gray-300 bg-gray-100 rounded-md shadow-sm px-2 py-1 text-center font-bold text-green-700" value="${stockAnterior}" readonly>
                </div>
            </div>
        </div>
    `;
}

/**
 * Función que se dispara al cambiar algún valor de la calculadora para actualizar el stock total
 */
window.actualizarCalculadoraStock = function() {
    const tipo = document.getElementById('prod_ingreso_tipo').value;
    
    const divCajas = document.getElementById('div_ingreso_cajas');
    const divPaquetes = document.getElementById('div_ingreso_paquetes');
    const divUnidadesPaquete = document.getElementById('div_ingreso_unidades_paquete');
    const divUnidades = document.getElementById('div_ingreso_unidades');
    const labelPaquetes = divPaquetes.querySelector('label');

    if (tipo === 'caja') {
        divCajas.style.display = 'block';
        divPaquetes.style.display = 'block';
        divUnidadesPaquete.style.display = 'block';
        divUnidades.style.display = 'none';
        labelPaquetes.innerText = 'Paquetes (por caja)';
    } else if (tipo === 'paquete') {
        divCajas.style.display = 'none';
        divPaquetes.style.display = 'block';
        divUnidadesPaquete.style.display = 'block';
        divUnidades.style.display = 'none';
        labelPaquetes.innerText = 'Cant. Paquetes Nuevos';
    } else {
        divCajas.style.display = 'none';
        divPaquetes.style.display = 'none';
        divUnidadesPaquete.style.display = 'none';
        divUnidades.style.display = 'block';
    }

    const labelMinimo = document.getElementById('label_prod_minimo');
    if (labelMinimo) {
        if (tipo === 'caja') {
            labelMinimo.innerText = 'Stock Mínimo (Cajas)';
        } else if (tipo === 'paquete') {
            labelMinimo.innerText = 'Stock Mínimo (Paquetes)';
        } else {
            labelMinimo.innerText = 'Stock Mínimo (Unidades)';
        }
    }

    const cajas = parseInt(document.getElementById('prod_ingreso_cajas').value) || 0;
    const paquetes = parseInt(document.getElementById('prod_ingreso_paquetes').value) || 0;
    const udsPorPaquete = parseInt(document.getElementById('prod_ingreso_unidades_paquete').value) || 0;
    const unidadesSueltas = parseInt(document.getElementById('prod_ingreso_unidades').value) || 0;
    
    let aSumar = 0;
    if (tipo === 'caja') {
        aSumar = cajas * paquetes * udsPorPaquete;
    } else if (tipo === 'paquete') {
        aSumar = paquetes * udsPorPaquete;
    } else {
        aSumar = unidadesSueltas;
    }

    document.getElementById('span_nuevas_piezas').innerText = aSumar;
    
    const stockAnterior = parseInt(document.getElementById('span_stock_anterior').innerText) || 0;
    document.getElementById('prod_stock').value = stockAnterior + aSumar;
};
