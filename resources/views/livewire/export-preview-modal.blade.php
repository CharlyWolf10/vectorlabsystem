<div>
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative bg-white rounded-xl shadow-2xl overflow-hidden sm:my-8 sm:max-w-3xl sm:w-full border border-gray-200 flex flex-col h-[90vh]">
                
                <!-- Header with Actions -->
                <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap gap-4 justify-between items-center bg-gray-50">
                    <h3 class="text-lg leading-6 font-bold text-gray-900 flex items-center" id="modal-title">
                        <i class="fas fa-file-alt text-blue-600 mr-2"></i> {{ $title }}
                    </h3>
                    
                    <div class="flex flex-wrap gap-2 items-center">
                        <button type="button" onclick="shareEmail()" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#d33] text-sm font-medium text-white hover:bg-[#b92b2b] focus:outline-none items-center transition-colors">
                            <i class="fas fa-envelope mr-2"></i> Correo
                        </button>

                        <button type="button" onclick="confirmarDescargaPdf()" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#3085d6] text-sm font-medium text-white hover:bg-[#2874ba] focus:outline-none items-center transition-colors">
                            <i class="fas fa-file-pdf mr-2"></i> Descargar PDF
                        </button>

                        <button type="button" onclick="shareWhatsApp()" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#25D366] text-sm font-medium text-white hover:bg-[#20b858] focus:outline-none items-center transition-colors">
                            <i class="fab fa-whatsapp mr-2"></i> WhatsApp
                        </button>
                        
                        <button wire:click="closeModal" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#d33] text-sm font-medium text-white hover:bg-[#b92b2b] focus:outline-none items-center transition-colors ml-2">
                            Cancelar
                        </button>
                    </div>
                </div>

                <!-- Content Area (Iframe) -->
                <div class="flex-1 bg-gray-200 p-6 overflow-y-auto flex justify-center items-start" id="pdf-preview-container">
                    <div class="bg-white shadow-lg relative" style="width: 21cm; height: 29.7cm; max-width: 100%; transform: scale(0.85); transform-origin: top center; margin-bottom: -4cm;">
                        @if($previewUrl)
                            <iframe id="preview-iframe" src="{{ $previewUrl }}" class="w-full h-full border-0 absolute inset-0" title="PDF Preview"></iframe>
                        @else
                            <div class="flex items-center justify-center h-full text-gray-500">
                                Cargando previsualización...
                            </div>
                        @endif
                    </div>
                </div>

                <!-- No Footer required since actions are in the header -->
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadPreviewAsImage() {
            const iframe = document.getElementById('preview-iframe');
            if(!iframe) return;
            
            // html2canvas needs to run inside the iframe context to capture its content correctly
            const iframeWindow = iframe.contentWindow;
            const iframeDocument = iframe.contentDocument;
            
            if (!iframeWindow.html2canvas) {
                const script = iframeDocument.createElement('script');
                script.src = "https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js";
                script.onload = () => {
                    captureIframe(iframeWindow, iframeDocument);
                };
                iframeDocument.head.appendChild(script);
            } else {
                captureIframe(iframeWindow, iframeDocument);
            }
        }

        function captureIframe(iframeWindow, iframeDocument) {
            const container = document.getElementById('pdf-preview-container');
            container.style.opacity = '0.5';

            iframeWindow.html2canvas(iframeDocument.body, {
                scale: 2, 
                useCORS: true,
                logging: false,
                windowWidth: iframeDocument.body.scrollWidth,
                windowHeight: iframeDocument.body.scrollHeight
            }).then(canvas => {
                container.style.opacity = '1';
                
                const link = document.createElement('a');
                link.download = 'Reporte_' + new Date().toISOString().split('T')[0] + '.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            }).catch(err => {
                container.style.opacity = '1';
                console.error("Error capturing image: ", err);
                alert("Hubo un error al generar la imagen.");
            });
        }

        function confirmarDescargaPdf() {
            Swal.fire({
                title: '¿Exportar a PDF?',
                text: '¿Estás seguro que quieres exportar los registros seleccionados a PDF?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, descargar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open('{{ $exportPdfUrl }}', '_blank');
                    Livewire.dispatch('closeModal');
                }
            });
        }

        function shareWhatsApp() {
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
                    if (window.intlTelInput) {
                        window.iti = window.intlTelInput(input, {
                            initialCountry: "mx",
                            preferredCountries: ["mx", "us", "co", "ar", "es"],
                            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                            dropdownContainer: document.querySelector('.swal2-popup'),
                        });
                    }
                    input.addEventListener('input', function() {
                        this.value = this.value.replace(/[^0-9]/g, '').substring(0, 10);
                    });
                },
                preConfirm: () => {
                    const input = document.getElementById('swal-phone');
                    const number = input.value;
                    if (number.length !== 10) {
                        Swal.showValidationMessage('El número debe tener exactamente 10 dígitos numéricos');
                        return false;
                    }
                    const fullNumber = window.iti ? window.iti.getNumber() : number;
                    const cleanNumber = fullNumber.replace(/[^0-9]/g, '');
                    const mensaje = "TAL PARECE QUE HAY UN FALTANTE EN VECTOR LAB PORFAVOR CONSULTA AL ADMIN PARA SABER CUAL";
                    const url = `https://wa.me/${cleanNumber}?text=${encodeURIComponent(mensaje)}`;
                    window.open(url, '_blank');
                    return true;
                }
            });
        }

        function shareEmail() {
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
                    Livewire.dispatch('sendPdfEmail', { email: emailResult.value });
                    Swal.fire('Enviando...', 'El correo está en proceso de envío.', 'info');
                }
            });
        }
    </script>
    @endif
</div>
