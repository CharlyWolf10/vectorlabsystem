/* =========================================
   Controlador JavaScript de Arqueos
========================================= */

window.addEventListener('swal:success', event => {
    Swal.fire({
        icon: 'success',
        title: event.detail[0].title,
        text: event.detail[0].text,
    });
});

function abrirCaja() {
    Swal.fire({
        title: 'Apertura de Caja',
        text: 'Ingresa el conteo físico de monedas y billetes (Debe sumar $500 exactos).',
        width: 600,
        html: `
            <div class="grid grid-cols-2 gap-4 text-left">
                <div>
                    <h4 class="font-bold text-gray-700 mb-2">Billetes</h4>
                    <div class="flex justify-between items-center mb-1"><label>$500</label> <input id="a_b_500" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$200</label> <input id="a_b_200" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$100</label> <input id="a_b_100" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$50</label> <input id="a_b_50" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700 mb-2">Monedas</h4>
                    <div class="flex justify-between items-center mb-1"><label>$20</label> <input id="a_m_20" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$10</label> <input id="a_m_10" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$5</label> <input id="a_m_5" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$2</label> <input id="a_m_2" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$1</label> <input id="a_m_1" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$0.50</label> <input id="a_m_50c" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotalAbrir()"></div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-300 text-right">
                <span class="text-xl">Total: </span>
                <span class="text-2xl font-bold text-blue-600" id="total_abrir">$0.00</span>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Continuar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const b500 = parseInt(document.getElementById('a_b_500').value) || 0;
            const b200 = parseInt(document.getElementById('a_b_200').value) || 0;
            const b100 = parseInt(document.getElementById('a_b_100').value) || 0;
            const b50 = parseInt(document.getElementById('a_b_50').value) || 0;
            const m20 = parseInt(document.getElementById('a_m_20').value) || 0;
            const m10 = parseInt(document.getElementById('a_m_10').value) || 0;
            const m5 = parseInt(document.getElementById('a_m_5').value) || 0;
            const m2 = parseInt(document.getElementById('a_m_2').value) || 0;
            const m1 = parseInt(document.getElementById('a_m_1').value) || 0;
            const m50c = parseInt(document.getElementById('a_m_50c').value) || 0;
            
            const total = (b500 * 500) + (b200 * 200) + (b100 * 100) + (b50 * 50) +
                          (m20 * 20) + (m10 * 10) + (m5 * 5) + (m2 * 2) + (m1 * 1) + (m50c * 0.5);

            if(total !== 500) {
                Swal.showValidationMessage('El conteo inicial DEBE sumar exactamente $500 pesos. Tienes: $' + total);
                return false;
            }
            return total;
        }
    }).then((result) => {
        if(result.isConfirmed) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se registrará este monto como tu fondo inicial del turno ($500).",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0066ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, abrir caja'
            }).then((confirmResult) => {
                if(confirmResult.isConfirmed){
                    Livewire.dispatch('abrirCaja', [parseFloat(result.value)]);
                }
            });
        }
    });
}

function cerrarCaja() {
    Swal.fire({
        title: 'Conteo Físico de Billetes y Monedas',
        width: 600,
        html: `
            <div class="grid grid-cols-2 gap-4 text-left">
                <div>
                    <h4 class="font-bold text-gray-700 mb-2">Billetes</h4>
                    <div class="flex justify-between items-center mb-1"><label>$500</label> <input id="b_500" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$200</label> <input id="b_200" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$100</label> <input id="b_100" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$50</label> <input id="b_50" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                </div>
                <div>
                    <h4 class="font-bold text-gray-700 mb-2">Monedas</h4>
                    <div class="flex justify-between items-center mb-1"><label>$20</label> <input id="m_20" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$10</label> <input id="m_10" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$5</label> <input id="m_5" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$2</label> <input id="m_2" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$1</label> <input id="m_1" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                    <div class="flex justify-between items-center mb-1"><label>$0.50</label> <input id="m_50c" type="number" value="0" min="0" class="w-24 border rounded px-2" onchange="calcTotal()"></div>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-300 text-right">
                <span class="text-xl">Total Físico: </span>
                <span class="text-2xl font-bold text-green-600" id="total_arqueo">$0.00</span>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Continuar con el Cierre',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const b500 = parseInt(document.getElementById('b_500').value) || 0;
            const b200 = parseInt(document.getElementById('b_200').value) || 0;
            const b100 = parseInt(document.getElementById('b_100').value) || 0;
            const b50 = parseInt(document.getElementById('b_50').value) || 0;
            const m20 = parseInt(document.getElementById('m_20').value) || 0;
            const m10 = parseInt(document.getElementById('m_10').value) || 0;
            const m5 = parseInt(document.getElementById('m_5').value) || 0;
            const m2 = parseInt(document.getElementById('m_2').value) || 0;
            const m1 = parseInt(document.getElementById('m_1').value) || 0;
            const m50c = parseInt(document.getElementById('m_50c').value) || 0;
            
            const total = (b500 * 500) + (b200 * 200) + (b100 * 100) + (b50 * 50) +
                          (m20 * 20) + (m10 * 10) + (m5 * 5) + (m2 * 2) + (m1 * 1) + (m50c * 0.5);

            return {
                b_500: b500, b_200: b200, b_100: b100, b_50: b50,
                m_20: m20, m_10: m10, m_5: m5, m_2: m2, m_1: m1, m_50c: m50c,
                total: total
            }
        }
    }).then((result) => {
        if(result.isConfirmed) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Se cerrará el turno y se registrará este arqueo en la base de datos.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#0066ff',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, cerrar caja'
            }).then((confirmResult) => {
                if(confirmResult.isConfirmed){
                    Livewire.dispatch('cerrarCaja', [result.value]);
                }
            });
        }
    });
}

// Available globally for the modal
window.calcTotal = function() {
    const b500 = parseInt(document.getElementById('b_500').value) || 0;
    const b200 = parseInt(document.getElementById('b_200').value) || 0;
    const b100 = parseInt(document.getElementById('b_100').value) || 0;
    const b50 = parseInt(document.getElementById('b_50').value) || 0;
    const m20 = parseInt(document.getElementById('m_20').value) || 0;
    const m10 = parseInt(document.getElementById('m_10').value) || 0;
    const m5 = parseInt(document.getElementById('m_5').value) || 0;
    const m2 = parseInt(document.getElementById('m_2').value) || 0;
    const m1 = parseInt(document.getElementById('m_1').value) || 0;
    const m50c = parseInt(document.getElementById('m_50c').value) || 0;
    
    const total = (b500 * 500) + (b200 * 200) + (b100 * 100) + (b50 * 50) +
                  (m20 * 20) + (m10 * 10) + (m5 * 5) + (m2 * 2) + (m1 * 1) + (m50c * 0.5);
    
    document.getElementById('total_arqueo').innerText = "$" + total.toFixed(2);
}

window.calcTotalAbrir = function() {
    const b500 = parseInt(document.getElementById('a_b_500').value) || 0;
    const b200 = parseInt(document.getElementById('a_b_200').value) || 0;
    const b100 = parseInt(document.getElementById('a_b_100').value) || 0;
    const b50 = parseInt(document.getElementById('a_b_50').value) || 0;
    const m20 = parseInt(document.getElementById('a_m_20').value) || 0;
    const m10 = parseInt(document.getElementById('a_m_10').value) || 0;
    const m5 = parseInt(document.getElementById('a_m_5').value) || 0;
    const m2 = parseInt(document.getElementById('a_m_2').value) || 0;
    const m1 = parseInt(document.getElementById('a_m_1').value) || 0;
    const m50c = parseInt(document.getElementById('a_m_50c').value) || 0;
    
    const total = (b500 * 500) + (b200 * 200) + (b100 * 100) + (b50 * 50) +
                  (m20 * 20) + (m10 * 10) + (m5 * 5) + (m2 * 2) + (m1 * 1) + (m50c * 0.5);
    
    const display = document.getElementById('total_abrir');
    display.innerText = "$" + total.toFixed(2);
    if(total === 500) {
        display.className = "text-2xl font-bold text-green-600";
    } else {
        display.className = "text-2xl font-bold text-red-600";
    }
}
