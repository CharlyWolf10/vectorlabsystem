<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Principal (Dashboard)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Logo Section -->
            <div class="flex flex-col items-center justify-center mb-10">
                <!-- Espacio para el Logo de la Página Web de Vector Lab -->
                <div class="w-48 h-48 bg-gray-200 rounded-full flex items-center justify-center mb-4 overflow-hidden border-4 border-blue-500 shadow-lg">
                    <img src="https://ui-avatars.com/api/?name=Vector+Lab&background=0D8ABC&color=fff&size=512" alt="Vector Lab Logo" id="vector-lab-logo" class="object-cover w-full h-full">
                </div>
                <h1 class="text-3xl font-bold text-gray-800 tracking-wider uppercase">Bienvenido a Vector Lab ERP</h1>
                <p class="text-gray-500 mt-2">Sistema Integral de Administración y Punto de Venta</p>
            </div>

            <!-- Widgets -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Widget 1: Ventas -->
                <div class="bg-blue-600 rounded-lg shadow-md p-6 text-white flex items-center justify-between hover:bg-blue-700 transition">
                    <div>
                        <p class="text-sm uppercase tracking-wide font-semibold opacity-80">Ventas del Día</p>
                        <p class="text-3xl font-bold mt-1">$0.00</p>
                    </div>
                    <i class="fas fa-chart-line text-5xl opacity-50"></i>
                </div>

                <!-- Widget 2: Cuentas por Pagar -->
                <div class="bg-red-500 rounded-lg shadow-md p-6 text-white flex items-center justify-between hover:bg-red-600 transition">
                    <div>
                        <p class="text-sm uppercase tracking-wide font-semibold opacity-80">Por Pagar</p>
                        <p class="text-3xl font-bold mt-1">$0.00</p>
                    </div>
                    <i class="fas fa-file-invoice-dollar text-5xl opacity-50"></i>
                </div>

                <!-- Widget 3: Clientes -->
                <div class="bg-indigo-500 rounded-lg shadow-md p-6 text-white flex items-center justify-between hover:bg-indigo-600 transition">
                    <div>
                        <p class="text-sm uppercase tracking-wide font-semibold opacity-80">Clientes CRM</p>
                        <p class="text-3xl font-bold mt-1">0</p>
                    </div>
                    <i class="fas fa-users text-5xl opacity-50"></i>
                </div>

                <!-- Widget 4: Inventario Alertas -->
                <div class="bg-orange-500 rounded-lg shadow-md p-6 text-white flex items-center justify-between hover:bg-orange-600 transition">
                    <div>
                        <p class="text-sm uppercase tracking-wide font-semibold opacity-80">Alertas Stock</p>
                        <p class="text-3xl font-bold mt-1">0</p>
                    </div>
                    <i class="fas fa-exclamation-triangle text-5xl opacity-50"></i>
                </div>
            </div>
            
        </div>
    </div>
</x-app-layout>
