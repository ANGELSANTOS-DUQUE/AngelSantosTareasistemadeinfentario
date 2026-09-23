<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <span>✏️</span> Editar Proveedor: {{ $proveedor->nombre }}
            </h2>
            <a href="{{ route('proveedores.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur">
            <form action="{{ route('proveedores.update', $proveedor) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Empresa -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Empresa *</label>
                    <select name="empresa_id" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        @foreach ($empresas as $empresa)
                            <option value="{{ $empresa->id }}" {{ old('empresa_id', $proveedor->empresa_id) == $empresa->id ? 'selected' : '' }}>
                                {{ $empresa->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nombre Proveedor -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Nombre o Razón Social del Proveedor *</label>
                    <input type="text" name="nombre" value="{{ old('nombre', $proveedor->nombre) }}" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <!-- Contacto -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Nombre del Contacto / Vendedor</label>
                    <input type="text" name="contacto" value="{{ old('contacto', $proveedor->contacto) }}" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Teléfono</label>
                        <input type="text" name="telefono" value="{{ old('telefono', $proveedor->telefono) }}" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <!-- Correo -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Correo Electrónico</label>
                        <input type="email" name="correo" value="{{ old('correo', $proveedor->correo) }}" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('proveedores.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-900/30 transition">
                        Actualizar Proveedor
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
