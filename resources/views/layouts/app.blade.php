<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'VectorLab ERP/POS') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-100" x-data="{ sidebarOpen: true }">
        <div class="flex h-screen overflow-hidden">
            
            <!-- Main Sidebar Container -->
            <aside class="bg-vl-dark text-white flex flex-col transition-all duration-300 z-20" 
                   :class="{'w-64': sidebarOpen, 'w-0 -translate-x-full': !sidebarOpen}">
                
                <!-- Brand Logo -->
                <div class="h-16 flex items-center justify-center bg-[#0a0a0c] border-b border-gray-800 px-4 shrink-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center text-xl font-bold text-white hover:text-vl-blue transition-colors">
                        <img src="https://charlywolf10.github.io/VectorLab/assets/img/lobo.png" alt="Logo" class="w-8 h-8 mr-2" x-show="sidebarOpen">
                        <span x-show="sidebarOpen">VECTORLAB</span>
                    </a>
                </div>

                <!-- Sidebar Content -->
                <div class="flex-1 overflow-y-auto py-4 px-3 custom-scrollbar">
                    
                    <!-- Sidebar user panel -->
                    <div class="flex items-center pb-4 mb-4 border-b border-gray-800">
                        <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center text-xl font-bold shrink-0">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="ml-3 overflow-hidden">
                            <p class="text-sm font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-green-400 truncate"><i class="fas fa-circle text-[10px] mr-1"></i> Online</p>
                        </div>
                    </div>

                    <!-- Sidebar Menu -->
                    <nav class="space-y-1">
                        <a href="{{ route('dashboard') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 group {{ request()->routeIs('dashboard') ? 'bg-vl-blue hover:bg-vl-blue' : '' }}">
                            <i class="fas fa-tachometer-alt w-6 text-center text-vl-text-muted group-hover:text-white {{ request()->routeIs('dashboard') ? 'text-white' : '' }}"></i>
                            <span class="ml-3 text-sm font-medium">Dashboard</span>
                        </a>

                        <div class="pt-4 pb-2">
                            <p class="text-xs font-semibold text-vl-text-muted uppercase tracking-wider">Módulos</p>
                        </div>

                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('compras') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 group {{ request()->routeIs('compras') ? 'bg-vl-blue hover:bg-vl-blue' : '' }}">
                                <i class="fas fa-shopping-cart w-6 text-center text-vl-text-muted group-hover:text-white {{ request()->routeIs('compras') ? 'text-white' : '' }}"></i>
                                <span class="ml-3 text-sm font-medium">Compras y Pagos</span>
                            </a>
                            
                            <a href="{{ route('inventario') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 group {{ request()->routeIs('inventario') ? 'bg-vl-blue hover:bg-vl-blue' : 'text-vl-text-muted' }}">
                                <i class="fas fa-boxes w-6 text-center group-hover:text-white {{ request()->routeIs('inventario') ? 'text-white' : 'text-vl-text-muted' }}"></i>
                                <span class="ml-3 text-sm font-medium">Inventario</span>
                            </a>
                            
                            <a href="{{ route('clientes') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 group {{ request()->routeIs('clientes') ? 'bg-vl-blue hover:bg-vl-blue' : 'text-vl-text-muted' }}">
                                <i class="fas fa-users w-6 text-center group-hover:text-white {{ request()->routeIs('clientes') ? 'text-white' : 'text-vl-text-muted' }}"></i>
                                <span class="ml-3 text-sm font-medium">Clientes (CRM)</span>
                            </a>
                        @endif
                        
                        <a href="{{ route('pos') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 group {{ request()->routeIs('pos') ? 'bg-vl-blue hover:bg-vl-blue' : 'text-vl-text-muted' }}">
                            <i class="fas fa-cash-register w-6 text-center group-hover:text-white {{ request()->routeIs('pos') ? 'text-white' : 'text-vl-text-muted' }}"></i>
                            <span class="ml-3 text-sm font-medium">Punto de Venta</span>
                        </a>

                        <a href="{{ route('arqueos') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 group {{ request()->routeIs('arqueos') ? 'bg-vl-blue hover:bg-vl-blue' : 'text-vl-text-muted' }}">
                            <i class="fas fa-calculator w-6 text-center group-hover:text-white {{ request()->routeIs('arqueos') ? 'text-white' : 'text-vl-text-muted' }}"></i>
                            <span class="ml-3 text-sm font-medium">Corte de Caja</span>
                        </a>

                        @if(Auth::user()->role === 'admin')
                            <div class="pt-4 pb-2">
                                <p class="text-xs font-semibold text-vl-text-muted uppercase tracking-wider">Ajustes</p>
                            </div>
                            <a href="{{ route('usuarios') }}" class="flex items-center px-3 py-2.5 rounded-lg hover:bg-gray-800 group {{ request()->routeIs('usuarios') ? 'bg-vl-blue hover:bg-vl-blue' : 'text-vl-text-muted' }}">
                                <i class="fas fa-user-shield w-6 text-center group-hover:text-white {{ request()->routeIs('usuarios') ? 'text-white' : 'text-vl-text-muted' }}"></i>
                                <span class="ml-3 text-sm font-medium">Control de Usuarios</span>
                            </a>
                        @endif
                    </nav>
                </div>
            </aside>

            <!-- Content Wrapper -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-gray-100">
                
                <!-- Navbar -->
                <header class="h-16 bg-white shadow-sm flex items-center justify-between px-4 sm:px-6 z-10">
                    <div class="flex items-center">
                        <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 hover:text-gray-700 focus:outline-none p-2 rounded-md hover:bg-gray-100 transition-colors">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Settings Dropdown from Breeze -->
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ml-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')">
                                    <i class="fas fa-user mr-2 text-gray-400"></i> {{ __('Profile') }}
                                </x-dropdown-link>

                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf

                                    <x-dropdown-link :href="route('logout')"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        <i class="fas fa-sign-out-alt mr-2 text-gray-400"></i> {{ __('Log Out') }}
                                    </x-dropdown-link>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </header>

                <!-- Main Content -->
                <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                    @isset($header)
                        <div class="mb-6 flex items-center justify-between">
                            <h1 class="text-2xl font-semibold text-gray-900">{{ $header }}</h1>
                        </div>
                    @endisset
                    
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScripts
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </body>
</html>
