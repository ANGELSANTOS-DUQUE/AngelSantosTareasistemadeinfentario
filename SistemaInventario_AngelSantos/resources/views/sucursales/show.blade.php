<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>🏬</span> Sucursal: {{ $sucursal->nombre }}
                </h2>
                <p class="text-sm text-slate-400">Perteneciente a: <strong class="text-emerald-400">{{ $sucursal->empresa->nombre }}</strong></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('sucursales.edit', $sucursal) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition">
                    Editar Sucursal
                </a>
                <a href="{{ route('sucursales.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                    ← Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Detalle de Sucursal -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Empresa</span>
                    <p class="text-sm font-semibold text-white mt-0.5">{{ $sucursal->empresa->nombre }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Teléfono</span>
                    <p class="text-sm text-slate-200 mt-0.5">{{ $sucursal->telefono ?? 'Sin teléfono' }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</span>
                    <p class="mt-0.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $sucursal->estado ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                            {{ $sucursal->estado ? 'Activa' : 'Inactiva' }}
                        </span>
                    </p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Áreas Registradas</span>
                    <p class="text-sm font-bold text-emerald-400 mt-0.5">{{ $sucursal->areas->count() }} áreas</p>
                </div>
                <div class="md:col-span-4">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dirección</span>
                    <p class="text-sm text-slate-300 mt-0.5">{{ $sucursal->direccion ?? 'Sin dirección especificada' }}</p>
                </div>
            </div>
        </div>

        <!-- Áreas de la Sucursal -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>📍</span> Áreas de Trabajo / Almacenes ({{ $sucursal->areas->count() }})
                </h3>
                <a href="{{ route('areas.create', ['sucursal_id' => $sucursal->id]) }}" class="px-3 py-1.5 bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600/30 border border-emerald-500/30 rounded-xl text-xs font-semibold transition">
                    + Nueva Área
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @forelse ($sucursal->areas as $area)
                    <div class="p-4 bg-slate-800/60 border border-slate-700/60 rounded-xl">
                        <div class="flex items-start justify-between">
                            <h4 class="font-bold text-white text-base">{{ $area->nombre }}</h4>
                            <span class="text-xs {{ $area->estado ? 'text-emerald-400' : 'text-rose-400' }}">●</span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">{{ $area->descripcion ?? 'Sin descripción' }}</p>
                        
                        <div class="mt-3 pt-3 border-t border-slate-700/60 text-xs flex justify-between items-center">
                            <span class="text-slate-400">Encargado:</span>
                            <span class="font-medium text-emerald-300">{{ $area->encargado->name ?? 'No asignado' }}</span>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 py-6 text-center text-slate-500">
                        No hay áreas registradas en esta sucursal.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
