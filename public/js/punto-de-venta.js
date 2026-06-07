/* =========================================
   Controlador JavaScript de Punto de Venta
========================================= */

/**
 * Escucha notificaciones "toast" desde Livewire
 */
window.addEventListener('swal:toast', event => {
    Swal.fire({
        toast: true,
        position: 'top-end',
        icon: 'success',
        title: event.detail[0].title,
        showConfirmButton: false,
        timer: 1500
    });
});

/**
 * Escucha notificaciones exitosas desde Livewire
 */
window.addEventListener('swal:success', event => {
    Swal.fire({
        icon: 'success',
        title: event.detail[0].title,
        text: event.detail[0].text,
    });
});

/**
 * Muestra el ticket de venta al finalizar una transacción
 */
window.addEventListener('swal:ticket', event => {
    const t = event.detail[0];
    let ticketHtml = `
        <div class="text-left font-mono text-sm">
            <p class="text-center font-bold text-xl mb-2">VECTOR LAB</p>
            <p class="text-center mb-4">Ticket #${t.id} - ${t.fecha}</p>
            <hr class="border-dashed my-2 border-gray-400">
            <p>Subtotal: <span class="float-right">$${parseFloat(t.subtotal).toFixed(2)}</span></p>
            <p>Descuento (${t.descuento_porcentaje}%): <span class="float-right text-red-500">-$${parseFloat(t.descuento_monto).toFixed(2)}</span></p>
            <p class="font-bold text-lg mt-2">TOTAL: <span class="float-right">$${parseFloat(t.total).toFixed(2)}</span></p>
            <hr class="border-dashed my-2 border-gray-400">
            <p>Método de Pago: <span class="float-right uppercase">${t.metodo.replace('_', ' ')}</span></p>
    `;
    
    if (t.metodo === 'efectivo') {
        ticketHtml += `
            <p>Pagó con: <span class="float-right">$${parseFloat(t.pago_con).toFixed(2)}</span></p>
            <p>Cambio: <span class="float-right">$${parseFloat(t.cambio).toFixed(2)}</span></p>
        `;
    }

    ticketHtml += `
            <p>Requiere Factura: <span class="float-right">${t.factura ? 'SÍ' : 'NO'}</span></p>
            <hr class="border-dashed my-2 border-gray-400">
            <p class="text-center mt-4">¡Gracias por su compra!</p>
        </div>
    `;

    Swal.fire({
        title: 'Venta Registrada',
        html: ticketHtml,
        icon: 'success',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#10b981'
    });
});

/**
 * Abre el modal para cobrar una venta
 */
function cobrar(total) {
    if (total <= 0) return;
    
    Swal.fire({
        title: 'Cobrar Venta',
        html: `
            <h2 id="total_display" class="text-3xl text-green-600 font-bold mb-4" data-total="${total}">$${total.toFixed(2)}</h2>
            <select id="metodo_cobro" class="swal2-select w-full mb-4" onchange="toggleEfectivo()">
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta_credito">Tarjeta de Crédito</option>
                <option value="tarjeta_debito">Tarjeta de Débito</option>
                <option value="transferencia">Transferencia</option>
                <option value="credito">Crédito a Cliente</option>
            </select>
            
            <select id="descuento" class="swal2-select w-full mb-4" onchange="actualizarTotalCobro()">
                <option value="0">Sin Descuento (0%)</option>
                <option value="5">Descuento 5%</option>
                <option value="10">Descuento 10%</option>
                <option value="15">Descuento 15%</option>
                <option value="20">Descuento 20%</option>
            </select>

            <div id="efectivo_fields">
                <input id="pago_con" type="number" step="0.01" class="swal2-input w-full" placeholder="Pagó con (Ej: 500)" onkeyup="calcularCambio()">
                <p class="text-right text-gray-600 font-bold mt-2 text-lg">Cambio: <span id="cambio_display" class="text-blue-600">$0.00</span></p>
            </div>

            <div class="mt-4 text-left border-t pt-4">
                <label class="flex items-center space-x-2 cursor-pointer font-bold">
                    <input type="checkbox" id="req_factura" class="form-checkbox text-blue-600 w-5 h-5">
                    <span>¿Requiere Factura?</span>
                </label>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Confirmar Pago',
        confirmButtonColor: '#10b981',
        cancelButtonText: 'Cancelar',
        didOpen: () => { toggleEfectivo(); },
        preConfirm: () => {
            const metodo = document.getElementById('metodo_cobro').value;
            const clienteId = document.getElementById('select_cliente_venta').value;
            const descuento = document.getElementById('descuento').value;
            const reqFactura = document.getElementById('req_factura').checked;
            const pagoCon = document.getElementById('pago_con').value;
            
            const totalBase = parseFloat(document.getElementById('total_display').getAttribute('data-total'));
            const descMonto = totalBase * (descuento / 100);
            const totalFinal = totalBase - descMonto;
            
            const pagoConFloat = parseFloat(pagoCon) || 0;
            let cambio = 0;

            if ((metodo === 'credito' || reqFactura) && !clienteId) {
                Swal.showValidationMessage('Debe seleccionar un cliente del panel derecho para Crédito o Factura');
                return false;
            }

            if (metodo === 'efectivo') {
                if (pagoConFloat < totalFinal) {
                    Swal.showValidationMessage('El monto "Pagó con" debe ser mayor o igual al total');
                    return false;
                }
                cambio = pagoConFloat - totalFinal;
            }

            return { 
                metodo: metodo, 
                clienteId: clienteId, 
                descuento: descuento,
                requiere_factura: reqFactura,
                pago_con: pagoConFloat,
                cambio: cambio
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.dispatch('registrarVenta', [result.value]);
        }
    });
}

/**
 * Muestra u oculta los campos de pago en efectivo
 */
function toggleEfectivo() {
    const metodo = document.getElementById('metodo_cobro').value;
    const fields = document.getElementById('efectivo_fields');
    if (metodo === 'efectivo') {
        fields.style.display = 'block';
    } else {
        fields.style.display = 'none';
        document.getElementById('pago_con').value = '';
        document.getElementById('cambio_display').innerText = '$0.00';
    }
}

/**
 * Actualiza el total que se muestra en el modal de cobro (aplicando descuentos)
 */
function actualizarTotalCobro() {
    const totalBase = parseFloat(document.getElementById('total_display').getAttribute('data-total'));
    const desc = document.getElementById('descuento').value;
    const nuevoTotal = totalBase - (totalBase * (desc / 100));
    document.getElementById('total_display').innerText = '$' + nuevoTotal.toFixed(2);
    calcularCambio();
}

/**
 * Calcula el cambio a devolver al cliente
 */
function calcularCambio() {
    const totalBase = parseFloat(document.getElementById('total_display').getAttribute('data-total'));
    const desc = document.getElementById('descuento').value;
    const totalFinal = totalBase - (totalBase * (desc / 100));
    
    const pagoCon = parseFloat(document.getElementById('pago_con').value) || 0;
    const display = document.getElementById('cambio_display');
    
    if (pagoCon >= totalFinal) {
        const cambio = pagoCon - totalFinal;
        display.innerText = '$' + cambio.toFixed(2);
        display.classList.remove('text-red-600');
        display.classList.add('text-blue-600');
    } else {
        display.innerText = 'Faltan $' + (totalFinal - pagoCon).toFixed(2);
        display.classList.remove('text-blue-600');
        display.classList.add('text-red-600');
    }
}
