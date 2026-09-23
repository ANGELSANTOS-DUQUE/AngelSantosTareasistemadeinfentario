<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📍</span> Área: {{ $area->nombre }}
                </h2>
                <p class="text-sm text-slate-400">Sucursal: <strong class="text-white">{{ $area->sucursal->nombre }}</strong> ({{ $area->sucursal->empresa->nombre }})</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('areas.edit', $area) }}" class="px-4 py-2 bg-amber-600 hover:bg-amber-500 text-white rounded-xl text-sm font-semibold transition">
                    Editar Área
                </a>
                <a href="{{ route('areas.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                    ← Volver
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Ficha del Área -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Sucursal / Empresa</span>
                    <p class="text-sm font-semibold text-white mt-0.5">{{ $area->sucursal->nombre }}</p>
                    <p class="text-xs text-slate-400">{{ $area->sucursal->empresa->nombre }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Encargado Responsable</span>
                    <p class="text-sm font-semibold text-emerald-400 mt-0.5">{{ $area->encargado->name ?? 'Sin encargado asignado' }}</p>
                    <p class="text-xs text-slate-400">{{ $area->encargado->email ?? '' }}</p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Estado</span>
                    <p class="mt-0.5">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $area->estado ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                            {{ $area->estado ? 'Activa' : 'Inactiva' }}
                        </span>
                    </p>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total de Ítems en Almacén</span>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $area->inventarios->count() }} referencias</p>
                </div>
                <div class="md:col-span-4">
                    <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Descripción / Notas</span>
                    <p class="text-sm text-slate-300 mt-0.5">{{ $area->descripcion ?? 'Sin notas registradas' }}</p>
                </div>
            </div>
        </div>

        <!-- Stock en esta Área -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <span>📦</span> Inventario Actual en este Almacén
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Ítem / SKU</th>
                            <th class="px-6 py-3">Categoría</th>
                            <th class="px-6 py-3 text-center">Unidad</th>
                            <th class="px-6 py-3 text-center">Stock Actual</th>
                            <th class="px-6 py-3 text-center">Stock Mínimo</th>
                            <th class="px-6 py-3 text-center">Alerta</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($area->inventarios as $inv)
                            <tr class="hover:bg-slate-800/40 transition">
                                <td class="px-6 py-4 font-medium text-white">
                                    <div class="font-semibold">{{ $inv->item->nombre }}</div>
                                    <div class="text-xs font-mono text-slate-500">SKU: {{ $inv->item->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-300">
                                    {{ $inv->item->categoria->nombre ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center text-xs">
                                    {{ $inv->item->unidadMedida->abreviatura ?? 'UND' }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-base text-white">
                                    {{ $inv->cantidad }}
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-slate-400">
                                    {{ $inv->item->stock_minimo }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($inv->cantidad <= 0)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                            Agotado
                                        </span>
                                    @elseif ($inv->cantidad <= $inv->item->stock_minimo)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            Stock Mínimo
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            Óptimo
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    No hay existencias registradas en esta área actualmente.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
