<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>🏬</span> Gestión de Sucursales
                </h2>
                <p class="text-sm text-slate-400">Administra las sedes y locales comerciales de cada empresa.</p>
            </div>
            <a href="{{ route('sucursales.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Sucursal
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtros y Búsqueda -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('sucursales.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Buscar Sucursal</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, dirección, teléfono..." class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                @if(Auth::user()->isSuperAdmin())
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Empresa</label>
                    <select name="empresa_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todas las Empresas</option>
                        @foreach($empresas as $emp)
                            <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Estado</label>
                    <select name="estado" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todos los Estados</option>
                        <option value="1" {{ request('estado') === '1' ? 'selected' : '' }}>Activa</option>
                        <option value="0" {{ request('estado') === '0' ? 'selected' : '' }}>Inactiva</option>
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium rounded-xl text-sm transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'empresa_id', 'estado']))
                        <a href="{{ route('sucursales.index') }}" class="px-3 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/30 rounded-xl text-sm transition">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de Sucursales -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Sucursal</th>
                            <th class="px-6 py-4">Empresa</th>
                            <th class="px-6 py-4">Contacto & Ubicación</th>
                            <th class="px-6 py-4 text-center">Áreas</th>
                            <th class="px-6 py-4 text-center">Estado</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($sucursales as $sucursal)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-medium text-white">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 rounded-lg bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center font-bold text-indigo-400">
                                            🏬
                                        </div>
                                        <div>
                                            <a href="{{ route('sucursales.show', $sucursal) }}" class="text-white hover:text-emerald-400 font-semibold transition">
                                                {{ $sucursal->nombre }}
                                            </a>
                                            <p class="text-xs text-slate-500">ID: #{{ $sucursal->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-300">
                                    {{ $sucursal->empresa->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="text-slate-300">{{ $sucursal->direccion ?? 'Sin dirección' }}</div>
                                    <div class="text-slate-500">{{ $sucursal->telefono ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-white">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $sucursal->areas_count }} áreas
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($sucursal->estado)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Activa
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            Inactiva
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('sucursales.show', $sucursal) }}" class="p-1.5 inline-block text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition" title="Ver detalles">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('sucursales.edit', $sucursal) }}" class="p-1.5 inline-block text-amber-400 hover:text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('sucursales.destroy', $sucursal) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar lógicamente esta sucursal?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-rose-400 hover:text-rose-300 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 rounded-lg transition" title="Eliminar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    <p class="text-base font-medium text-slate-400">No se encontraron sucursales</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($sucursales->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                    {{ $sucursales->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
