<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📦</span> Catálogo de Ítems (Productos)
                </h2>
                <p class="text-sm text-slate-400">Catálogo maestro de referencias, SKUs, categorías y stock consolidado.</p>
            </div>
            <a href="{{ route('items.create') }}" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl shadow-lg shadow-emerald-900/30 transition transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo Ítem
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtros y Búsqueda -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Buscar por Nombre o SKU</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre, código SKU o descripción..." class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Categoría</label>
                    <select name="categoria_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todas las Categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Proveedor</label>
                    <select name="proveedor_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todos los Proveedores</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ request('proveedor_id') == $prov->id ? 'selected' : '' }}>{{ $prov->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium rounded-xl text-sm transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'categoria_id', 'proveedor_id', 'estado']))
                        <a href="{{ route('items.index') }}" class="px-3 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/30 rounded-xl text-sm transition">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de Ítems -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Ítem / Producto</th>
                            <th class="px-6 py-4">SKU</th>
                            <th class="px-6 py-4">Categoría</th>
                            <th class="px-6 py-4 text-center">Costo Unit.</th>
                            <th class="px-6 py-4 text-center">Stock Total</th>
                            <th class="px-6 py-4 text-center">Mínimo</th>
                            <th class="px-6 py-4 text-center">Alerta de Stock</th>
                            <th class="px-6 py-4 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($items as $item)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-medium text-white">
                                    <div class="flex items-center space-x-3">
                                        @if($item->imagen)
                                            <img src="{{ asset('storage/' . $item->imagen) }}" alt="{{ $item->nombre }}" class="w-10 h-10 rounded-lg object-cover border border-slate-700">
                                        @else
                                            <div class="w-10 h-10 rounded-lg bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-emerald-400">
                                                📦
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('items.show', $item) }}" class="text-white hover:text-emerald-400 font-semibold transition">
                                                {{ $item->nombre }}
                                            </a>
                                            <p class="text-xs text-slate-500">{{ $item->proveedor->nombre ?? 'Sin proveedor' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-emerald-400 font-bold">
                                    {{ $item->sku }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-300">
                                    {{ $item->categoria->nombre ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-center font-mono text-xs text-slate-300">
                                    L. {{ number_format($item->costo_unitario, 2) }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-base text-white">
                                    {{ $item->stock_total }} <span class="text-xs font-normal text-slate-500">{{ $item->unidadMedida->abreviatura ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-slate-400">
                                    {{ $item->stock_minimo }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $status = $item->stock_status; @endphp
                                    @if ($status === 'danger')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30 animate-pulse">
                                            🔴 Agotado (0)
                                        </span>
                                    @elseif ($status === 'warning')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            🟡 Bajo Mínimo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                            🟢 Óptimo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1.5 whitespace-nowrap">
                                    <!-- Ver stock por área -->
                                    <a href="{{ route('items.inventario', $item) }}" class="p-1.5 inline-block text-cyan-400 hover:text-cyan-300 bg-cyan-500/10 hover:bg-cyan-500/20 border border-cyan-500/20 rounded-lg transition" title="Ver stock en cada área">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                                    </a>
                                    <a href="{{ route('items.show', $item) }}" class="p-1.5 inline-block text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 rounded-lg transition" title="Detalle">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('items.edit', $item) }}" class="p-1.5 inline-block text-amber-400 hover:text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/20 rounded-lg transition" title="Editar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('items.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('¿Deseas eliminar este ítem? Solo se podrá si no tiene stock.');">
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
                                <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                    No se encontraron ítems en el catálogo maestro.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($items->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
