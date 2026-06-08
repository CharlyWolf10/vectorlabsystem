<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Datos del Negocio') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                @if (session()->has('message'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                <form wire:submit.prevent="guardar" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Nombre del Negocio -->
                        <div>
                            <label for="nombre" class="block font-medium text-sm text-gray-700">Nombre del Negocio</label>
                            <input id="nombre" type="text" style="text-transform: uppercase;" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model="nombre" />
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- RFC -->
                        <div>
                            <label for="rfc" class="block font-medium text-sm text-gray-700">RFC</label>
                            <input id="rfc" type="text" maxlength="13" style="text-transform: uppercase;" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model="rfc" />
                            @error('rfc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label for="direccion" class="block font-medium text-sm text-gray-700">Dirección Completa</label>
                            <input id="direccion" type="text" style="text-transform: uppercase;" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model="direccion" />
                            @error('direccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block font-medium text-sm text-gray-700">Teléfono</label>
                            <input id="telefono" type="text" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model="telefono" />
                            @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Correo Electrónico -->
                        <div>
                            <label for="correo" class="block font-medium text-sm text-gray-700">Correo Electrónico</label>
                            <input id="correo" type="email" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model="correo" />
                            @error('correo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Sitio Web -->
                        <div class="md:col-span-2">
                            <label for="sitio_web" class="block font-medium text-sm text-gray-700">Sitio Web</label>
                            <input id="sitio_web" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model="sitio_web" placeholder="ej. https://www.tupagina.com" />
                            @error('sitio_web') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Logo -->
                        <div class="md:col-span-2">
                            <label for="logo" class="block font-medium text-sm text-gray-700">Logo del Negocio</label>
                            <input id="logo" type="file" class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" wire:model="logo" accept="image/*" />
                            @error('logo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror

                            <div class="mt-4">
                                @if ($logo)
                                    <p class="text-sm text-gray-500">Previsualización del nuevo logo:</p>
                                    <img src="{{ $logo->temporaryUrl() }}" class="mt-2 h-20 object-contain">
                                @elseif ($current_logo_path)
                                    <p class="text-sm text-gray-500">Logo actual:</p>
                                    <img src="{{ asset($current_logo_path) }}" class="mt-2 h-20 object-contain">
                                @else
                                    <p class="text-sm text-gray-500">Usando logo predeterminado del sistema.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 border-t pt-5">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('swal:success', (event) => {
            let data = event[0] || event;
            Swal.fire({
                icon: 'success',
                title: data.title,
                text: data.text,
                timer: 3000,
                showConfirmButton: false
            });
        });
    });
</script>
