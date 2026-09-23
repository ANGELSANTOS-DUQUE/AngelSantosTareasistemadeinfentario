<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📋</span> Reporte de Inventario Actual
                </h2>
                <p class="text-sm text-slate-400">Distribución de stock por área, ítem y categoría.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reportes.inventario.excel', request()->all()) }}"
                   class="px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold shadow transition flex items-center gap-1">
                    📥 Exportar Excel
                </a>
                <a href="{{ route('reportes.inventario.pdf', request()->all()) }}"
                   class="px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-xl text-xs font-semibold shadow transition flex items-center gap-1">
                    📄 Exportar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Filtros --}}
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <form method="GET" action="{{ route('reportes.inventario') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @if(Auth::user()->isSuperAdmin())
                    <div>
                        <label class="block text-xs text-slate-400 mb-1">Empresa</label>
                        <select name="empresa_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                            <option value="">Todas</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}" {{ request('empresa_id') == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Sucursal</label>
                    <select name="sucursal_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">Todas</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->id }}" {{ request('sucursal_id') == $suc->id ? 'selected' : '' }}>{{ $suc->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Área</label>
                    <select name="area_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">Todas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>{{ $area->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Categoría</label>
                    <select name="categoria_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">Todas</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-2 lg:col-span-4 flex gap-2 justify-end">
                    <a href="{{ route('reportes.inventario') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-sm transition">Limpiar</a>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold transition">Filtrar</button>
                </div>
            </form>
        </div>

        {{-- Tabla de Inventario --}}
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-white font-semibold">Existencias por Área</h3>
                <span class="text-xs text-slate-400">{{ $inventarios->total() }} registros encontrados</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-800/60 text-slate-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-3">SKU</th>
                            <th class="px-6 py-3">Ítem</th>
                            <th class="px-6 py-3">Categoría</th>
                            <th class="px-6 py-3">Área</th>
                            <th class="px-6 py-3">Sucursal</th>
                            <th class="px-6 py-3">Unidad</th>
                            <th class="px-6 py-3 text-right">Cantidad</th>
                            <th class="px-6 py-3 text-right">Stock Mín.</th>
                            <th class="px-6 py-3 text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($inventarios as $inv)
                            @php
                                $stockTotal = $inv->item->inventariosArea->sum('cantidad');
                                $alerta = $stockTotal <= 0
                                    ? 'agotado'
                                    : ($stockTotal <= $inv->item->stock_minimo ? 'bajo' : 'ok');
                            @endphp
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 font-mono text-xs text-emerald-400">{{ $inv->item->sku }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-white">{{ $inv->item->nombre }}</div>
                                </td>
                                <td class="px-6 py-4 text-slate-400">{{ $inv->item->categoria->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-300">{{ $inv->area->nombre }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ $inv->area->sucursal->nombre ?? '—' }}</td>
                                <td class="px-6 py-4 text-slate-400">{{ $inv->item->unidadMedida->abreviatura ?? '—' }}</td>
                                <td class="px-6 py-4 text-right font-bold text-white">{{ number_format($inv->cantidad) }}</td>
                                <td class="px-6 py-4 text-right text-slate-400">{{ number_format($inv->item->stock_minimo) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($alerta === 'agotado')
                                        <span class="px-2 py-1 bg-red-900/40 text-red-400 border border-red-800 rounded-full text-xs font-semibold">🔴 Agotado</span>
                                    @elseif($alerta === 'bajo')
                                        <span class="px-2 py-1 bg-amber-900/40 text-amber-400 border border-amber-800 rounded-full text-xs font-semibold">🟡 Bajo</span>
                                    @else
                                        <span class="px-2 py-1 bg-emerald-900/40 text-emerald-400 border border-emerald-800 rounded-full text-xs font-semibold">🟢 OK</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-16 text-center text-slate-500">
                                    <div class="text-4xl mb-2">📭</div>
                                    <p>No hay registros de inventario con los filtros aplicados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($inventarios->hasPages())
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $inventarios->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
