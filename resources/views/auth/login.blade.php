<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vector Lab - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #000000;
            margin: 0;
            overflow: hidden; /* Prevent scrolling during splash wipe */
            color: #ffffff;
        }

        /* Splash Screen */
        #splash-screen {
            position: fixed;
            inset: 0;
            background-color: #000000;
            background-image: radial-gradient(circle at center, rgba(29, 78, 216, 0.25) 0%, rgba(15, 23, 42, 0.1) 45%, #000000 80%);
            z-index: 50;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* Efecto de barrido (wipe) hacia arriba */
            transition: transform 1s cubic-bezier(0.77, 0, 0.175, 1);
        }

        .splash-hidden {
            transform: translateY(-100%);
        }

        /* Logo appearance and flash/destello */
        .logo-enter {
            animation: logoFadeIn 1.5s ease-out forwards, flash 3s infinite 1.5s;
            opacity: 0;
            transform: scale(0.8);
        }

        @keyframes logoFadeIn {
            to { opacity: 1; transform: scale(1); }
        }

        @keyframes flash {
            0%, 100% { filter: drop-shadow(0 0 5px rgba(59,130,246,0.1)) brightness(1); }
            50% { filter: drop-shadow(0 0 50px rgba(59,130,246,1)) brightness(1.3); }
        }

        .btn-enter {
            opacity: 0;
            animation: btnFadeIn 1s ease-out 1.5s forwards;
        }

        @keyframes btnFadeIn {
            to { opacity: 1; transform: translateY(0); }
            from { opacity: 0; transform: translateY(20px); }
        }

        /* Login Layout */
        #login-content {
            display: flex;
            height: 100vh;
            width: 100vw;
        }

        /* Left Side (Welcome Text) */
        .login-left-legend {
            display: none;
            width: 50%;
            background-color: #0a0a0c;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            text-align: center;
            border-right: 1px solid #1f2937;
            position: relative;
        }
        @media (min-width: 768px) {
            .login-left-legend { display: flex; }
        }

        .slideshow-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.5s ease-in-out;
            z-index: 1;
        }
        .slideshow-bg.active {
            opacity: 1;
        }
        .blue-overlay {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at center, rgba(30, 58, 138, 0.85) 0%, rgba(0, 0, 0, 1) 90%);
            z-index: 2;
        }
        .neon-text {
            text-shadow: 0 0 10px rgba(59, 130, 246, 0.8), 0 0 20px rgba(59, 130, 246, 0.8), 0 0 40px rgba(59, 130, 246, 0.6);
        }

        /* Right Side (Form) */
        .login-right-form {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: radial-gradient(circle at center, rgb(30, 58, 138) 0%, rgb(0, 0, 0) 90%);
        }
        @media (min-width: 768px) {
            .login-right-form { width: 50%; }
        }

        /* Dark Form styling */
        .dark-input {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }
        .dark-input:focus {
            background: rgba(0, 0, 0, 0.8) !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 1px #3b82f6 !important;
        }
        input:-webkit-autofill, input:-webkit-autofill:hover, input:-webkit-autofill:focus, input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0a0a0c inset !important;
            -webkit-text-fill-color: white !important;
        }
    </style>
</head>
<body class="font-sans antialiased">
    
    <!-- SPLASH SCREEN (ENTRADA) -->
    <div id="splash-screen">
        <canvas id="matrix-canvas" class="absolute inset-0 w-full h-full z-0 opacity-25 pointer-events-none"></canvas>
        <img src="https://charlywolf10.github.io/VectorLab/assets/img/VectorLab.png" alt="Vector Lab Logo" class="logo-enter w-[20rem] md:w-[35rem] object-contain mb-16">
        
        <button type="button" onclick="startLogin()" class="btn-enter px-12 py-4 text-xl font-bold text-white bg-blue-600 rounded-full shadow-[0_0_30px_rgba(37,99,235,0.6)] hover:bg-blue-500 hover:scale-105 transition-all transform flex items-center group">
            INICIAR SESIÓN <i class="fas fa-chevron-right ml-3 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </div>

    <!-- MAIN CONTENT (SISTEMA) -->
    <div id="login-content">
        
        <!-- IZQUIERDA: LEYENDA -->
        <div class="login-left-legend overflow-hidden">
            <!-- Slideshow Backgrounds -->
            <div class="slideshow-bg active" style="background-image: url('{{ asset('assets/img/inventory_bg_1.png') }}');"></div>
            <div class="slideshow-bg" style="background-image: url('{{ asset('assets/img/inventory_bg_2.png') }}');"></div>
            <div class="slideshow-bg" style="background-image: url('{{ asset('assets/img/inventory_bg_3.png') }}');"></div>
            
            <!-- Capa degradado azul transparente -->
            <div class="blue-overlay"></div>
            
            <div class="z-10 flex flex-col items-center relative p-8 w-full">
                <h2 class="text-6xl md:text-8xl font-extrabold text-white mb-8 neon-text tracking-wider text-center">Bienvenido a<br><span class="text-blue-400">Vector Lab</span></h2>
                <p class="text-blue-50 text-4xl md:text-5xl max-w-4xl leading-relaxed font-medium drop-shadow-lg bg-black/40 p-8 rounded-2xl backdrop-blur-sm border border-white/10 text-center">El sistema de control y administración definitiva para optimizar tu negocio.</p>
            </div>
        </div>

        <!-- DERECHA: FORMULARIO -->
        <div class="login-right-form">
            <div class="relative overflow-hidden w-full max-w-md p-8 bg-slate-900/85 backdrop-blur-md rounded-2xl shadow-[0_0_40px_rgba(30,58,138,0.3)] border border-blue-900/50">
                <!-- Canvas de matrix financiero -->
                <canvas id="finance-matrix-canvas" class="absolute inset-0 w-full h-full z-0 opacity-40 pointer-events-none"></canvas>
                
                <div class="relative z-10">
                    <div class="flex justify-center mb-6">
                        <img src="https://charlywolf10.github.io/VectorLab/assets/img/VectorLab.png" alt="Vector Lab Logo" class="w-48 object-contain filter drop-shadow-lg">
                    </div>
                    <h2 class="text-3xl font-bold text-white mb-2 text-center">Acceso Seguro</h2>
                    <p class="text-gray-400 mb-8 text-center">Ingresa tus credenciales para continuar.</p>

                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-300">Correo Electrónico</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-500"></i>
                                </div>
                                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" 
                                    class="dark-input block w-full pl-10 sm:text-sm rounded-md h-12 transition-colors placeholder-gray-600 bg-[#0a0a0c]/80" placeholder="usuario@vectorlab.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-400 text-sm" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-300">Contraseña</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-500"></i>
                                </div>
                                <input id="password" type="password" name="password" required autocomplete="current-password" 
                                    class="dark-input block w-full pl-10 pr-10 sm:text-sm rounded-md h-12 transition-colors placeholder-gray-600 bg-[#0a0a0c]/80" placeholder="••••••••">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer z-20" onclick="togglePassword()">
                                    <i class="fas fa-eye text-gray-500 hover:text-blue-400 transition-colors" id="togglePasswordIcon"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-400 text-sm" />
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between pt-2">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-600 bg-gray-800 rounded">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-400">
                                    Recordarme
                                </label>
                            </div>

                            @if (Route::has('password.request'))
                                <div class="text-sm">
                                    <a href="{{ route('password.request') }}" class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-lg text-sm font-bold text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 focus:ring-offset-gray-900 transition-all transform hover:scale-[1.02]">
                                ENTRAR AL SISTEMA <i class="fas fa-sign-in-alt ml-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        let matrixInterval;
        const canvas = document.getElementById('matrix-canvas');
        if (canvas) {
            const ctx = canvas.getContext('2d');
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
            
            const letters = '01';
            const fontSize = 16;
            const columns = canvas.width / fontSize;
            
            const drops = [];
            for (let x = 0; x < columns; x++) {
                drops[x] = 1;
            }
            
            matrixInterval = setInterval(() => {
                ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                ctx.fillStyle = '#3b82f6';
                ctx.font = fontSize + 'px monospace';
                
                for (let i = 0; i < drops.length; i++) {
                    const text = letters.charAt(Math.floor(Math.random() * letters.length));
                    ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                    
                    if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                        drops[i] = 0;
                    }
                    drops[i]++;
                }
            }, 50);

            window.addEventListener('resize', () => {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            });
        }

        function startLogin() {
            const splash = document.getElementById('splash-screen');
            // Agrega la clase que hace el barrido (deslizar hacia arriba)
            splash.classList.add('splash-hidden');
            
            // Oculta completamente el splash después de la animación para no estorbar
            setTimeout(() => {
                splash.style.display = 'none';
                if (typeof matrixInterval !== 'undefined') clearInterval(matrixInterval);
                document.getElementById('email').focus();
            }, 1000);
        }

        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Si hay errores de validación, saltarse el splash screen e ir directo al form
        @if($errors->any() || session('status'))
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('splash-screen').style.display = 'none';
                if (typeof matrixInterval !== 'undefined') clearInterval(matrixInterval);
            });
        @endif

        // Lógica del Slideshow del fondo
        document.addEventListener('DOMContentLoaded', function() {
            let currentSlide = 0;
            const slides = document.querySelectorAll('.slideshow-bg');
            if (slides.length > 0) {
                setInterval(() => {
                    slides[currentSlide].classList.remove('active');
                    currentSlide = (currentSlide + 1) % slides.length;
                    slides[currentSlide].classList.add('active');
                }, 4000);
            }

            // Lógica del Matrix Financiero en el formulario
            const financeCanvas = document.getElementById('finance-matrix-canvas');
            if (financeCanvas) {
                const fCtx = financeCanvas.getContext('2d');
                
                const resizeFCanvas = () => {
                    financeCanvas.width = financeCanvas.offsetWidth;
                    financeCanvas.height = financeCanvas.offsetHeight;
                };
                resizeFCanvas();
                window.addEventListener('resize', resizeFCanvas);
                
                const fSymbols = ['$', '€', '£', '¥', '%', '#', '@', '&', '+', '-', '↑', '↓', '¢', '฿'];
                const fFontSize = 14;
                const fColumns = 100;
                const fDrops = [];
                for(let x = 0; x < fColumns; x++) fDrops[x] = Math.random() * 50; 

                function drawFinanceMatrix() {
                    fCtx.fillStyle = 'rgba(15, 23, 42, 0.15)'; // Fondo slate transparente para el barrido
                    fCtx.fillRect(0, 0, financeCanvas.width, financeCanvas.height);
                    fCtx.fillStyle = 'rgba(59, 130, 246, 0.8)'; // Azul brilloso
                    fCtx.font = fFontSize + 'px monospace';
                    
                    const activeCols = Math.floor(financeCanvas.width / fFontSize) + 1;
                    
                    for(let i = 0; i < activeCols; i++) {
                        const text = fSymbols[Math.floor(Math.random() * fSymbols.length)];
                        fCtx.fillText(text, i * fFontSize, fDrops[i] * fFontSize);
                        
                        if(fDrops[i] * fFontSize > financeCanvas.height && Math.random() > 0.95) {
                            fDrops[i] = 0;
                        }
                        fDrops[i] += 0.5; // velocidad de caída suave
                    }
                }
                setInterval(drawFinanceMatrix, 50);
            }
        });
    </script>
</body>
</html>
