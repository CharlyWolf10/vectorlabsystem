<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Reporte')</title>
    <style>
        @page {
            margin: 150px 40px 100px 40px;
        }

        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #2d3748; 
            margin: 0;
            padding: 0;
        }

        /* WATERMARK */
        #watermark {
            position: fixed;
            top: 25%;
            left: 10%;
            width: 80%;
            opacity: 0.15; /* A little stronger for wolf */
            z-index: -1000;
            text-align: center;
        }
        
        #watermark img {
            width: 100%;
            max-width: 600px;
        }
        
        #watermark img {
            width: 80%;
            max-width: 500px;
        }


        /* BACKGROUND DECORATION */
        .top-bar {
            position: fixed;
            top: -150px;
            left: -40px;
            right: -40px;
            height: 15px;
            background: #0056b3;
        }

        @if(isset($isPreview) && $isPreview)
        .bottom-bg-interactive {
            position: fixed;
            bottom: -50px;
            left: -40px;
            right: -40px;
            height: 350px;
            z-index: -1100;
            background: linear-gradient(to bottom, transparent 0%, rgba(0, 71, 171, 0.9) 100%);
            overflow: hidden;
        }
        @elseif(isset($bg_pdf) && $bg_pdf)
        .bottom-bg-image {
            position: fixed;
            bottom: -100px;
            left: -40px;
            right: -40px;
            height: 400px;
            z-index: -1100;
            opacity: 0.15;
            text-align: right;
        }
        .bottom-bg-image img {
            height: 100%;
            object-fit: cover;
        }
        @else
        .bottom-bar {
            position: fixed;
            bottom: -100px;
            left: -40px;
            right: -40px;
            height: 25px;
            background: #0056b3;
        }
        .bottom-bar-accent {
            position: fixed;
            bottom: -75px;
            left: -40px;
            right: -40px;
            height: 5px;
            background: #cbd5e1;
        }
        @endif

        /* HEADER */
        header { 
            position: fixed; 
            top: -140px; 
            left: 0px; 
            right: 0px; 
            height: 130px; 
            text-align: center;
        }
        
        .header-container {
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 10px;
        }

        .logo { max-width: 250px; max-height: 80px; display: block; margin: 0 auto; }
        
        .business-name { margin: 10px 0 2px 0; color: #0056b3; font-size: 20px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; text-align: center; }
        .business-details { font-size: 11px; color: #4a5568; line-height: 1.4; text-align: center; }

        /* FOOTER */
        footer { 
            position: fixed; 
            bottom: -70px; 
            left: 0px; 
            right: 0px; 
            height: 60px; 
            color: #718096;
            font-size: 10px;
        }

        .page-number:after { content: counter(page); }

        /* CONTENT STYLES */
        main {
            margin-top: 10px; /* Adjusted since header is taller */
        }

        .report-title-container {
            text-align: center;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        .report-title { 
            font-size: 16px; 
            font-weight: bold; 
            color: #1e293b; 
            margin: 0; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* TABLES */
        table.data-table { 
            width: 90%; 
            margin: 0 auto;
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        
        table.data-table th { 
            background-color: #0056b3; 
            color: #ffffff; 
            padding: 12px 10px; 
            text-align: left; 
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            border: 1px solid #004494;
        }
        
        table.data-table td { 
            padding: 10px 10px; 
            border: 1px solid #e2e8f0; 
            color: #334155;
            font-size: 11px;
        }
        
        table.data-table tr:nth-child(even) { 
            background-color: #f8fafc; 
        }

        table.data-table tr:nth-child(odd) { 
            background-color: #ffffff; 
        }

        /* UTILITIES */
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .text-red { color: #dc2626 !important; font-weight: bold; }
        .text-green { color: #16a34a !important; font-weight: bold; }
        .font-bold { font-weight: bold !important; }
        
        @yield('styles')
    </style>
</head>
<body>

    <!-- Top Decoration Bar -->
    <div class="top-bar"></div>
    
    @if(isset($isPreview) && $isPreview)
        <!-- Interactive Background for Preview -->
        <div class="bottom-bg-interactive">
        </div>
    @elseif(isset($bg_pdf) && $bg_pdf)
        <!-- Static Background for PDF -->
        <div class="bottom-bg-image">
            <img src="{{ $bg_pdf }}" alt="Background">
        </div>
    @else
        <div class="bottom-bar"></div>
        <div class="bottom-bar-accent"></div>
    @endif

    <!-- Watermark -->
    <div id="watermark">
        @if(isset($watermark) && $watermark)
            <img src="{{ $watermark }}" alt="Watermark Lobo">
        @elseif(isset($logo) && $logo)
            <img src="{{ $logo }}" alt="Watermark">
        @endif
    </div>

    <!-- Header -->
    <header>
        <div class="header-container">
            @if($logo)
                <img src="{{ $logo }}" class="logo" alt="Logo">
            @else
                <h2 style="margin: 0; color: #0056b3; font-size: 28px; font-weight: 900;">Vector Lab</h2>
            @endif
            
            <div class="business-name">{{ isset($perfil) && $perfil ? $perfil->nombre : 'Vector Lab' }}</div>
            <div class="business-details">
                @if(isset($perfil) && $perfil)
                    @if($perfil->rfc) <strong>RFC:</strong> {{ $perfil->rfc }} | @endif
                    @if($perfil->direccion) {{ $perfil->direccion }} | @endif
                    @if($perfil->telefono) <strong>Tel:</strong> {{ $perfil->telefono }} | @endif
                    @if($perfil->correo) <strong>Email:</strong> {{ $perfil->correo }} @endif
                @else
                    Sistema de Gestión Integral Avanzado
                @endif
            </div>
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <td style="width: 25%; text-align: left; vertical-align: bottom; border: none; color: #64748b;">
                    <strong>Generado:</strong><br>{{ date('d/m/Y H:i') }}<br><br>
                    <strong>Página <span class="page-number"></span></strong>
                </td>
                <td style="width: 75%; text-align: right; vertical-align: bottom; color: #1e293b; border: none; font-size: 10px; line-height: 1.4;">
                    <div style="color: #0056b3; font-weight: 900; text-transform: uppercase; font-size: 13px; margin-bottom: 4px; letter-spacing: 1px;">
                        {{ isset($perfil) && $perfil ? $perfil->nombre : 'Vector Lab' }}
                    </div>
                    @if(isset($perfil) && $perfil)
                        @if($perfil->sitio_web) <strong>Sitio Web:</strong> {{ $perfil->sitio_web }} @endif
                    @else
                        Sistema de Gestión Integral Avanzado
                    @endif
                </td>
            </tr>
        </table>
    </footer>

    <!-- Main Content -->
    <main>
        <div class="report-title-container">
            <h1 class="report-title">@yield('title')</h1>
        </div>
        
        @yield('content')
    </main>

</body>
</html>
