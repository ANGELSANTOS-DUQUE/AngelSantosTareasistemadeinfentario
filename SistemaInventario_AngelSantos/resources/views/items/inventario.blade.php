<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📊</span> Inventario por Área: {{ $item->nombre }}
                </h2>
                <p class="text-sm text-slate-400">SKU: <span class="font-mono text-emerald-400 font-bold">{{ $item->sku }}</span> | Unidad: {{ $item->unidadMedida->nombre }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('items.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                    ← Catálogo de Ítems
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Resumen KPI -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 backdrop-blur shadow-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Stock Consolidado</span>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-white">{{ $item->stock_total }}</span>
                    <span class="text-sm font-medium text-slate-400">{{ $item->unidadMedida->abreviatura }}</span>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 backdrop-blur shadow-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Stock Mínimo Exigido</span>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-amber-400">{{ $item->stock_minimo }}</span>
                    <span class="text-sm font-medium text-slate-400">{{ $item->unidadMedida->abreviatura }}</span>
                </div>
            </div>

            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 backdrop-blur shadow-xl">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block mb-1">Áreas con Presencia</span>
                <div class="flex items-baseline space-x-2">
                    <span class="text-3xl font-black text-emerald-400">{{ $item->inventariosArea->where('cantidad', '>', 0)->count() }}</span>
                    <span class="text-sm font-medium text-slate-400">ubicaciones</span>
                </div>
            </div>
        </div>

        <!-- Tabla detallada de existencias por área y responsable -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center">
                <h3 class="font-bold text-lg text-white">Detalle de Existencias por Área y Responsables</h3>
                <div class="text-xs text-slate-400">
                    Empresa: <strong class="text-white">{{ $item->empresa->nombre }}</strong>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-4">Sucursal</th>
                            <th class="px-6 py-4">Área / Almacén</th>
                            <th class="px-6 py-4">Encargado Responsable</th>
                            <th class="px-6 py-4">Correo Contacto</th>
                            <th class="px-6 py-4 text-center">Cantidad en Stock</th>
                            <th class="px-6 py-4 text-center">% del Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @php $stockTotal = $item->stock_total; @endphp
                        @forelse ($item->inventariosArea as $inv)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-semibold text-white">
                                    {{ $inv->area->sucursal->nombre }}
                                </td>
                                <td class="px-6 py-4 font-medium text-emerald-400">
                                    📍 {{ $inv->area->nombre }}
                                </td>
                                <td class="px-6 py-4 text-slate-200">
                                    {{ $inv->area->encargado->name ?? 'Sin encargado' }}
                                </td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-400">
                                    {{ $inv->area->encargado->email ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-xl text-white">
                                    {{ $inv->cantidad }} <span class="text-xs font-normal text-slate-400">{{ $item->unidadMedida->abreviatura }}</span>
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-slate-400 font-mono">
                                    {{ $stockTotal > 0 ? number_format(($inv->cantidad / $stockTotal) * 100, 1) : 0 }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-slate-500">
                                    No hay stock distribuido para este ítem en ninguna área actualmente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
