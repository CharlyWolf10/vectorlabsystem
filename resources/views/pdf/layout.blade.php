<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Reporte')</title>
    <style>
        @page {
            margin: 140px 40px 80px 40px;
        }

        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11px; 
            color: #2d3748; 
        }

        /* WATERMARK */
        @if($logo)
        #watermark {
            position: fixed;
            top: 25%;
            left: 15%;
            width: 70%;
            opacity: 0.05; 
            z-index: -1000;
        }
        
        #watermark img {
            width: 100%;
        }
        @else
        #watermark {
            position: fixed;
            top: 40%;
            left: 10%;
            width: 80%;
            opacity: 0.03; 
            z-index: -1000;
            text-align: center;
            font-size: 100px;
            font-weight: bold;
            color: #0056b3;
            transform: rotate(-30deg);
        }
        @endif

        /* HEADER */
        header { 
            position: fixed; 
            top: -110px; 
            left: 0px; 
            right: 0px; 
            height: 90px; 
            border-bottom: 3px solid #0056b3; 
        }
        
        .header-bg {
            background-color: #f8fafc;
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
            height: 60px;
        }

        .logo { max-width: 160px; max-height: 60px; }
        
        .info-table { width: 100%; border: none; margin: 0; padding: 0; }
        .info-table td { border: none; padding: 0; }
        
        .business-name { margin: 0; color: #0056b3; font-size: 18px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .business-details { font-size: 10px; color: #4a5568; line-height: 1.5; margin-top: 4px; }

        /* FOOTER */
        footer { 
            position: fixed; 
            bottom: -60px; 
            left: 0px; 
            right: 0px; 
            height: 40px; 
            border-top: 2px solid #e2e8f0;
            color: #718096;
            font-size: 9px;
            text-align: center;
            padding-top: 15px;
        }

        .page-number:after { content: counter(page); }

        /* CONTENT STYLES */
        main {
            margin-top: 10px;
        }

        .report-title { 
            font-size: 16px; 
            font-weight: 800; 
            color: #1a202c; 
            margin: 0 0 20px 0; 
            text-align: center; 
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px;
            background-color: #edf2f7;
            border-radius: 4px;
        }

        /* TABLES */
        table.data-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px; 
        }
        
        table.data-table th { 
            background-color: #0056b3; 
            color: #ffffff; 
            padding: 10px 8px; 
            text-align: left; 
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        table.data-table td { 
            padding: 9px 8px; 
            border-bottom: 1px solid #e2e8f0; 
            color: #2d3748;
            font-size: 11px;
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
    <div id="watermark">
        @if($logo)
            <img src="{{ $logo }}" alt="Watermark">
        @else
            VECTOR LAB
        @endif
    </div>

    <!-- Header -->
    <header>
        <div class="header-bg">
            <table class="info-table">
                <tr>
                    <td style="width: 40%; text-align: left; vertical-align: middle;">
                        @if($logo)
                            <img src="{{ $logo }}" class="logo" alt="Logo">
                        @else
                            <!-- Blank space if no logo, to prevent duplicate text -->
                        @endif
                    </td>
                    <td style="width: 60%; text-align: right; vertical-align: middle;">
                        <div class="business-name">{{ isset($perfil) && $perfil ? $perfil->nombre : 'Vector Lab' }}</div>
                        <div class="business-details">
                            @if(isset($perfil) && $perfil)
                                @if($perfil->rfc) <strong>RFC:</strong> {{ $perfil->rfc }} | @endif
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
        </div>
    </header>

    <!-- Footer -->
    <footer>
        <table style="width: 100%; border: none; margin: 0; padding: 0;">
            <tr>
                <td style="width: 33%; text-align: left; border: none;">
                    Generado el: {{ date('d/m/Y H:i') }}
                </td>
                <td style="width: 33%; text-align: center; color: #0056b3; font-weight: bold; border: none;">
                    {{ isset($perfil) && $perfil ? $perfil->nombre : 'Vector Lab' }}
                </td>
                <td style="width: 33%; text-align: right; border: none;">
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
