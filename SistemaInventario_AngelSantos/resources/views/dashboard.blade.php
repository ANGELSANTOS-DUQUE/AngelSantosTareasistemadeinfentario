<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                    <span>📊</span> Panel de Control & Dashboard
                </h2>
                <p class="text-sm text-slate-400">Resumen operativo en tiempo real del estado de tus existencias y almacenes.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @if(Auth::user()->isSuperAdmin() && $empresas->count() > 0)
                    <form method="GET" action="{{ route('dashboard') }}" class="flex items-center">
                        <select name="empresa_id" onchange="this.form.submit()" class="px-3 py-1.5 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">Todas las Empresas</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}" {{ $empresaId == $emp->id ? 'selected' : '' }}>{{ $emp->nombre }}</option>
                            @endforeach
                        </select>
                    </form>
                @endif
                <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-semibold shadow transition">
                    + Registrar Entrada
                </a>
                <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl text-xs font-semibold shadow transition">
                    ⇄ Registrar Traslado
                </a>
                <a href="{{ route('reportes.inventario') }}" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold transition">
                    📄 Ver Reportes
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Script de Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- 1. Tarjetas de KPIs Principales -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Ítems en Catálogo -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl backdrop-blur relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Catálogo Total</span>
                        <div class="text-3xl font-black text-white mt-1">{{ number_format($totalItems) }}</div>
                        <p class="text-xs text-slate-500 mt-1">Referencias de productos</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-2xl text-emerald-400">
                        📦
                    </div>
                </div>
            </div>

            <!-- Total Unidades en Stock -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl backdrop-blur relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Unidades en Stock</span>
                        <div class="text-3xl font-black text-cyan-400 mt-1">{{ number_format($totalUnidades) }}</div>
                        <p class="text-xs text-slate-500 mt-1">Existencias consolidadas</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-2xl text-cyan-400">
                        📊
                    </div>
                </div>
            </div>

            <!-- Ítems en Alerta de Stock Mínimo -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl backdrop-blur relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Alerta de Reposición</span>
                        <div class="text-3xl font-black {{ $itemsAlerta->count() > 0 ? 'text-amber-400' : 'text-emerald-400' }} mt-1">
                            {{ number_format($itemsAlerta->count()) }}
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Bajo stock o agotados</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl {{ $itemsAlerta->count() > 0 ? 'bg-amber-500/10 border-amber-500/30 text-amber-400' : 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400' }} border flex items-center justify-center text-2xl">
                        ⚠️
                    </div>
                </div>
            </div>

            <!-- Movimientos Registrados -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl backdrop-blur relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Auditoría / Bitácora</span>
                        <div class="text-3xl font-black text-indigo-400 mt-1">{{ number_format($ultimosMovimientos->count()) }}</div>
                        <p class="text-xs text-slate-500 mt-1">Operaciones recientes</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-2xl text-indigo-400">
                        📜
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. Alertas Visuales de Stock Mínimo (Semáforo Rojo / Amarillo) -->
        @if ($itemsAlerta->count() > 0)
            <div class="bg-slate-900/90 border border-amber-500/30 rounded-2xl p-6 shadow-xl backdrop-blur">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-amber-500/20 text-amber-400 rounded-xl">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-lg text-white">Alerta de Existencias Bajas y Agotadas</h3>
                            <p class="text-xs text-slate-400">Los siguientes productos requieren compra o traslado de reposición urgente.</p>
                        </div>
                    </div>
                    <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">
                        + Abastecer Stock
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach ($itemsAlerta->take(8) as $itemAlert)
                        @php $status = $itemAlert->stock_status; @endphp
                        <div class="p-3.5 rounded-xl border {{ $status === 'danger' ? 'bg-rose-950/40 border-rose-800/60' : 'bg-amber-950/40 border-amber-800/60' }}">
                            <div class="flex justify-between items-start">
                                <span class="font-bold text-sm text-white truncate">{{ $itemAlert->nombre }}</span>
                                <span class="text-xs font-bold px-2 py-0.5 rounded-full {{ $status === 'danger' ? 'bg-rose-500/20 text-rose-400' : 'bg-amber-500/20 text-amber-400' }}">
                                    {{ $status === 'danger' ? 'AGOTADO' : 'BAJO' }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-400 font-mono mt-0.5">{{ $itemAlert->sku }}</div>
                            <div class="mt-2 text-xs flex justify-between items-center text-slate-300">
                                <span>Disponible: <strong class="{{ $status === 'danger' ? 'text-rose-400' : 'text-amber-400' }}">{{ $itemAlert->stock_total }}</strong></span>
                                <span>Mínimo: <strong>{{ $itemAlert->stock_minimo }}</strong></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- 3. Gráficos de Distribución con Chart.js -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Gráfico Stock por Sucursal -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
                <h3 class="font-bold text-base text-white mb-4 flex items-center gap-2">
                    <span>🏬</span> Distribución de Unidades por Sucursal
                </h3>
                <div class="relative h-64">
                    <canvas id="chartSucursales"></canvas>
                </div>
            </div>

            <!-- Gráfico Stock por Categoría -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 shadow-xl backdrop-blur">
                <h3 class="font-bold text-base text-white mb-4 flex items-center gap-2">
                    <span>🏷️</span> Existencias por Categoría
                </h3>
                <div class="relative h-64">
                    <canvas id="chartCategorias"></canvas>
                </div>
            </div>
        </div>

        <!-- 4. Últimos Movimientos Registrados (Auditoría en vivo) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
            <div class="p-6 border-b border-slate-800 flex justify-between items-center">
                <div>
                    <h3 class="font-bold text-lg text-white">Últimas Operaciones en Inventario</h3>
                    <p class="text-xs text-slate-400">Historial reciente de entradas, salidas, traslados y ajustes.</p>
                </div>
                <a href="{{ route('movimientos.index') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">
                    Ver bitácora completa →
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="bg-slate-800/80 text-xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="px-6 py-3">Fecha</th>
                            <th class="px-6 py-3 text-center">Tipo</th>
                            <th class="px-6 py-3">Ítem / SKU</th>
                            <th class="px-6 py-3 text-center">Cantidad</th>
                            <th class="px-6 py-3">Responsable</th>
                            <th class="px-6 py-3">Detalle / Motivo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse ($ultimosMovimientos as $mov)
                            <tr class="hover:bg-slate-800/40 transition text-xs">
                                <td class="px-6 py-3 font-mono text-slate-400 whitespace-nowrap">
                                    {{ $mov->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if ($mov->tipo === 'entrada')
                                        <span class="px-2 py-0.5 rounded-full font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">+ Entrada</span>
                                    @elseif ($mov->tipo === 'salida')
                                        <span class="px-2 py-0.5 rounded-full font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/20">- Salida</span>
                                    @elseif ($mov->tipo === 'traslado')
                                        <span class="px-2 py-0.5 rounded-full font-semibold bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">⇄ Traslado</span>
                                    @elseif ($mov->tipo === 'ajuste')
                                        <span class="px-2 py-0.5 rounded-full font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20">⚙️ Ajuste</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-white font-medium">
                                    {{ $mov->item->nombre }}
                                </td>
                                <td class="px-6 py-3 text-center font-bold text-white">
                                    {{ $mov->cantidad }} {{ $mov->item->unidadMedida->abreviatura ?? '' }}
                                </td>
                                <td class="px-6 py-3 text-slate-300">
                                    {{ $mov->usuario->name ?? 'Sistema' }}
                                </td>
                                <td class="px-6 py-3 text-slate-400 truncate max-w-xs">
                                    {{ $mov->motivo ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">
                                    No hay movimientos recientes registrados.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Inicialización de Gráficos con Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Gráfico de Sucursales
            const ctxSucursales = document.getElementById('chartSucursales');
            if (ctxSucursales) {
                new Chart(ctxSucursales, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode($labelsSucursales) !!},
                        datasets: [{
                            label: 'Unidades en Stock',
                            data: {!! json_encode($dataSucursales) !!},
                            backgroundColor: 'rgba(16, 185, 129, 0.6)',
                            borderColor: 'rgb(16, 185, 129)',
                            borderWidth: 1,
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(255, 255, 255, 0.05)' },
                                ticks: { color: '#94a3b8' }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#94a3b8' }
                            }
                        }
                    }
                });
            }

            // 2. Gráfico de Categorías
            const ctxCategorias = document.getElementById('chartCategorias');
            if (ctxCategorias) {
                new Chart(ctxCategorias, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($labelsCategorias) !!},
                        datasets: [{
                            data: {!! json_encode($dataCategorias) !!},
                            backgroundColor: [
                                '#10b981', '#06b6d4', '#6366f1', '#f59e0b', '#ec4899', '#8b5cf6', '#14b8a6'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: '#94a3b8', font: { size: 11 } }
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
