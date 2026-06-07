<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gastos Operativos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Gastos Fijos (Mensual)</h3>
                    <p class="text-sm text-gray-600 mb-6">Ingresa tus gastos fijos mensuales y tus tiempos operativos para calcular exactamente cuánto te cuesta mantener el negocio abierto cada minuto. Este valor se usará en el Cotizador.</p>
                    
                    <form wire:submit.prevent="guardar">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Gastos -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Renta ($)</label>
                                    <input type="number" step="0.01" wire:model="renta" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Luz ($)</label>
                                    <input type="number" step="0.01" wire:model="luz" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Agua ($)</label>
                                    <input type="number" step="0.01" wire:model="agua" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Sueldos Fijos ($)</label>
                                    <input type="number" step="0.01" wire:model="sueldos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Otros Gastos (Internet, etc) ($)</label>
                                    <input type="number" step="0.01" wire:model="otros_gastos" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <!-- Tiempos -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Días trabajados por mes</label>
                                    <input type="number" wire:model="dias_por_mes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Ej. 24">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Horas trabajadas por día</label>
                                    <input type="number" wire:model="horas_por_dia" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Ej. 8">
                                </div>
                                
                                <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
                                    <h4 class="text-sm font-bold text-blue-800 mb-2">Resumen Operativo</h4>
                                    <div class="flex justify-between mb-1">
                                        <span class="text-sm text-gray-600">Total Gastos:</span>
                                        <span class="text-sm font-semibold">${{ number_format((float)$renta + (float)$luz + (float)$agua + (float)$sueldos + (float)$otros_gastos, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between mb-3">
                                        <span class="text-sm text-gray-600">Total Minutos/Mes:</span>
                                        <span class="text-sm font-semibold">{{ (int)$dias_por_mes * (int)$horas_por_dia * 60 }} min</span>
                                    </div>
                                    <div class="pt-3 border-t border-blue-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-base font-bold text-gray-800">Costo por Minuto:</span>
                                            <span class="text-xl font-bold text-red-600">${{ number_format($costo_por_minuto, 4) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 text-right">Esto te cuesta existir cada minuto.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-200">
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded shadow transition-colors">
                                <i class="fas fa-save mr-2"></i> Guardar Configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/gastos-operativos.js') }}"></script>
</div>
