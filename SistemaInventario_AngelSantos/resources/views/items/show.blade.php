<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📦</span> {{ $item->nombre }}
                </h2>
                <p class="text-sm text-slate-400">SKU: <span class="font-mono text-emerald-400 font-bold">{{ $item->sku }}</span> | Empresa: <strong class="text-white">{{ $item->empresa->nombre }}</strong></p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('items.inventario', $item) }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-sm font-semibold transition flex items-center gap-1.5">
                    <span>📊</span> Ver Stock por Área
                </a>
                <a href="{{ route('items.edit', $item) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition">
                    Editar Ítem
                </a>
                <a href="{{ route('items.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                    ← Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Ficha del Ítem -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="flex flex-col md:flex-row gap-6 items-start">
                @if($item->imagen)
                    <img src="{{ asset('storage/' . $item->imagen) }}" alt="{{ $item->nombre }}" class="w-32 h-32 rounded-2xl object-cover border border-slate-700 shadow-md">
                @else
                    <div class="w-32 h-32 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-4xl text-emerald-400 shadow-md">
                        📦
                    </div>
                @endif

                <div class="flex-1 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Categoría</span>
                        <p class="text-sm font-semibold text-white mt-0.5">{{ $item->categoria->nombre ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Unidad de Medida</span>
                        <p class="text-sm font-semibold text-white mt-0.5">{{ $item->unidadMedida->nombre }} ({{ $item->unidadMedida->abreviatura }})</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Proveedor</span>
                        <p class="text-sm text-slate-200 mt-0.5">{{ $item->proveedor->nombre ?? 'Sin proveedor' }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Costo Unitario</span>
                        <p class="text-sm font-mono text-emerald-400 font-bold mt-0.5">L. {{ number_format($item->costo_unitario, 2) }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock Total Disponible</span>
                        <p class="text-lg font-bold text-white mt-0.5">{{ $item->stock_total }} {{ $item->unidadMedida->abreviatura }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Stock Mínimo</span>
                        <p class="text-sm font-mono text-slate-300 mt-0.5">{{ $item->stock_minimo }} {{ $item->unidadMedida->abreviatura }}</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado Alerta</span>
                        <p class="mt-0.5">
                            @php $status = $item->stock_status; @endphp
                            @if ($status === 'danger')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30">🔴 Agotado</span>
                            @elseif ($status === 'warning')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">🟡 Bajo Mínimo</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">🟢 Óptimo</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado en Catálogo</span>
                        <p class="mt-0.5">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $item->estado ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                {{ $item->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-span-2 sm:col-span-3 md:col-span-4">
                        <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Descripción</span>
                        <p class="text-sm text-slate-300 mt-0.5">{{ $item->descripcion ?? 'Sin descripción detallada' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Distribución de Stock por Área -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>📍</span> Ubicación y Stock por Área
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Sucursal</th>
                            <th class="px-6 py-3">Área / Almacén</th>
                            <th class="px-6 py-3">Encargado Responsable</th>
                            <th class="px-6 py-3 text-center">Cantidad en Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($item->inventariosArea as $inv)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    {{ $inv->area->sucursal->nombre }}
                                </td>
                                <td class="px-6 py-4 text-emerald-400 font-medium">
                                    {{ $inv->area->nombre }}
                                </td>
                                <td class="px-6 py-4 text-xs">
                                    {{ $inv->area->encargado->name ?? 'Sin encargado' }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-lg text-white">
                                    {{ $inv->cantidad }} <span class="text-xs font-normal text-slate-500">{{ $item->unidadMedida->abreviatura }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-6 text-center text-slate-500">
                                    Este ítem aún no tiene existencias registradas en ninguna de las áreas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
