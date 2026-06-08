<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Reporte')</title>
    <style>
        @page {
            margin: 120px 40px 80px 40px;
        }

        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #333; 
        }

        /* WATERMARK */
        #watermark {
            position: fixed;
            top: 25%;
            left: 15%;
            width: 70%;
            opacity: 0.08; /* Very subtle */
            z-index: -1000;
        }
        
        #watermark img {
            width: 100%;
        }

        /* HEADER */
        header { 
            position: fixed; 
            top: -90px; 
            left: 0px; 
            right: 0px; 
            height: 80px; 
            border-bottom: 3px solid #0056b3; 
            padding-bottom: 10px;
        }
        
        .logo { max-width: 150px; max-height: 70px; }
        
        .info-table { width: 100%; border: none; margin: 0; padding: 0; }
        .info-table td { border: none; padding: 0; }
        
        .business-name { margin: 0; color: #0056b3; font-size: 16px; font-weight: bold; }
        .business-details { font-size: 10px; color: #555; line-height: 1.4; margin-top: 4px; }

        /* FOOTER */
        footer { 
            position: fixed; 
            bottom: -60px; 
            left: 0px; 
            right: 0px; 
            height: 40px; 
            border-top: 2px solid #0056b3;
            color: #555;
            font-size: 9px;
            text-align: center;
            padding-top: 10px;
        }

        .page-number:after { content: counter(page); }

        /* CONTENT STYLES */
        main {
            margin-top: 10px;
        }

        .report-title { 
            font-size: 18px; 
            font-weight: bold; 
            color: #0056b3; 
            margin: 0 0 15px 0; 
            text-align: center; 
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* TABLES */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        
        table.data-table th { 
            background-color: #0056b3; 
            color: white; 
            padding: 8px; 
            text-align: left; 
            font-size: 11px;
            font-weight: bold;
        }
        
        table.data-table td { 
            padding: 8px; 
            border-bottom: 1px solid #e2e8f0; 
            color: #4a5568;
        }
        
        table.data-table tr:nth-child(even) { 
            background-color: #f8fafc; 
        }

        /* UTILITIES */
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .text-red { color: #e53e3e !important; }
        .text-green { color: #38a169 !important; }
        .font-bold { font-weight: bold !important; }
        
        @yield('styles')
    </style>
</head>
<body>

    <!-- Watermark -->
    @if($logo)
    <div id="watermark">
        <img src="{{ $logo }}" alt="Watermark">
    </div>
    @endif

    <!-- Header -->
    <header>
        <table class="info-table">
            <tr>
                <td style="width: 40%; text-align: left; vertical-align: bottom;">
                    @if($logo)
                        <img src="{{ $logo }}" class="logo" alt="Logo">
                    @else
                        <h2 style="margin: 0; color: #0056b3; font-size: 24px; font-weight: bold;">Vector Lab</h2>
                    @endif
                </td>
                <td style="width: 60%; text-align: right; vertical-align: bottom;">
                    <div class="business-name">{{ isset($perfil) && $perfil ? $perfil->nombre : 'Vector Lab' }}</div>
                    <div class="business-details">
                        @if(isset($perfil) && $perfil)
                            @if($perfil->rfc) <strong>RFC:</strong> {{ $perfil->rfc }} <br> @endif
                            @if($perfil->direccion) {{ $perfil->direccion }} <br> @endif
                            @if($perfil->telefono) Tel: {{ $perfil->telefono }} @endif
                            @if($perfil->telefono && $perfil->correo) | @endif
                            @if($perfil->correo) Email: {{ $perfil->correo }} @endif
                            @if($perfil->sitio_web) <br> Web: {{ $perfil->sitio_web }} @endif
                        @else
                            Sistema de Gestión Integral
                        @endif
                    </div>
                </td>
            </tr>
        </table>
    </header>

    <!-- Footer -->
    <footer>
        <table style="width: 100%; border: none;">
            <tr>
                <td style="width: 33%; text-align: left;">
                    Generado el: {{ date('d/m/Y H:i') }}
                </td>
                <td style="width: 33%; text-align: center; color: #0056b3; font-weight: bold;">
                    {{ isset($perfil) && $perfil ? $perfil->nombre : 'Vector Lab' }}
                </td>
                <td style="width: 33%; text-align: right;">
                    Página <span class="page-number"></span>
                </td>
            </tr>
        </table>
    </footer>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

</body>
</html>
