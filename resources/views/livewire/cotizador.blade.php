<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cotizador y Fórmulas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Diseñador de Fórmulas / Recetas</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Lado izquierdo: Formularios para agregar ingredientes -->
                        <div class="md:col-span-2 space-y-6">
                            
                            <!-- Información básica de la Fórmula -->
                            <div class="p-4 bg-gray-50 border rounded-lg">
                                <h4 class="font-bold text-gray-700 mb-3">1. Información de la Fórmula</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nombre del Producto / Receta</label>
                                        <input type="text" wire:model="nombre" placeholder="Ej. Impresión Tabloide Couché" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tiempo de Producción (Minutos)</label>
                                        <input type="number" wire:model.live="minutos_produccion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Ej. 2">
                                        <p class="text-xs text-gray-500 mt-1">Costo operativo actual: ${{ number_format($costo_por_minuto, 4) }}/min</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Agregar Materiales -->
                            <div class="p-4 bg-gray-50 border rounded-lg">
                                <h4 class="font-bold text-gray-700 mb-3">2. Papel y Materiales Base</h4>
                                <div class="relative">
                                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar material en inventario..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    
                                    @if(count($resultados) > 0)
                                        <div class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-md shadow-lg">
                                            <ul>
                                                @foreach($resultados as $producto)
                                                    <li wire:click="agregarIngrediente({{ $producto->id }})" class="px-4 py-2 hover:bg-blue-50 cursor-pointer text-sm">
                                                        <span class="font-bold">{{ $producto->nombre }}</span> - 
                                                        Costo Unitario: ${{ number_format($producto->precio_pieza > 0 ? $producto->precio_pieza : $producto->precio_venta, 2) }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </div>

                                @if(count($ingredientes) > 0)
                                    <div class="mt-4">
                                        <table class="w-full text-sm text-left text-gray-500">
                                            <thead class="text-xs text-gray-700 uppercase bg-gray-200">
                                                <tr>
                                                    <th class="px-3 py-2">Material</th>
                                                    <th class="px-3 py-2 w-24">Cantidad</th>
                                                    <th class="px-3 py-2 w-24">Subtotal</th>
                                                    <th class="px-3 py-2 w-10"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($ingredientes as $index => $ing)
                                                    <tr class="bg-white border-b">
                                                        <td class="px-3 py-2 font-medium text-gray-900">{{ $ing['nombre'] }}</td>
                                                        <td class="px-3 py-2">
                                                            <input type="number" step="0.01" wire:model.live="ingredientes.{{ $index }}.cantidad" class="w-20 p-1 border border-gray-300 rounded text-sm">
                                                        </td>
                                                        <td class="px-3 py-2">
                                                            ${{ number_format($ing['cantidad'] * $ing['precio_unitario'], 2) }}
                                                        </td>
                                                        <td class="px-3 py-2 text-right">
                                                            <button wire:click="removerIngrediente({{ $index }})" class="text-red-500 hover:text-red-700">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>

                            <!-- Tintas CMYK -->
                            <div class="p-4 bg-gray-50 border rounded-lg">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-bold text-gray-700">3. Impresión (Tintas CMYK)</h4>
                                    <label class="inline-flex items-center cursor-pointer">
                                        <input type="checkbox" wire:model.live="lleva_impresion" class="sr-only peer">
                                        <div class="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                        <span class="ms-3 text-sm font-medium text-gray-900">¿Lleva Impresión?</span>
                                    </label>
                                </div>

                                @if($lleva_impresion)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                        @foreach(['C' => 'Cyan', 'M' => 'Magenta', 'Y' => 'Yellow', 'K' => 'Black'] as $key => $name)
                                            <div class="border p-3 rounded bg-white shadow-sm">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="font-bold text-sm">{{ $name }}</span>
                                                    @if($cmyk[$key]['producto_id'])
                                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Vinculado: {{ $cmyk[$key]['nombre'] }}</span>
                                                    @else
                                                        <div class="relative w-40">
                                                            <input type="text" wire:model.live.debounce.300ms="search_{{ strtolower($key) }}" placeholder="Buscar tinta..." class="w-full text-xs p-1 border-gray-300 rounded">
                                                            @if(count(${'res_'.strtolower($key)}) > 0)
                                                                <ul class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded shadow text-xs">
                                                                    @foreach(${'res_'.strtolower($key)} as $tinta)
                                                                        <li wire:click="seleccionarTinta('{{ $key }}', {{ $tinta->id }})" class="p-1 hover:bg-gray-100 cursor-pointer">
                                                                            {{ $tinta->nombre }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <input type="number" step="0.001" wire:model.live="cmyk.{{ $key }}.cantidad" class="w-24 p-1 border-gray-300 rounded text-sm" placeholder="ml">
                                                        <span class="text-xs text-gray-500">ml</span>
                                                    </div>
                                                    <div class="text-right text-sm">
                                                        <span class="text-gray-500 text-xs">Costo:</span> 
                                                        <span class="font-bold">${{ number_format($cmyk[$key]['cantidad'] * $cmyk[$key]['precio_ml'], 2) }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <p class="text-xs text-blue-600 mt-2"><i class="fas fa-info-circle"></i> Al vender esta fórmula, se descontarán los mililitros exactos de tu inventario.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Lado derecho: Resumen Financiero -->
                        <div class="space-y-4">
                            <div class="bg-gray-800 text-white p-5 rounded-lg shadow-lg sticky top-6">
                                <h3 class="text-lg font-bold border-b border-gray-600 pb-2 mb-4">Resumen de Costos</h3>
                                
                                <div class="flex justify-between mb-2">
                                    <span class="text-gray-300">Materiales & Tintas:</span>
                                    <span class="font-medium">${{ number_format($this->costo_materiales, 2) }}</span>
                                </div>
                                <div class="flex justify-between mb-4 pb-4 border-b border-gray-600">
                                    <span class="text-gray-300">Costo Operativo (Tiempos):</span>
                                    <span class="font-medium">${{ number_format($this->costo_operativo, 2) }}</span>
                                </div>
                                
                                <div class="flex justify-between mb-4 text-xl text-yellow-400">
                                    <span class="font-bold">Costo Base:</span>
                                    <span class="font-bold">${{ number_format($this->costo_total, 2) }}</span>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm text-gray-300 mb-1">Margen de Ganancia (%)</label>
                                    <input type="number" wire:model.live="margen_ganancia" class="w-full text-black p-2 rounded focus:ring-blue-500" placeholder="Ej. 50">
                                </div>

                                <div class="bg-blue-600 p-4 rounded-lg mt-6">
                                    <div class="text-sm text-blue-100 mb-1">Precio Sugerido al Cliente</div>
                                    <div class="text-3xl font-bold text-white text-center">${{ number_format($this->precio_sugerido, 2) }}</div>
                                </div>

                                <button wire:click="guardar" class="w-full mt-6 bg-green-500 hover:bg-green-600 text-white font-bold py-3 rounded transition-colors shadow">
                                    <i class="fas fa-save mr-2"></i> Guardar Fórmula
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('js/cotizador.js') }}"></script>
</div>
