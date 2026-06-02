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
            background-color: #000000; /* Fondo totalmente negro */
            margin: 0;
            overflow: hidden;
            color: #ffffff;
        }

        /* Fade-in and float exclusively for the logo area */
        .logo-transition {
            animation: fadeInLogo 2s ease-out forwards;
            opacity: 0;
        }
        @keyframes fadeInLogo {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }

        .animated-logo {
            animation: float 6s ease-in-out infinite, pulse-glow 4s ease-in-out infinite alternate;
            /* Tenue glow around the logo image */
            filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.2));
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        @keyframes pulse-glow {
            from { filter: drop-shadow(0 0 10px rgba(59, 130, 246, 0.1)); }
            to { filter: drop-shadow(0 0 30px rgba(59, 130, 246, 0.5)); }
        }

        /* Subtle tech background effects ONLY on the left side (logo side) */
        .tech-container {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            overflow: hidden;
            z-index: 0;
            pointer-events: none;
        }

        .tech-grid {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background-image: 
                linear-gradient(rgba(59, 130, 246, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.04) 1px, transparent 1px);
            background-size: 30px 30px;
            animation: panGrid 20s linear infinite;
        }

        @keyframes panGrid {
            0% { transform: translateY(0); }
            100% { transform: translateY(30px); }
        }

        .floating-node {
            position: absolute;
            background: radial-gradient(circle, rgba(37,99,235,0.06) 0%, rgba(0,0,0,0) 70%);
            border-radius: 50%;
            animation: floatNode 8s infinite alternate ease-in-out;
        }

        .node-1 { top: 10%; left: 20%; width: 200px; height: 200px; animation-delay: 0s; }
        .node-2 { bottom: 20%; right: 10%; width: 250px; height: 250px; animation-delay: 2s; }

        @keyframes floatNode {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(20px, -20px) scale(1.1); }
        }

        /* Form styling */
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

        input:-webkit-autofill,
        input:-webkit-autofill:hover, 
        input:-webkit-autofill:focus, 
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 30px #0a0a0c inset !important;
            -webkit-text-fill-color: white !important;
        }
    </style>
</head>
<body class="font-sans antialiased h-screen flex flex-col md:flex-row bg-black">
    
    <!-- LEFT SIDE: LOGO & TRANSITIONS -->
    <div class="relative w-full md:w-1/2 h-1/3 md:h-full flex items-center justify-center bg-black border-r border-gray-900 logo-transition">
        
        <!-- Tech effects confined to this side -->
        <div class="tech-container">
            <div class="tech-grid"></div>
            <div class="floating-node node-1"></div>
            <div class="floating-node node-2"></div>
        </div>

        <div class="z-10 flex flex-col items-center">
            <!-- Requested VectorLab.png -->
            <img src="https://charlywolf10.github.io/VectorLab/assets/img/VectorLab.png" alt="Vector Lab Logo" class="w-64 md:w-[24rem] h-auto animated-logo object-contain">
        </div>
    </div>

    <!-- RIGHT SIDE: STATIC FORM -->
    <div class="w-full md:w-1/2 h-2/3 md:h-full flex flex-col justify-center items-center p-8 bg-black z-20">
        
        <div class="w-full max-w-md bg-[#0a0a0c] p-8 md:p-10 rounded-2xl shadow-[0_0_30px_rgba(0,0,0,0.8)] border border-gray-800">
            <h2 class="text-3xl font-bold text-white mb-2">Bienvenido de nuevo</h2>
            <p class="text-gray-400 mb-8">Ingresa tus credenciales para acceder al sistema.</p>

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
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                            class="dark-input block w-full pl-10 sm:text-sm rounded-md h-12 transition-colors placeholder-gray-600" placeholder="usuario@vectorlab.com">
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
                            class="dark-input block w-full pl-10 pr-10 sm:text-sm rounded-md h-12 transition-colors placeholder-gray-600" placeholder="••••••••">
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
                        INICIAR SESIÓN <i class="fas fa-sign-in-alt ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
        
        <div class="text-center mt-6">
            <p class="text-xs text-gray-600">&copy; {{ date('Y') }} Vector Lab. Todos los derechos reservados.</p>
        </div>
    </div>

    <script>
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
    </script>
</body>
</html>
