<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>🏢</span> {{ $empresa->nombre }}
                </h2>
                <p class="text-sm text-slate-400">Detalle general de la empresa, sucursales y estructura.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('empresas.edit', $empresa) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition">
                    Editar Empresa
                </a>
                <a href="{{ route('empresas.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                    ← Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Ficha de la Empresa -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="flex flex-col md:flex-row gap-6 items-start">
                @if($empresa->logo)
                    <img src="{{ asset('storage/' . $empresa->logo) }}" alt="{{ $empresa->nombre }}" class="w-24 h-24 rounded-2xl object-cover border border-slate-700 shadow-md">
                @else
                    <div class="w-24 h-24 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-3xl text-emerald-400 shadow-md">
                        {{ strtoupper(substr($empresa->nombre, 0, 2)) }}
                    </div>
                @endif
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Identificación Fiscal / RTN</span>
                        <p class="text-sm font-mono text-slate-200 mt-0.5">{{ $empresa->identificacion_fiscal }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Teléfono</span>
                        <p class="text-sm text-slate-200 mt-0.5">{{ $empresa->telefono ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Correo Electrónico</span>
                        <p class="text-sm text-slate-200 mt-0.5">{{ $empresa->correo ?? 'No registrado' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</span>
                        <p class="mt-0.5">
                            @if($empresa->estado)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Activa</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">Inactiva</span>
                            @endif
                        </p>
                    </div>
                    <div class="sm:col-span-2 md:col-span-4">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Dirección</span>
                        <p class="text-sm text-slate-300 mt-0.5">{{ $empresa->direccion ?? 'Sin dirección registrada' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sucursales y Áreas de la Empresa -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>🏬</span> Sucursales Asociadas ({{ $empresa->sucursales->count() }})
                </h3>
                <a href="{{ route('sucursales.create', ['empresa_id' => $empresa->id]) }}" class="px-3 py-1.5 bg-emerald-600/20 text-emerald-400 hover:bg-emerald-600/30 border border-emerald-500/30 rounded-xl text-xs font-semibold transition">
                    + Agregar Sucursal
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($empresa->sucursales as $sucursal)
                    <div class="p-4 bg-slate-800/60 border border-slate-700/60 rounded-xl hover:border-slate-600 transition">
                        <div class="flex items-start justify-between">
                            <div>
                                <h4 class="font-bold text-white text-base">{{ $sucursal->nombre }}</h4>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $sucursal->direccion ?? 'Sin dirección' }}</p>
                                <p class="text-xs text-slate-500">{{ $sucursal->telefono ?? '' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $sucursal->estado ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                {{ $sucursal->estado ? 'Activa' : 'Inactiva' }}
                            </span>
                        </div>

                        <!-- Áreas de la Sucursal -->
                        <div class="mt-4 pt-3 border-t border-slate-700/60">
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-2">Áreas ({{ $sucursal->areas->count() }}):</span>
                            @if($sucursal->areas->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($sucursal->areas as $area)
                                        <div class="px-2.5 py-1 bg-slate-900 border border-slate-700 rounded-lg text-xs text-slate-300 flex items-center space-x-1.5">
                                            <span>📍 {{ $area->nombre }}</span>
                                            @if($area->encargado)
                                                <span class="text-slate-500 font-mono">({{ $area->encargado->name }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-xs text-slate-500 italic">No hay áreas configuradas en esta sucursal.</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 py-8 text-center text-slate-500">
                        No hay sucursales registradas para esta empresa.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
