/* =========================================
   Controlador JavaScript de Cotizador
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
