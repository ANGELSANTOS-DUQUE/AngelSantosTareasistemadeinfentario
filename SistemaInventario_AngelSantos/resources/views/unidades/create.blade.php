<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <span>➕</span> Nueva Unidad de Medida
            </h2>
            <a href="{{ route('unidades.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur">
            <form action="{{ route('unidades.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Nombre -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Nombre Completo *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Kilogramo, Paquete, Docena" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <!-- Abreviatura -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Abreviatura / Símbolo *</label>
                    <input type="text" name="abreviatura" value="{{ old('abreviatura') }}" required maxlength="10" placeholder="Ej. KG, PAQ, DOC, L" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono uppercase">
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('unidades.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-900/30 transition">
                        Guardar Unidad
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
