<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>🔄</span> Reporte de Movimientos
                </h2>
                <p class="text-sm text-slate-400">Bitácora completa de entradas, salidas, traslados y ajustes.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('reportes.movimientos.excel', request()->all()) }}"
                   class="px-4 py-2 bg-emerald-700 hover:bg-emerald-600 text-white rounded-xl text-xs font-semibold shadow transition flex items-center gap-1">
                    📥 Exportar Excel
                </a>
                <a href="{{ route('reportes.movimientos.pdf', request()->all()) }}"
                   class="px-4 py-2 bg-red-700 hover:bg-red-600 text-white rounded-xl text-xs font-semibold shadow transition flex items-center gap-1">
                    📄 Exportar PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Filtros --}}
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <form method="GET" action="{{ route('reportes.movimientos') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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
                    <label class="block text-xs text-slate-400 mb-1">Tipo de Movimiento</label>
                    <select name="tipo" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">Todos</option>
                        <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>📥 Entrada</option>
                        <option value="salida" {{ request('tipo') === 'salida' ? 'selected' : '' }}>📤 Salida</option>
                        <option value="traslado" {{ request('tipo') === 'traslado' ? 'selected' : '' }}>🔄 Traslado</option>
                        <option value="ajuste" {{ request('tipo') === 'ajuste' ? 'selected' : '' }}>🔧 Ajuste</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Ítem</label>
                    <select name="item_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">Todos</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>{{ $item->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Usuario</label>
                    <select name="usuario_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                        <option value="">Todos</option>
                        @foreach($usuarios as $usr)
                            <option value="{{ $usr->id }}" {{ request('usuario_id') == $usr->id ? 'selected' : '' }}>{{ $usr->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}"
                           class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Fecha Fin</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}"
                           class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:outline-none">
                </div>
                <div class="sm:col-span-2 flex gap-2 justify-end items-end">
                    <a href="{{ route('reportes.movimientos') }}" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-xl text-sm transition">Limpiar</a>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-sm font-semibold transition">Filtrar</button>
                </div>
            </form>
        </div>

        {{-- Tabla de Movimientos --}}
        <div class="bg-slate-900/80 border border-slate-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between">
                <h3 class="text-white font-semibold">Historial de Movimientos</h3>
                <span class="text-xs text-slate-400">{{ $movimientos->total() }} registros</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-800/60 text-slate-400 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-5 py-3">Fecha</th>
                            <th class="px-5 py-3">Tipo</th>
                            <th class="px-5 py-3">Ítem / SKU</th>
                            <th class="px-5 py-3 text-right">Cantidad</th>
                            <th class="px-5 py-3">Área Origen</th>
                            <th class="px-5 py-3">Área Destino</th>
                            <th class="px-5 py-3">Usuario</th>
                            <th class="px-5 py-3">Observación</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        @forelse($movimientos as $mov)
                            <tr class="hover:bg-slate-800/50 transition-colors">
                                <td class="px-5 py-3 text-slate-400 whitespace-nowrap text-xs">
                                    {{ $mov->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $badge = match($mov->tipo) {
                                            'entrada'  => ['📥', 'bg-emerald-900/40 text-emerald-400 border-emerald-800'],
                                            'salida'   => ['📤', 'bg-red-900/40 text-red-400 border-red-800'],
                                            'traslado' => ['🔄', 'bg-blue-900/40 text-blue-400 border-blue-800'],
                                            'ajuste'   => ['🔧', 'bg-amber-900/40 text-amber-400 border-amber-800'],
                                            default    => ['⚙️', 'bg-slate-800 text-slate-400 border-slate-700'],
                                        };
                                    @endphp
                                    <span class="px-2 py-0.5 border rounded-full text-xs font-semibold {{ $badge[1] }}">
                                        {{ $badge[0] }} {{ ucfirst($mov->tipo) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="font-medium text-white">{{ $mov->item->nombre }}</div>
                                    <div class="font-mono text-xs text-emerald-400">{{ $mov->item->sku }}</div>
                                </td>
                                <td class="px-5 py-3 text-right font-bold text-white">
                                    {{ number_format($mov->cantidad) }}
                                    <span class="text-xs text-slate-500">{{ $mov->item->unidadMedida->abreviatura ?? '' }}</span>
                                </td>
                                <td class="px-5 py-3 text-slate-400">{{ $mov->areaOrigen->nombre ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-400">{{ $mov->areaDestino->nombre ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-300">{{ $mov->usuario->name ?? '—' }}</td>
                                <td class="px-5 py-3 text-slate-500 text-xs max-w-xs truncate">{{ $mov->motivo ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                                    <div class="text-4xl mb-2">📭</div>
                                    <p>No se encontraron movimientos con los filtros aplicados.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($movimientos->hasPages())
                <div class="px-6 py-4 border-t border-slate-800">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>

    </div>
</x-app-layout>
