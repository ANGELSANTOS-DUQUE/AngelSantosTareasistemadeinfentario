<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <span>➕</span> Registrar Nueva Área
            </h2>
            <a href="{{ route('areas.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                ← Volver
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur">
            <form action="{{ route('areas.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Sucursal -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Sucursal a la que pertenece *</label>
                    <select name="sucursal_id" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        @foreach ($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}" {{ old('sucursal_id', request('sucursal_id')) == $sucursal->id ? 'selected' : '' }}>
                                {{ $sucursal->nombre }} (Empresa: {{ $sucursal->empresa->nombre }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nombre del Área -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Nombre del Área / Almacén *</label>
                    <input type="text" name="nombre" value="{{ old('nombre') }}" required placeholder="Ej. Bodega Principal, Cocina, Recepción, Mostrador 1" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <!-- Encargado Responsable -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Encargado Responsable (Usuario del sistema)</label>
                    <select name="encargado_id" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">-- Sin encargado asignado por ahora --</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}" {{ old('encargado_id') == $usuario->id ? 'selected' : '' }}>
                                {{ $usuario->name }} ({{ $usuario->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1">El encargado será responsable automático del inventario que resida o sea trasladado a esta área.</p>
                </div>

                <!-- Descripción -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Descripción o Detalles del Área</label>
                    <textarea name="descripcion" rows="3" placeholder="Ubicación interna, especificaciones del almacén..." class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">{{ old('descripcion') }}</textarea>
                </div>

                <!-- Estado -->
                <div class="flex items-center">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="estado" value="1" class="sr-only peer" {{ old('estado', '1') ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-slate-700 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        <span class="ml-3 text-sm font-medium text-slate-300">Área Activa</span>
                    </label>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('areas.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-900/30 transition">
                        Guardar Área
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
