<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <span>➕</span> Registrar Ítem en Catálogo
            </h2>
            <a href="{{ route('items.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur">
            <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Empresa -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Empresa *</label>
                        <select name="empresa_id" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            @foreach ($empresas as $empresa)
                                <option value="{{ $empresa->id }}" {{ old('empresa_id') == $empresa->id ? 'selected' : '' }}>
                                    {{ $empresa->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Nombre del Ítem -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Nombre del Ítem / Producto *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Leche Entera 1L, Martillo de Uña 16oz" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <!-- Código SKU -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Código / SKU (Dejar en blanco para autogenerar)</label>
                        <input type="text" name="sku" value="{{ old('sku') }}" placeholder="Ej. PROD-00123 (opcional)" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <!-- Categoría -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Categoría *</label>
                        <select name="categoria_id" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Seleccionar Categoría --</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
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
                                <option value="{{ $u->id }}" {{ old('unidad_medida_id') == $u->id ? 'selected' : '' }}>
                                    {{ $u->nombre }} ({{ $u->abreviatura }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Proveedor -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Proveedor Principal (Opcional)</label>
                        <select name="proveedor_id" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Ninguno / Varios --</option>
                            @foreach ($proveedores as $prov)
                                <option value="{{ $prov->id }}" {{ old('proveedor_id') == $prov->id ? 'selected' : '' }}>
                                    {{ $prov->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Costo Unitario -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Costo Unitario (L.)</label>
                        <input type="number" step="0.01" name="costo_unitario" value="{{ old('costo_unitario', '0.00') }}" min="0" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <!-- Stock Mínimo -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Stock Mínimo (Alerta de reposición)</label>
                        <input type="number" name="stock_minimo" value="{{ old('stock_minimo', '5') }}" min="0" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <!-- Imagen -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Fotografía o Imagen del Ítem (Opcional)</label>
                        <input type="file" name="imagen" accept="image/*" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 cursor-pointer">
                    </div>

                    <!-- Descripción -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Descripción Detallada</label>
                        <textarea name="descripcion" rows="3" placeholder="Características técnicas, especificaciones..." class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">{{ old('descripcion') }}</textarea>
                    </div>

                    <!-- Estado -->
                    <div class="md:col-span-2 flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="estado" value="1" class="sr-only peer" {{ old('estado', '1') ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-300">Ítem Activo en Catálogo</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('items.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-900/30 transition">
                        Guardar Ítem
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
