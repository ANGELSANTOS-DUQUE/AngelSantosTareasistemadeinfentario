<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📏</span> Unidades de Medida
                </h2>
                <p class="text-sm text-slate-400">Catálogo estándar de unidades de conteo y pesaje.</p>
            </div>
            <a href="{{ route('unidades.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nueva Unidad
            </a>
        </div>
    </x-slot>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Búsqueda -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('unidades.index') }}" class="flex gap-4">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nombre o abreviatura..." class="flex-1 px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium rounded-xl text-sm transition">
                    Buscar
                </button>
            </form>
        </div>

        <!-- Tabla de Unidades -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Nombre</th>
                            <th class="px-6 py-4 text-center">Abreviatura</th>
                            <th class="px-6 py-4 text-center">Ítems que la usan</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($unidades as $u)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    {{ $u->nombre }}
                                </td>
                                <td class="px-6 py-4 text-center font-mono font-bold text-emerald-400">
                                    <span class="px-2.5 py-1 bg-emerald-500/10 border border-emerald-500/20 rounded-lg text-xs">
                                        {{ $u->abreviatura }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-semibold text-slate-300">
                                    {{ $u->items_count }} ítems
                                </td>
                                <td class="px-6 py-4 text-right space-x-2">
                                    <a href="{{ route('unidades.edit', $u) }}" class="p-1.5 inline-block text-amber-400 hover:text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('unidades.destroy', $u) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Deseas eliminar esta unidad de medida?');">
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
                                <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                    No hay unidades de medida registradas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($unidades->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                    {{ $unidades->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
