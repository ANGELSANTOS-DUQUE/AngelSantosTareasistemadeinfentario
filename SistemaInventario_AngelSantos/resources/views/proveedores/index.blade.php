<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>🚚</span> Proveedores y Suplidores
                </h2>
                <p class="text-sm text-slate-400">Directorio de contactos comerciales y proveedores de mercancía.</p>
            </div>
            <a href="{{ route('proveedores.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Proveedor
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtros y Búsqueda -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('proveedores.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Buscar Proveedor</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, contacto, correo, teléfono..." class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
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

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium rounded-xl text-sm transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'empresa_id']))
                        <a href="{{ route('proveedores.index') }}" class="px-3 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/30 rounded-xl text-sm transition">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de Proveedores -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Proveedor</th>
                            <th class="px-6 py-4">Empresa</th>
                            <th class="px-6 py-4">Contacto Directo</th>
                            <th class="px-6 py-4">Teléfono / Correo</th>
                            <th class="px-6 py-4 text-center">Ítems Suministrados</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($proveedores as $prov)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    {{ $prov->nombre }}
                                </td>
                                <td class="px-6 py-4 text-xs font-medium text-slate-300">
                                    {{ $prov->empresa->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-300">
                                    {{ $prov->contacto ?? 'Sin contacto' }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="text-white">{{ $prov->telefono ?? '—' }}</div>
                                    <div class="text-slate-500">{{ $prov->correo ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-emerald-400">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        {{ $prov->items_count }} ítems
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('proveedores.edit', $prov) }}" class="p-1.5 inline-block text-amber-400 hover:text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('proveedores.destroy', $prov) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Deseas eliminar este proveedor?');">
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
                                    No hay proveedores registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($proveedores->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                    {{ $proveedores->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
