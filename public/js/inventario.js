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

    let html = `
        <div class="flex items-center gap-2 w-full">
            <select id="prod_proveedor" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">Ninguno</option>`;
    
    window.inventarioProveedores.forEach(prov => {
        const isSelected = (proveedorSeleccionado == prov.id) ? 'selected' : '';
        html += `<option value="${prov.id}" ${isSelected}>${prov.nombre}</option>`;
    });

    html += `</select>
            <button type="button" onclick="nuevoProveedor()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold w-10 h-10 rounded shadow flex items-center justify-center shrink-0" title="Añadir nuevo proveedor">
                <i class="fas fa-plus"></i>
            </button>
        </div>`;
    return html;
}

/**
 * Genera el HTML para el botón que abre el Gestor de Categorías.
 */
function generarHtmlCategorias(categoriaSeleccionada) {
    let displayNombre = "Ninguna / General";
    if (categoriaSeleccionada && categoriaSeleccionada !== '_nuevo') {
        displayNombre = categoriaSeleccionada;
    } else if (categoriaSeleccionada === '_nuevo') {
        displayNombre = "+ Añadir nueva categoría...";
    }

    return `
        <div class="w-full text-left">
            <!-- Campo oculto para el valor real -->
            <input type="hidden" id="prod_categoria" value="${categoriaSeleccionada || ''}">
            
            <!-- Botón del Gestor -->
            <button type="button" id="btn_abrir_gestor_categorias" onclick="abrirGestorCategorias()" class="w-full border border-blue-600 rounded-md shadow-sm px-4 py-2 bg-blue-50 hover:bg-blue-100 active:bg-blue-200 focus:ring-2 focus:ring-blue-500 focus:outline-none flex justify-between items-center text-sm transition-all duration-150 transform active:scale-[0.98]">
                <span id="categoria_selected_text" class="${categoriaSeleccionada === '_nuevo' ? 'text-blue-700 font-bold' : 'text-blue-900 font-semibold uppercase'}">
                    <i class="fas fa-folder-open text-blue-600 mr-2"></i> ${displayNombre}
                </span>
                <i class="fas fa-external-link-alt text-blue-500 text-xs"></i>
            </button>
        </div>
    `;
}

let modalKeyboardIndex = -1;

/**
 * Inyecta el HTML del Modal de Categorías en el body si no existe, y lo actualiza.
 */
function inyectarModalCategorias(categoriaSeleccionada) {
    let modal = document.getElementById('modal_gestor_categorias');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'modal_gestor_categorias';
        modal.className = 'fixed inset-0 hidden flex items-center justify-center bg-black bg-opacity-40 backdrop-blur-sm transition-opacity';
        modal.style.zIndex = "1070"; // Elevado por encima de SweetAlert (1060) para poder usarse durante edición
        // Cerrar al hacer clic fuera del contenido
        modal.addEventListener('mousedown', function(e) {
            if (e.target === modal) {
                cerrarGestorCategorias();
            }
        });
        document.body.appendChild(modal);
    }

    let listHtml = `
        <div class="bg-white rounded-xl shadow-2xl overflow-hidden transform transition-all scale-100" style="width: 550px; max-width: 95vw;" id="modal_gestor_content" tabindex="0">
            <div class="bg-gray-50 px-5 py-4 border-b flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-tags text-blue-500 mr-2"></i>Categorías</h3>
                <button type="button" onclick="cerrarGestorCategorias()" class="text-gray-400 hover:text-red-500 transition-colors focus:outline-none">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            
            <div class="p-4 border-b bg-gray-50 flex items-center gap-2">
                <input type="text" id="nueva_categoria_inline" placeholder="Nueva Categoría..." class="flex-1 border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" onkeydown="if(event.key === 'Enter') guardarNuevaCategoriaInline()" oninput="this.value = this.value.toUpperCase()">
                <select id="nueva_categoria_medida" class="w-48 border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-white">
                    <option value="">Sin medida</option>
                    <option value="gramaje_papel">Gramaje (Papel)</option>
                    <option value="grosor_laser">Grosor (Láser)</option>
                    <option value="g">Gramos (g)</option>
                    <option value="kg">Kilogramos (kg)</option>
                    <option value="ml">Mililitros (ml)</option>
                    <option value="l">Litros (l)</option>
                    <option value="mm">Milímetros (mm)</option>
                    <option value="cm">Centímetros (cm)</option>
                    <option value="m">Metros (m)</option>
                </select>
                <button type="button" onclick="guardarNuevaCategoriaInline()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition-colors">
                    Guardar
                </button>
            </div>
            
            <div class="p-4">
                <div class="max-h-80 overflow-y-auto pr-2 custom-scrollbar text-base">
                    <ul class="divide-y divide-gray-100" id="categorias_list_ul">
                        <li id="cat_item_1" class="cat-nav-item py-3 hover:bg-blue-50 cursor-pointer rounded px-3 flex justify-between items-center transition-colors focus:outline-none focus:bg-blue-100" tabindex="0" onclick="seleccionarCategoria('', 'Ninguna / General', '')" onkeydown="handleCatKey(event, '', 'Ninguna / General', '')">
                            <span class="text-gray-700 font-medium uppercase"><i class="fas fa-folder text-gray-400 mr-3"></i>Ninguna / General</span>
                        </li>`;

    let catIndex = 2;
    if (window.inventarioCategorias && window.inventarioCategorias.length > 0) {
        window.inventarioCategorias.forEach(cat => {
            const isSelected = (categoriaSeleccionada == cat.nombre) ? 'bg-blue-50 font-bold text-blue-700' : 'text-gray-700';
            listHtml += `
                        <li id="cat_item_${catIndex}" class="cat-nav-item py-3 hover:bg-blue-50 rounded px-3 flex justify-between items-center transition-colors group focus:outline-none focus:bg-blue-100" tabindex="0" onkeydown="handleCatKey(event, '${cat.nombre}', '${cat.nombre}', '${cat.tipo_medida || ''}')">
                            <div class="flex-grow cursor-pointer flex items-center truncate mr-3 uppercase ${isSelected}" onclick="seleccionarCategoria('${cat.nombre}', '${cat.nombre}', '${cat.tipo_medida || ''}')" title="${cat.nombre}">
                                <i class="fas fa-folder text-yellow-500 mr-3"></i> <span class="truncate">${cat.nombre}</span>
                            </div>
                            <div class="flex space-x-2">
                                <button type="button" onclick="editarCategoriaModal(${cat.id}, '${cat.nombre}', '${cat.tipo_medida || ''}')" class="text-blue-500 hover:text-white hover:bg-blue-500 bg-blue-50 px-3 py-2 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-blue-400" title="Editar">
                                    <i class="fas fa-edit text-base"></i>
                                </button>
                                <button type="button" onclick="eliminarCategoriaModal(${cat.id}, '${cat.nombre}')" class="text-red-500 hover:text-white hover:bg-red-500 bg-red-50 px-3 py-2 rounded transition-colors focus:outline-none focus:ring-2 focus:ring-red-400" title="Eliminar">
                                    <i class="fas fa-trash text-base"></i>
                                </button>
                            </div>
                        </li>`;
            catIndex++;
        });
    }

    listHtml += `
                    </ul>
                </div>
            </div>
        </div>
    `;
    
    modal.innerHTML = listHtml;
}

function handleCatKey(event, valor, texto, tipoMedida = '') {
    if (event.key === 'Enter') {
        event.preventDefault();
        seleccionarCategoria(valor, texto, tipoMedida);
    } else if (event.key === 'ArrowDown') {
        event.preventDefault();
        navegarTecladoCategorias(1);
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        navegarTecladoCategorias(-1);
    } else if (event.key === 'Escape') {
        cerrarGestorCategorias();
    }
}

function navegarTecladoCategorias(dir) {
    const items = document.querySelectorAll('.cat-nav-item');
    if (!items || items.length === 0) return;
    
    modalKeyboardIndex += dir;
    if (modalKeyboardIndex < 0) modalKeyboardIndex = items.length - 1;
    if (modalKeyboardIndex >= items.length) modalKeyboardIndex = 0;
    
    items[modalKeyboardIndex].focus();
}

function abrirGestorCategorias() {
    const valorActual = document.getElementById('prod_categoria') ? document.getElementById('prod_categoria').value : '';
    inyectarModalCategorias(valorActual);
    const modal = document.getElementById('modal_gestor_categorias');
    modal.classList.remove('hidden');
    
    // Add keyboard listener to modal itself for escape key
    modal.onkeydown = function(e) {
        if (e.key === 'Escape') cerrarGestorCategorias();
        if (e.key === 'ArrowDown' && document.activeElement === document.body) {
            navegarTecladoCategorias(1);
        }
    };
    
    // Set initial focus
    setTimeout(() => {
        modalKeyboardIndex = 1;
        const firstItem = document.getElementById('cat_item_1');
        if(firstItem) firstItem.focus();
    }, 50);
}

function cerrarGestorCategorias() {
    const modal = document.getElementById('modal_gestor_categorias');
    if (modal) {
        modal.classList.add('hidden');
    }
    const btn = document.getElementById('btn_abrir_gestor_categorias');
    if(btn) btn.focus();
}

function seleccionarCategoria(valor, texto, tipoMedida = null) {
    const inputCategoria = document.getElementById('prod_categoria');
    const selectedText = document.getElementById('categoria_selected_text');
    const inputNuevaCategoria = document.getElementById('prod_nueva_categoria');

    if (inputCategoria) inputCategoria.value = valor;
    
    if (selectedText) {
        if (valor === '_nuevo') {
            selectedText.innerHTML = `<i class="fas fa-folder-open text-blue-600 mr-2"></i> ${texto}`;
            selectedText.className = 'text-blue-700 font-bold';
            if (inputNuevaCategoria) {
                inputNuevaCategoria.style.display = 'block';
                inputNuevaCategoria.focus();
            }
        } else {
            selectedText.innerHTML = `<i class="fas fa-folder-open text-blue-600 mr-2"></i> ${texto}`;
            selectedText.className = 'text-blue-900 font-semibold uppercase';
            if (inputNuevaCategoria) {
                inputNuevaCategoria.style.display = 'none';
                inputNuevaCategoria.value = '';
            }
        }
    }
    
    // Si tipoMedida es null pero hay un texto, podemos intentar inferir de inventarioCategorias
    if (tipoMedida === null && window.inventarioCategorias && valor) {
        const catObj = window.inventarioCategorias.find(c => c.nombre === valor);
        if (catObj) tipoMedida = catObj.tipo_medida;
    }
    
    // Measurement Logic
    const divGramaje = document.getElementById('div_gramaje');
    if (divGramaje) {
        // Limpiamos el contenedor
        divGramaje.innerHTML = '';
        divGramaje.classList.add('hidden');
        
        let unitNames = {
            'g': 'Gramos (g)', 'kg': 'Kilogramos (kg)', 'ml': 'Mililitros (ml)', 
            'l': 'Litros (l)', 'mm': 'Milímetros (mm)', 'cm': 'Centímetros (cm)', 'm': 'Metros (m)'
        };

        if (tipoMedida === 'gramaje_papel') {
            divGramaje.innerHTML = `
                <label class="text-sm text-blue-800 font-bold mb-1 block">Gramaje</label>
                <select id="prod_gramaje" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-blue-50 focus:ring-blue-500 focus:border-blue-500" onchange="aplicarMedidaAlNombre()">
                    <option value="">Selecciona Gramaje</option>
                    <option value="75">75 gramos</option>
                    <option value="90">90 gramos</option>
                    <option value="115">115 gramos</option>
                    <option value="120">120 gramos</option>
                    <option value="125">125 gramos</option>
                    <option value="150">150 gramos</option>
                    <option value="200">200 gramos</option>
                    <option value="250">250 gramos</option>
                    <option value="300">300 gramos</option>
                </select>
                <input type="hidden" id="prod_medida_tipo" value="gramaje_papel">
            `;
            divGramaje.classList.remove('hidden');
        } else if (tipoMedida === 'grosor_laser') {
            divGramaje.innerHTML = `
                <label class="text-sm text-blue-800 font-bold mb-1 block">Grosor (Material)</label>
                <select id="prod_gramaje" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-blue-50 focus:ring-blue-500 focus:border-blue-500" onchange="aplicarMedidaAlNombre()">
                    <option value="">Selecciona Grosor</option>
                    <option value="3">3 mm</option>
                    <option value="5">5 mm</option>
                    <option value="6">6 mm</option>
                    <option value="9">9 mm</option>
                    <option value="12">12 mm</option>
                </select>
                <input type="hidden" id="prod_medida_tipo" value="grosor_laser">
            `;
            divGramaje.classList.remove('hidden');
        } else if (tipoMedida && unitNames[tipoMedida]) {
            divGramaje.innerHTML = `
                <label class="text-sm text-blue-800 font-bold mb-1 block">¿Cuántos ${unitNames[tipoMedida]} tiene?</label>
                <div class="flex items-center">
                    <input type="number" step="any" id="prod_gramaje" class="flex-1 border border-gray-300 rounded-l-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Ej. 500" onchange="aplicarMedidaAlNombre()" onkeyup="aplicarMedidaAlNombre()">
                    <span class="bg-gray-100 border border-l-0 border-gray-300 text-gray-600 px-3 py-2 rounded-r-md font-bold uppercase">${tipoMedida}</span>
                </div>
                <input type="hidden" id="prod_medida_tipo" value="${tipoMedida}">
            `;
            divGramaje.classList.remove('hidden');
        } else {
            // Compatibilidad anterior por si no tiene tipo_medida
            const txtUpper = texto.toUpperCase();
            if (txtUpper.includes('PAPEL') || txtUpper.includes('OPALINA') || txtUpper.includes('CARTULINA') || txtUpper.includes('IMPRESI')) {
                divGramaje.innerHTML = `
                    <label class="text-sm text-blue-800 font-bold mb-1 block">Gramaje</label>
                    <select id="prod_gramaje" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-blue-50 focus:ring-blue-500 focus:border-blue-500" onchange="aplicarMedidaAlNombre()">
                        <option value="">Selecciona Gramaje</option>
                        <option value="75">75 gramos</option>
                        <option value="90">90 gramos</option>
                        <option value="115">115 gramos</option>
                        <option value="120">120 gramos</option>
                        <option value="125">125 gramos</option>
                        <option value="150">150 gramos</option>
                        <option value="200">200 gramos</option>
                        <option value="250">250 gramos</option>
                        <option value="300">300 gramos</option>
                    </select>
                    <input type="hidden" id="prod_medida_tipo" value="gramaje_papel">
                `;
                divGramaje.classList.remove('hidden');
            } else {
                divGramaje.innerHTML = '<input type="hidden" id="prod_gramaje" value=""><input type="hidden" id="prod_medida_tipo" value="">';
            }
        }
    }
    
    actualizarOpcionesStockMinimo(tipoMedida);
    
    cerrarGestorCategorias();
}

function guardarNuevaCategoriaInline() {
    const input = document.getElementById('nueva_categoria_inline');
    const selectMedida = document.getElementById('nueva_categoria_medida');
    if (!input) return;
    
    const value = input.value.trim().toUpperCase();
    if (!value) {
        Swal.fire({
            icon: 'warning',
            title: 'Atención',
            text: 'El nombre de la categoría no puede estar vacío.',
            target: document.getElementById('modal_gestor_content')
        });
        return;
    }
    
    const medida = selectMedida ? selectMedida.value : null;
    Livewire.dispatch('crearCategoria', [value, medida]);
    cerrarGestorCategorias(); // Close modal to see success alert clearly
}

function editarCategoriaModal(id, nombreActual, medidaActual) {
    
    const optionsHTML = `
        <option value="" ${!medidaActual ? 'selected' : ''}>Sin medida</option>
        <option value="gramaje_papel" ${medidaActual === 'gramaje_papel' ? 'selected' : ''}>Gramaje (Papel)</option>
        <option value="grosor_laser" ${medidaActual === 'grosor_laser' ? 'selected' : ''}>Grosor (Láser)</option>
        <option value="g" ${medidaActual === 'g' ? 'selected' : ''}>Gramos (g)</option>
        <option value="kg" ${medidaActual === 'kg' ? 'selected' : ''}>Kilogramos (kg)</option>
        <option value="ml" ${medidaActual === 'ml' ? 'selected' : ''}>Mililitros (ml)</option>
        <option value="l" ${medidaActual === 'l' ? 'selected' : ''}>Litros (l)</option>
        <option value="mm" ${medidaActual === 'mm' ? 'selected' : ''}>Milímetros (mm)</option>
        <option value="cm" ${medidaActual === 'cm' ? 'selected' : ''}>Centímetros (cm)</option>
        <option value="m" ${medidaActual === 'm' ? 'selected' : ''}>Metros (m)</option>
    `;

    Swal.fire({
        title: 'Editar Categoría',
        html: `
            <input id="swal-input-nombre" class="swal2-input" placeholder="Nombre de categoría" value="${nombreActual}">
            <select id="swal-input-medida" class="swal2-select w-[80%] max-w-[80%] mx-auto mt-4 text-center">
                ${optionsHTML}
            </select>
        `,
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3085d6',
        didOpen: () => {
            const container = Swal.getContainer();
            if (container) container.style.zIndex = '1080'; // Asegurar que esté por encima de modal_gestor_categorias (1070)
            const input = document.getElementById('swal-input-nombre');
            if (input) {
                input.oninput = () => {
                    input.value = input.value.toUpperCase();
                };
            }
        },
        preConfirm: () => {
            const nombre = document.getElementById('swal-input-nombre').value.trim();
            const medida = document.getElementById('swal-input-medida').value;
            if (!nombre) {
                Swal.showValidationMessage('El nombre no puede estar vacío');
                return false;
            }
            return { nombre: nombre, medida: medida };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('actualizarCategoria', [id, result.value.nombre.toUpperCase(), result.value.medida]);
        }
    });
}

function eliminarCategoriaModal(id, nombre) {
    Swal.fire({
        title: '¿Eliminar Categoría?',
        html: `Se eliminará permanentemente la categoría <b>${nombre}</b>.<br><br>Los productos que pertenezcan a esta categoría quedarán marcados como "Ninguna / General".`,
        icon: 'warning',
        showCancelButton: true,
        showCloseButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            const container = Swal.getContainer();
            if (container) container.style.zIndex = '1080'; // Asegurar que esté por encima de modal_gestor_categorias (1070)
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('eliminarCategoria', [id]);
        }
    });
}

/**
 * Escucha los eventos globales emitidos por Livewire para notificaciones exitosas
 */
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
 * Escucha cuando se debe mostrar un toast de éxito sin cerrar los modales activos
 */
window.addEventListener('toast:success', event => {
    Swal.fire({
        icon: 'success',
        title: event.detail[0].title,
        text: event.detail[0].text,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
    });
});

/**
 * Escucha cuando se crea una categoría exitosamente desde el modal
 */
window.addEventListener('categoriaCreada', event => {
    // Si hay una categoría recién creada, la seleccionamos automáticamente en el select
    setTimeout(() => {
        seleccionarCategoria(event.detail.nombre, event.detail.nombre);
    }, 500); // Pequeño delay para dejar que Livewire actualice window.inventarioCategorias
});

/**
 * Escucha cuando las categorías se actualizan desde el servidor
 */
window.addEventListener('categoriasActualizadas', event => {
    window.inventarioCategorias = event.detail.categorias;
    
    // Si el gestor de categorías está abierto, lo actualizamos inmediatamente
    const modal = document.getElementById('modal_gestor_categorias');
    if (modal && !modal.classList.contains('hidden')) {
        const valorActual = document.getElementById('prod_categoria') ? document.getElementById('prod_categoria').value : '';
        inyectarModalCategorias(valorActual);
    }
});

/**
 * Abre el modal de SweetAlert para crear un producto nuevo.
 * Captura los datos y llama a Livewire.
 */
function nuevoProducto() {
    let proveedoresHtml = generarHtmlProveedores(null);

    Swal.fire({
        title: 'Nuevo Producto',
        width: '1200px',
        html: `
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left mt-4">
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Código/SKU</label>
                    <input id="prod_codigo" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Dejar en blanco para autogenerar, o escanea código">
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre</label>
                    <input id="prod_nombre" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre del Producto" oninput="this.value = this.value.toUpperCase()" required>
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Proveedor</label>
                    ${proveedoresHtml}
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Categoría / Tipo</label>
                    ${generarHtmlCategorias(null)}
                    <input id="prod_nueva_categoria" type="text" style="display:none;" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 mt-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe la nueva categoría..." oninput="this.value = this.value.toUpperCase()">
                </div>
                <div id="div_gramaje" class="hidden"></div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Costo Neto</label>
                    <input id="prod_compra" type="number" step="0.01" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Precio de Compra $" oninput="if(document.getElementById('prod_iva').checked) calcularIvaCosto(document.getElementById('prod_iva'))">
                    <div class="mt-2 flex items-center">
                        <input type="checkbox" id="prod_iva" class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="calcularIvaCosto(this)">
                        <label for="prod_iva" class="text-xs text-gray-600 font-medium cursor-pointer">Sumar 16% IVA</label>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Costo con IVA</label>
                    <input id="prod_iva_resultado" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100 text-gray-500 font-bold cursor-not-allowed" value="---" readonly disabled>
                </div>
                <div>
                    <label id="label_prod_minimo" class="text-sm text-gray-600 font-bold mb-1 block">Stock Mínimo</label>
                    <div class="flex">
                        <input id="prod_minimo" type="number" class="w-1/2 border border-gray-300 rounded-l-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cantidad">
                        <select id="prod_minimo_tipo" class="w-1/2 border border-l-0 border-gray-300 rounded-r-md shadow-sm px-3 py-2 bg-gray-50 focus:ring-blue-500 focus:border-blue-500">
                            <option value="unidad">Unidades</option>
                            <option value="paquete">Paquetes</option>
                            <option value="caja">Cajas</option>
                        </select>
                    </div>
                </div>
                ${generarHtmlCalculadoraStock(0, 'unidad', 1, 1)}
            </div>
        `,
        showCloseButton: true,
        focusConfirm: false,
        showCancelButton: true,
        customClass: {
            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 ml-2',
            cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 mr-2'
        },
        buttonsStyling: false,
        confirmButtonText: 'Guardar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            actualizarCalculadoraStock();
            
            // Inicializar categoría si ya estaba seleccionada (o vacía)
            const catActual = document.getElementById('prod_categoria').value;
            let tipoMedida = null;
            if (catActual && window.inventarioCategorias) {
                const catObj = window.inventarioCategorias.find(c => c.nombre === catActual);
                if (catObj) tipoMedida = catObj.tipo_medida;
            }
            seleccionarCategoria(catActual || '', catActual || 'Ninguna / General', tipoMedida);
            
            // Soporte de teclado (Enter para guardar)
            const popup = Swal.getPopup();
            popup.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    Swal.clickConfirm();
                }
            });
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
            let minimoTipo = document.getElementById('prod_minimo_tipo') ? document.getElementById('prod_minimo_tipo').value : 'unidad';
            let minimoCalculado = minimoIngresado;
            if (minimoTipo === 'caja') {
                minimoCalculado = minimoIngresado * (ingresoPaquetes * ingresoUnidades);
            } else if (minimoTipo === 'paquete') {
                minimoCalculado = minimoIngresado * ingresoUnidades;
            }

            const data = {
                codigo: codigo,
                nombre: nombre,
                precio_compra: document.getElementById('prod_compra').value || 0,
                precio_venta: 0,
                stock: document.getElementById('prod_stock').value || 0,
                stock_minimo: minimoCalculado,
                proveedor_id: document.getElementById('prod_proveedor').value || null,
                categoria: document.getElementById('prod_categoria').value === '_nuevo' ? document.getElementById('prod_nueva_categoria').value : (document.getElementById('prod_categoria').value || null),
                aplica_iva: document.getElementById('prod_iva') ? document.getElementById('prod_iva').checked : false,
                ingreso_tipo_default: ingresoTipo,
                ingreso_paquetes_default: ingresoPaquetes,
                ingreso_unidades_default: ingresoUnidades
            };
            Livewire.dispatch('guardarProducto', [data]);
            return false; // Evita que se cierre la ventana de SweetAlert
        }
    });
}

/**
 * Abre el modal de SweetAlert para editar un producto existente.
 * Pre-llena los campos con la información recibida.
 * ACTUALIZADO: Se eliminó la sección de ingreso de stock (ahora tiene su propio modal)
 * y se integró el checkbox para indicar si el producto aplica IVA (aplica_iva).
 */
function editarProducto(id, codigo, nombre, compra, aplica_iva, minimo, proveedor_id, categoria) {
    let proveedoresHtml = generarHtmlProveedores(proveedor_id);

    // Si el producto tiene IVA, se marca el checkbox predeterminadamente
    let ivaCheckedHtml = aplica_iva ? 'checked' : '';

    Swal.fire({
        title: 'Editar Producto',
        width: '1200px',
        html: `
            <input id="prod_id" type="hidden" value="${id}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-left mt-4">
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Código/SKU</label>
                    <input id="prod_codigo" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-100" placeholder="Código de Barras/SKU" value="${codigo}" required readonly>
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Nombre</label>
                    <input id="prod_nombre" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre del Producto" oninput="this.value = this.value.toUpperCase()" value="${nombre}" required>
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Proveedor</label>
                    ${proveedoresHtml}
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Categoría / Tipo</label>
                    ${generarHtmlCategorias(categoria)}
                    <input id="prod_nueva_categoria" type="text" style="display:none;" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 mt-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe la nueva categoría..." oninput="this.value = this.value.toUpperCase()">
                </div>
                <div id="div_gramaje" class="hidden"></div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Costo Neto</label>
                    <input id="prod_compra" type="number" step="0.01" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Precio de Compra $" value="${compra}" oninput="if(document.getElementById('prod_iva').checked) calcularIvaCosto(document.getElementById('prod_iva'))">
                    <div class="mt-2 flex items-center">
                        <input type="checkbox" id="prod_iva" class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" onchange="calcularIvaCosto(this)">
                        <label for="prod_iva" class="text-xs text-gray-600 font-medium cursor-pointer">Sumar 16% IVA</label>
                    </div>
                </div>
                <div>
                    <label class="text-sm text-gray-600 font-bold mb-1 block">Costo con IVA</label>
                    <input id="prod_iva_resultado" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 bg-gray-100 text-gray-500 font-bold cursor-not-allowed" value="---" readonly disabled>
                </div>
                <div>
                    <label id="label_prod_minimo" class="text-sm text-gray-600 font-bold mb-1 block">Stock Mínimo</label>
                    <div class="flex">
                        <input id="prod_minimo" type="number" class="w-full border border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Cantidad (Piezas)" value="${minimo}">
                        <input type="hidden" id="prod_minimo_tipo" value="unidad">
                    </div>
                </div>
            </div>
        `,
        showCloseButton: true,
        focusConfirm: false,
        showCancelButton: true,
        customClass: {
            confirmButton: 'bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 ml-2',
            cancelButton: 'bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded shadow transition-all active:scale-95 mr-2'
        },
        buttonsStyling: false,
        confirmButtonText: 'Actualizar',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            if (aplica_iva) {
                calcularIvaCosto(document.getElementById('prod_iva'));
            }
            
            const divGramaje = document.getElementById('div_gramaje');
            if (divGramaje && categoria) {
                const catUpper = categoria.toUpperCase();
                if (catUpper.includes('PAPEL') || catUpper.includes('OPALINA') || catUpper.includes('CARTULINA') || catUpper.includes('IMPRESI')) {
                    divGramaje.classList.remove('hidden');
                }
            }
            
            // Soporte de teclado (Enter para guardar)
            const popup = Swal.getPopup();
            popup.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    Swal.clickConfirm();
                }
            });
            
            // Set stock minimo
            document.getElementById('prod_minimo').value = parseInt('${minimo}') || 0;
            document.getElementById('prod_minimo_tipo').value = 'unidad';
        },
        preConfirm: () => {
            const nombre = document.getElementById('prod_nombre').value;
            if (!nombre) {
                Swal.showValidationMessage('El nombre es obligatorio');
                return false;
            }
            
            // Calculamos el mínimo de stock y lo enviamos
            let minimoIngresado = document.getElementById('prod_minimo').value || 0;
            let minimoTipo = document.getElementById('prod_minimo_tipo') ? document.getElementById('prod_minimo_tipo').value : 'unidad';
            let minimoCalculado = minimoIngresado;
            
            // Estructura de datos que se enviará a Livewire para guardar
            const data = {
                id: document.getElementById('prod_id').value,
                codigo: document.getElementById('prod_codigo').value,
                nombre: nombre,
                precio_compra: document.getElementById('prod_compra').value || 0,
                aplica_iva: document.getElementById('prod_iva') ? document.getElementById('prod_iva').checked : false, // Guarda el estado del IVA
                precio_venta: 0,
                stock_minimo: minimoCalculado,
                proveedor_id: document.getElementById('prod_proveedor').value || null,
                categoria: document.getElementById('prod_categoria').value === '_nuevo' ? document.getElementById('prod_nueva_categoria').value : (document.getElementById('prod_categoria').value || null),
            };
            
            // Disparamos el evento a Livewire
            Livewire.dispatch('guardarProducto', [data]);
            return false; // Evita que se cierre la ventana de SweetAlert
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
                stopKeydownPropagation: false,
                html: `
                    <div class="text-left px-4 mt-4" style="min-height: 250px;">
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
                        dropdownContainer: document.querySelector('.swal2-popup'),
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
                    const fullNumber = window.iti.getNumber();
                    const cleanNumber = fullNumber.replace(/[^0-9]/g, '');
                    const mensaje = "TAL PARECE QUE HAY UN FALTANTE EN VECTOR LAB PORFAVOR CONSULTA AL ADMIN PARA SABER CUAL";
                    const url = `https://wa.me/${cleanNumber}?text=${encodeURIComponent(mensaje)}`;
                    // Evita el bloqueo de popups abriendo la URL directamente aquí durante el evento click
                    window.open(url, '_blank');
                    return true;
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
        <div class="md:col-span-4 mt-2 pt-4 border-t border-gray-200">
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
}

/**
 * Calculadora rápida de IVA para el campo Costo
 */
function calcularIvaCosto(checkbox) {
    const inputCosto = document.getElementById('prod_compra');
    const inputResultado = document.getElementById('prod_iva_resultado');
    if (!inputCosto || !inputCosto.value || isNaN(inputCosto.value)) {
        if(inputResultado) inputResultado.value = '---';
        return;
    }
    
    let costo = parseFloat(inputCosto.value);
    if (checkbox.checked) {
        let total = costo * 1.16;
        if(inputResultado) inputResultado.value = '$' + total.toFixed(2);
    } else {
        if(inputResultado) inputResultado.value = '---';
    }
}

function actualizarOpcionesStockMinimo(tipoMedida) {
    const selectMinimo = document.getElementById('prod_minimo_tipo');
    if (!selectMinimo) return;
    
    // Guardar el valor actual para intentar re-seleccionarlo si sigue existiendo
    const currentVal = selectMinimo.value;
    
    // Opciones base
    let options = `
        <option value="unidad">Unidades</option>
        <option value="paquete">Paquetes</option>
        <option value="caja">Cajas</option>
    `;
    
    // Añadir opción dinámica según el tipo de medida
    if (tipoMedida) {
        if (tipoMedida === 'g') options += '<option value="g">Gramos (g)</option>';
        if (tipoMedida === 'kg') options += '<option value="kg">Kilogramos (kg)</option>';
        if (tipoMedida === 'ml') options += '<option value="ml">Mililitros (ml)</option>';
        if (tipoMedida === 'l') options += '<option value="l">Litros (l)</option>';
        if (tipoMedida === 'mm') options += '<option value="mm">Milímetros (mm)</option>';
        if (tipoMedida === 'cm') options += '<option value="cm">Centímetros (cm)</option>';
        if (tipoMedida === 'm') options += '<option value="m">Metros (m)</option>';
    }
    
    selectMinimo.innerHTML = options;
    
    // Intentar re-seleccionar
    if (Array.from(selectMinimo.options).some(opt => opt.value === currentVal)) {
        selectMinimo.value = currentVal;
    } else {
        selectMinimo.value = 'unidad';
    }
}

/**
 * Agrega la medida seleccionada o ingresada al nombre del producto automáticamente
 */
function aplicarMedidaAlNombre() {
    const inputOSelect = document.getElementById('prod_gramaje');
    const inputTipo = document.getElementById('prod_medida_tipo');
    const nombreInput = document.getElementById('swal2-html-container') ? document.getElementById('swal2-html-container').querySelector('#prod_nombre') : document.getElementById('prod_nombre');
    
    if (!inputOSelect || !nombreInput || !inputOSelect.value || !inputTipo) return;
    
    const valor = inputOSelect.value.trim();
    const tipo = inputTipo.value;
    
    // Regex para detectar y remover cualquier terminación de medida previa (ej. 90G, 3MM, 500ML)
    let baseNombre = nombreInput.value.replace(/\s\d+(\.\d+)?(G|KG|ML|L|MM|CM|M)$/, '');
    
    let suffix = '';
    if (tipo === 'gramaje_papel') suffix = valor + 'G';
    else if (tipo === 'grosor_laser') suffix = valor + 'MM';
    else suffix = valor + tipo.toUpperCase();
    
    nombreInput.value = (baseNombre + ' ' + suffix).trim().toUpperCase();
}

window.addEventListener('mostrarHistorial', event => {
    let data = event.detail[0] || event.detail;
    let historial = data.historial;
    let nombre = data.nombre;
    let id = data.id;
    
    if (!historial || historial.length === 0) {
        Swal.fire('Historial vacío', 'No hay registros de auditoría para este producto.', 'info');
        return;
    }
    
    let html = '<div class="text-left" style="padding-left: 10px; margin-top: 20px;"><ul style="border-left: 2px solid #e5e7eb; margin-left: 1rem; padding-left: 0; list-style: none;">';
    historial.forEach(h => {
        let detallesHtml = '';
        if (h.detalles && Array.isArray(h.detalles)) {
            detallesHtml = '<ul style="list-style-type: disc; padding-left: 1.5rem; margin-top: 0.5rem; font-size: 0.875rem; color: #4b5563;">';
            h.detalles.forEach(d => {
                detallesHtml += `<li>${d}</li>`;
            });
            detallesHtml += '</ul>';
        }
        
        let colorClass = 'bg-blue-500';
        if (h.accion === 'CREADO') colorClass = 'bg-green-500';
        if (h.accion === 'ELIMINADO') colorClass = 'bg-red-500';
        
        let iconClass = h.accion === 'EDITADO' ? 'fa-pen' : (h.accion === 'CREADO' ? 'fa-plus' : 'fa-trash');
        if (h.accion === 'INGRESO_STOCK') {
            colorClass = 'bg-purple-500';
            iconClass = 'fa-box-open';
        }

        html += `
            <li style="position: relative; margin-bottom: 2rem; padding-left: 2rem;">
                <span style="position: absolute; left: -13px; top: 0px; display: flex; align-items: center; justify-content: center; width: 24px; height: 24px; border-radius: 50%; box-shadow: 0 0 0 8px white;" class="${colorClass} text-white">
                    <i class="fas ${iconClass} text-xs"></i>
                </span>
                <h3 style="display: flex; align-items: center; margin-bottom: 0.25rem; font-size: 1.125rem; font-weight: 600; color: #111827;">${h.accion}</h3>
                <time style="display: block; margin-bottom: 0.5rem; font-size: 0.875rem; font-weight: 400; line-height: 1; color: #9ca3af;">El ${h.fecha} por ${h.usuario}</time>
                ${detallesHtml}
            </li>
        `;
    });
    html += `
            </ul>
        </div>
        <div class="mt-4 text-center border-t border-gray-200 pt-4">
            <a href="/inventario/${id}/historial/export" target="_blank" class="inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <i class="fas fa-file-pdf mr-2"></i> Exportar a PDF
            </a>
        </div>
    `;

    Swal.fire({
        title: `Historial y Auditoría:<br><span class="text-lg text-blue-600">${nombre}</span>`,
        html: html,
        width: '800px',
        showCloseButton: true,
        showConfirmButton: false
    });
});

/**
 * Abre el modal de SweetAlert exclusivo para ingresar nuevo stock a un producto.
 * Permite usar la calculadora de inventario (por piezas, paquetes, cajas).
 */
function ingresarStockModal(id, nombre, stock_actual, ingreso_tipo, ingreso_paquetes, ingreso_unidades) {
    Swal.fire({
        title: 'Ingreso de Inventario',
        width: '800px',
        html: `
            <div class="text-left">
                <p class="mb-4 text-gray-700">Producto: <strong>${nombre}</strong></p>
                ${generarHtmlCalculadoraStock(stock_actual, ingreso_tipo, ingreso_paquetes, ingreso_unidades)}
            </div>
        `,
        showCloseButton: true,
        showCancelButton: true,
        confirmButtonText: 'Registrar Ingreso',
        cancelButtonText: 'Cancelar',
        didOpen: () => {
            actualizarCalculadoraStock();
        },
        preConfirm: () => {
            const sumado = parseInt(document.getElementById('prod_calc_sumar_real').textContent) || 0;
            if (sumado <= 0) {
                Swal.showValidationMessage('Debes ingresar una cantidad mayor a cero.');
                return false;
            }
            
            const tipo = document.getElementById('prod_ingreso_tipo').value;
            const cant = document.getElementById('prod_ingreso_cant').value;
            const paquetes = document.getElementById('prod_ingreso_paquetes').value;
            const unidades = document.getElementById('prod_ingreso_unidades_paquete').value;
            
            // Generar detalles descriptivos para el registro de auditoría/historial
            let detallesAuditoria = "";
            if (tipo === 'caja') {
                detallesAuditoria = `Se ingresaron ${cant} cajas (${paquetes} pqts/caja, ${unidades} uds/pqt). Total: ${sumado} piezas.`;
            } else if (tipo === 'paquete') {
                detallesAuditoria = `Se ingresaron ${cant} paquetes (${unidades} uds/pqt). Total: ${sumado} piezas.`;
            } else {
                detallesAuditoria = `Se ingresaron ${cant} piezas individuales.`;
            }

            // Despacha evento a Livewire para sumar el stock y guardar el historial
            Livewire.dispatch('registrarIngresoStock', [id, sumado, detallesAuditoria]);
        }
    });
}
