<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📜</span> Bitácora de Movimientos de Inventario
                </h2>
                <p class="text-sm text-slate-400">Historial oficial e inmutable de auditoría de todas las entradas, salidas, traslados y ajustes.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="px-3 py-2 bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-300 border border-emerald-500/30 font-medium rounded-xl text-xs transition">
                    + Entrada
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'salida']) }}" class="px-3 py-2 bg-rose-600/20 hover:bg-rose-600/30 text-rose-300 border border-rose-500/30 font-medium rounded-xl text-xs transition">
                    - Salida
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="px-3 py-2 bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/30 font-medium rounded-xl text-xs transition">
                    ⇄ Traslado
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'ajuste']) }}" class="px-3 py-2 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/30 font-medium rounded-xl text-xs transition">
                    ⚙️ Ajuste
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Filtros de la Bitácora -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 mb-6 backdrop-blur">
            <form method="GET" action="{{ route('movimientos.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Tipo de Movimiento</label>
                    <select name="tipo" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todos los Tipos</option>
                        <option value="entrada" {{ request('tipo') === 'entrada' ? 'selected' : '' }}>Entrada (+)</option>
                        <option value="salida" {{ request('tipo') === 'salida' ? 'selected' : '' }}>Salida (-)</option>
                        <option value="traslado" {{ request('tipo') === 'traslado' ? 'selected' : '' }}>Traslado (⇄)</option>
                        <option value="ajuste" {{ request('tipo') === 'ajuste' ? 'selected' : '' }}>Ajuste (⚙️)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Ítem / Producto</label>
                    <select name="item_id" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">Todos los Ítems</option>
                        @foreach($items as $it)
                            <option value="{{ $it->id }}" {{ request('item_id') == $it->id ? 'selected' : '' }}>{{ $it->nombre }} ({{ $it->sku }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Fecha Desde</label>
                    <input type="date" name="fecha_inicio" value="{{ request('fecha_inicio') }}" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Fecha Hasta</label>
                    <input type="date" name="fecha_fin" value="{{ request('fecha_fin') }}" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 font-medium rounded-xl text-sm transition">
                        Filtrar
                    </button>
                    @if(request()->hasAny(['tipo', 'item_id', 'fecha_inicio', 'fecha_fin']))
                        <a href="{{ route('movimientos.index') }}" class="px-3 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 border border-rose-500/30 rounded-xl text-sm transition">
                            Limpiar
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabla Inmutable de Bitácora -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Fecha / Hora</th>
                            <th class="px-6 py-4 text-center">Tipo</th>
                            <th class="px-6 py-4">Ítem / SKU</th>
                            <th class="px-6 py-4 text-center">Cantidad</th>
                            <th class="px-6 py-4">Origen / Destino</th>
                            <th class="px-6 py-4">Usuario Responsable</th>
                            <th class="px-6 py-4">Motivo / Auditoría</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($movimientos as $mov)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-mono text-xs text-slate-400 whitespace-nowrap">
                                    {{ $mov->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($mov->tipo === 'entrada')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            + Entrada
                                        </span>
                                    @elseif ($mov->tipo === 'salida')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            - Salida
                                        </span>
                                    @elseif ($mov->tipo === 'traslado')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                            ⇄ Traslado
                                        </span>
                                    @elseif ($mov->tipo === 'ajuste')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            ⚙️ Ajuste
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-medium text-white">
                                    <div>{{ $mov->item->nombre }}</div>
                                    <div class="text-xs font-mono text-slate-500">{{ $mov->item->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-base text-white">
                                    {{ $mov->cantidad }} <span class="text-xs font-normal text-slate-500">{{ $mov->item->unidadMedida->abreviatura ?? '' }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    @if ($mov->tipo === 'entrada')
                                        <span class="text-emerald-400">Destino:</span> {{ $mov->areaDestino->nombre ?? 'N/A' }}
                                    @elseif ($mov->tipo === 'salida')
                                        <span class="text-rose-400">Origen:</span> {{ $mov->areaOrigen->nombre ?? 'N/A' }}
                                    @elseif ($mov->tipo === 'traslado')
                                        <div><span class="text-rose-400">De:</span> {{ $mov->areaOrigen->nombre ?? 'N/A' }}</div>
                                        <div><span class="text-emerald-400">A:</span> {{ $mov->areaDestino->nombre ?? 'N/A' }}</div>
                                    @elseif ($mov->tipo === 'ajuste')
                                        <span>Área:</span> {{ $mov->areaDestino->nombre ?? ($mov->areaOrigen->nombre ?? 'N/A') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    <div class="font-semibold text-slate-200">{{ $mov->usuario->name ?? 'Sistema' }}</div>
                                    <div class="text-slate-500">{{ $mov->usuario->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-400 max-w-xs">
                                    {{ $mov->motivo ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                    No se han registrado movimientos de inventario en este periodo.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($movimientos->hasPages())
                <div class="px-6 py-4 border-t border-slate-800 bg-slate-900/60">
                    {{ $movimientos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
