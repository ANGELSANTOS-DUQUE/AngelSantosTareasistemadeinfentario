<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>🏢</span> Inventario Consolidado por Área
                </h2>
                <p class="text-sm text-slate-400">Existencias físicas, responsables asignados y ubicación exacta de inventario.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-medium rounded-xl text-xs transition">
                    + Entrada
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="px-3 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-medium rounded-xl text-xs transition">
                    ⇄ Trasladar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtros y Búsqueda -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('inventario.stock') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Buscar Ítem o SKU</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nombre del ítem, SKU..." class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Sucursal</label>
                    <select name="sucursal_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todas las Sucursales</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->id }}" {{ request('sucursal_id') == $suc->id ? 'selected' : '' }}>{{ $suc->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Área / Almacén</label>
                    <select name="area_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todas las Áreas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->sucursal->nombre }} → {{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium rounded-xl text-sm transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['search', 'sucursal_id', 'area_id', 'categoria_id']))
                        <a href="{{ route('inventario.stock') }}" class="px-3 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/30 rounded-xl text-sm transition">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla de Stock por Área -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Ítem / SKU</th>
                            <th class="px-6 py-4">Sucursal</th>
                            <th class="px-6 py-4">Área / Almacén</th>
                            <th class="px-6 py-4">Encargado Responsable</th>
                            <th class="px-6 py-4 text-center">Stock en Área</th>
                            <th class="px-6 py-4 text-center">Estado Alerta</th>
                            <th class="px-6 py-4 text-right">Acción Rápida</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($inventarios as $inv)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-medium text-white">
                                    <div class="font-semibold">{{ $inv->item->nombre }}</div>
                                    <div class="text-xs font-mono text-emerald-400">{{ $inv->item->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs font-semibold text-slate-200">
                                    {{ $inv->area->sucursal->nombre }}
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-300">
                                    📍 {{ $inv->area->nombre }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="text-white">{{ $inv->area->encargado->name ?? 'Sin encargado' }}</div>
                                    <div class="text-slate-500">{{ $inv->area->encargado->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-lg text-white">
                                    {{ $inv->cantidad }} <span class="text-xs font-normal text-slate-500">{{ $inv->item->unidadMedida->abreviatura ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($inv->cantidad <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            Agotado
                                        </span>
                                    @elseif ($inv->cantidad <= $inv->item->stock_minimo)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Bajo Mínimo (Mín: {{ $inv->item->stock_minimo }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Óptimo
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-1 whitespace-nowrap">
                                    <a href="{{ route('movimientos.create', ['tipo' => 'traslado', 'item_id' => $inv->item_id, 'area_origen_id' => $inv->area_id]) }}" class="px-2.5 py-1 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 border border-indigo-500/20 rounded-lg text-xs font-semibold transition" title="Trasladar desde aquí">
                                        ⇄ Trasladar
                                    </a>
                                    <a href="{{ route('movimientos.create', ['tipo' => 'ajuste', 'item_id' => $inv->item_id, 'area_id' => $inv->area_id]) }}" class="px-2.5 py-1 bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/20 rounded-lg text-xs font-semibold transition" title="Ajustar stock">
                                        ⚙️ Ajustar
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    No hay registros de inventario que coincidan con los filtros.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($inventarios->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                    {{ $inventarios->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
