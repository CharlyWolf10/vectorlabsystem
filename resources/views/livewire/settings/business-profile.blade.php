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
                            <input id="nombre" type="text" style="text-transform: uppercase;" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model.blur="nombre" />
                            @error('nombre') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- RFC -->
                        <div>
                            <label for="rfc" class="block font-medium text-sm text-gray-700">RFC</label>
                            <input id="rfc" type="text" maxlength="13" style="text-transform: uppercase;" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model.blur="rfc" />
                            @error('rfc') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Dirección -->
                        <div class="md:col-span-2">
                            <label for="direccion" class="block font-medium text-sm text-gray-700">Dirección Completa</label>
                            <input id="direccion" type="text" style="text-transform: uppercase;" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model.blur="direccion" />
                            @error('direccion') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Teléfono -->
                        <div>
                            <label for="telefono" class="block font-medium text-sm text-gray-700">Teléfono</label>
                            <input id="telefono" type="text" maxlength="10" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model.blur="telefono" />
                            @error('telefono') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Correo Electrónico -->
                        <div>
                            <label for="correo" class="block font-medium text-sm text-gray-700">Correo Electrónico</label>
                            <input id="correo" type="email" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model.blur="correo" />
                            @error('correo') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <!-- Sitio Web -->
                        <div class="md:col-span-2">
                            <label for="sitio_web" class="block font-medium text-sm text-gray-700">Sitio Web</label>
                            <input id="sitio_web" type="text" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" wire:model.blur="sitio_web" placeholder="ej. https://www.tupagina.com" />
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

                        <!-- Imágenes Adicionales / Referencias -->
                        <div class="md:col-span-2 mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900 mb-2"><i class="fas fa-images text-indigo-500 mr-2"></i> Imágenes y Recursos Adicionales</h3>
                            <p class="text-sm text-gray-500 mb-4">Sube imágenes, vectores o gráficos adicionales que quieras guardar en el sistema para usarlos como referencias, fondos o iconos en tus reportes.</p>
                            
                            <input id="newImages" type="file" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" wire:model="newImages" accept="image/*" />
                            @error('newImages.*') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror

                            <!-- Previsualización de imágenes nuevas -->
                            @if ($newImages)
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 font-medium mb-2">Imágenes listas para subir:</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @foreach ($newImages as $img)
                                            <div class="relative border border-dashed border-gray-300 rounded p-1 bg-gray-50">
                                                <img src="{{ $img->temporaryUrl() }}" class="h-24 w-full object-contain">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Imágenes guardadas -->
                            @if (count($existingImages) > 0)
                                <div class="mt-6">
                                    <p class="text-sm text-gray-500 font-medium mb-2">Imágenes alojadas en tu sistema:</p>
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                        @foreach ($existingImages as $existingImg)
                                            <div class="relative border border-gray-200 shadow-sm rounded p-1 bg-white flex items-center justify-center group">
                                                <img src="{{ asset($existingImg->path) }}" class="h-24 w-full object-contain">
                                                <button type="button" wire:click.prevent="eliminarImagen({{ $existingImg->id }})" wire:confirm="¿Estás seguro de que deseas eliminar esta imagen del sistema?" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow" title="Eliminar imagen">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
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
