<?php
$doc1 = file_get_contents('C:\Users\akuca\.gemini\antigravity-ide\brain\4731b475-2d96-419d-834c-ceead49d8344\inventario_doc.md');
$doc2 = file_get_contents('C:\Users\akuca\.gemini\antigravity-ide\brain\4731b475-2d96-419d-834c-ceead49d8344\flujo_inventario.md');

$markdown = $doc1 . "\n\n<div style='page-break-after: always;'></div>\n\n" . $doc2;

// Usar json_encode con flags para evitar que las etiquetas <script> rompan el HTML
$markdown_json = json_encode($markdown, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

$html = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación de Inventario</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; padding: 0; }
            .container { box-shadow: none; max-width: 100%; padding: 0; margin: 0; }
            pre, .mermaid { background: white !important; break-inside: avoid; }
            svg { background: white !important; }
            .page-break { page-break-after: always; }
        }
        body { font-family: sans-serif; padding: 2rem; background: #f3f4f6; }
        .container { max-width: 1000px; margin: 0 auto; background: white; padding: 3rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 8px; }
        h1, h2, h3 { color: #1f2937; margin-top: 2rem; margin-bottom: 1rem; font-weight: bold; }
        h1 { font-size: 2.25rem; border-bottom: 2px solid #e5e7eb; padding-bottom: 0.5rem; }
        h2 { font-size: 1.5rem; border-bottom: 1px solid #e5e7eb; padding-bottom: 0.25rem; }
        p, li { color: #4b5563; line-height: 1.6; margin-bottom: 0.5rem; }
        ul { list-style-type: disc; padding-left: 1.5rem; margin-bottom: 1rem; }
        .mermaid-wrapper { background: white; padding: 1rem; border: 1px solid #e5e7eb; border-radius: 8px; margin-bottom: 2rem; display: flex; justify-content: center;}
        hr { margin: 2rem 0; border: none; border-top: 1px solid #e5e7eb; }
        code { background: #f3f4f6; padding: 0.2rem 0.4rem; border-radius: 4px; font-family: monospace; }
        pre code { background: none; }
    </style>
</head>
<body>
    <div class="no-print text-center mb-6">
        <button onclick="window.print()" class="bg-blue-600 text-white px-6 py-2 rounded shadow hover:bg-blue-700 font-bold text-lg">🖨️ Exportar a PDF (Imprimir)</button>
        <p class="text-sm text-gray-500 mt-2">En la ventana de impresión, selecciona "Guardar como PDF".</p>
    </div>
    <div class="container" id="content"></div>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script type="module">
        import mermaid from 'https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs';
        
        // Forzar fondo blanco y tema claro
        mermaid.initialize({ 
            startOnLoad: false, 
            theme: 'default',
            themeVariables: {
                darkMode: false,
                background: '#ffffff'
            }
        });

        // Cargar el markdown codificado desde PHP
        const markdownContent = $markdown_json;

        // Render markdown
        document.getElementById('content').innerHTML = marked.parse(markdownContent);
        
        // Extraer y procesar diagramas
        const codeBlocks = document.querySelectorAll('code.language-mermaid');
        codeBlocks.forEach((block, index) => {
            const pre = block.parentElement;
            const container = document.createElement('div');
            container.className = 'mermaid-wrapper';
            container.innerHTML = `<div class="mermaid" id="mermaid-\${index}">\${block.textContent}</div>`;
            pre.parentNode.replaceChild(container, pre);
        });

        // Renderizar mermaid
        await mermaid.run({
            nodes: document.querySelectorAll('.mermaid'),
        });
        
        // Forzar fondo blanco en los SVGs generados
        document.querySelectorAll('.mermaid svg').forEach(svg => {
            svg.style.backgroundColor = 'white';
        });
    </script>
</body>
</html>
HTML;

file_put_contents('public/Documentacion.html', $html);
echo "HTML generado con éxito en public/Documentacion.html\n";
