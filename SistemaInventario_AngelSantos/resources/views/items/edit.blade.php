<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <span>✏️</span> Editar Ítem: {{ $item->nombre }}
            </h2>
            <a href="{{ route('items.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur">
            <form action="{{ route('items.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <input type="hidden" name="empresa_id" value="{{ $item->empresa_id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre del Ítem -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Nombre del Ítem / Producto *</label>
                        <input type="text" name="nombre" value="{{ old('nombre', $item->nombre) }}" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <!-- Código SKU -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Código / SKU *</label>
                        <input type="text" name="sku" value="{{ old('sku', $item->sku) }}" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono font-bold">
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Categoría *</label>
                        <select name="categoria_id" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id', $item->categoria_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Unidad de Medida -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Unidad de Medida *</label>
                        <select name="unidad_medida_id" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            @foreach ($unidades as $u)
                                <option value="{{ $u->id }}" {{ old('unidad_medida_id', $item->unidad_medida_id) == $u->id ? 'selected' : '' }}>
                                    {{ $u->nombre }} ({{ $u->abreviatura }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Proveedor Principal</label>
                        <select name="proveedor_id" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Ninguno / Varios --</option>
                            @foreach ($proveedores as $prov)
                                <option value="{{ $prov->id }}" {{ old('proveedor_id', $item->proveedor_id) == $prov->id ? 'selected' : '' }}>
                                    {{ $prov->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Costo Unitario -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Costo Unitario (L.)</label>
                        <input type="number" step="0.01" name="costo_unitario" value="{{ old('costo_unitario', $item->costo_unitario) }}" min="0" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <!-- Stock Mínimo -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Stock Mínimo (Alerta de reposición)</label>
                        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', $item->stock_minimo) }}" min="0" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <!-- Imagen -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Imagen del Producto</label>
                        @if($item->imagen)
                            <div class="flex items-center space-x-3 mb-2">
                                <img src="{{ asset('storage/' . $item->imagen) }}" alt="Imagen actual" class="w-12 h-12 rounded-lg object-cover border border-slate-700">
                                <span class="text-xs text-slate-400">Imagen actual</span>
                            </div>
                        @endif
                        <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 cursor-pointer">
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Descripción Detallada</label>
                        <textarea name="descripcion" rows="3" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">{{ old('descripcion', $item->descripcion) }}</textarea>
                    </div>

                    <!-- Estado -->
                    <div class="md:col-span-2 flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="estado" value="1" class="sr-only peer" {{ old('estado', $item->estado) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-300">Ítem Activo</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('items.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-900/30 transition">
                        Actualizar Ítem
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
