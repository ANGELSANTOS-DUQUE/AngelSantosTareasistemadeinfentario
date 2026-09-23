<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <span>➕</span> Registrar Nueva Empresa
            </h2>
            <a href="{{ route('empresas.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                ← Volver al Listado
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur">
            <form action="{{ route('empresas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Nombre de la Empresa *</label>
                        <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Distribuidora Central S. de R.L." class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <!-- RTN / Identificación Fiscal -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">RTN / Identificación Fiscal *</label>
                        <input type="text" name="identificacion_fiscal" value="{{ old('identificacion_fiscal') }}" required placeholder="Ej. 08011990123456" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono">
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Teléfono de Contacto</label>
                        <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Ej. +504 2234-5678" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <!-- Correo -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Correo Electrónico</label>
                        <input type="email" name="correo" value="{{ old('correo') }}" placeholder="contacto@empresa.com" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                    </div>

                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Logo de la Empresa (Opcional)</label>
                        <input type="file" name="logo" accept="image/*" class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-500/10 file:text-emerald-400 hover:file:bg-emerald-500/20 cursor-pointer">
                    </div>

                    <!-- Dirección -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Dirección Física</label>
                        <textarea name="direccion" rows="3" placeholder="Calle, Barrio, Ciudad..." class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">{{ old('direccion') }}</textarea>
                    </div>

                    <!-- Estado -->
                    <div class="md:col-span-2 flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="estado" value="1" class="sr-only peer" {{ old('estado', '1') ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                            <span class="ml-3 text-sm font-medium text-slate-300">Empresa Activa</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('empresas.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-900/30 transition">
                        Guardar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
