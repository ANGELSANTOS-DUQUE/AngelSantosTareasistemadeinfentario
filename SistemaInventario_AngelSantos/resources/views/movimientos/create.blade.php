<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-white leading-tight flex items-center gap-2">
                <span>🔄</span> Registrar Operación de Inventario
            </h2>
            <a href="{{ route('movimientos.index') }}" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium border border-slate-700 transition">
                ← Bitácora de Movimientos
            </a>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-6 md:p-8 shadow-xl backdrop-blur" x-data="movimientoForm()">
            <form action="{{ route('movimientos.store') }}" method="POST" class="space-y-6" @submit="validarStock">
                @csrf

                <!-- Selector de Tipo de Operación -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Selecciona el Tipo de Movimiento *</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="entrada" x-model="tipo" class="sr-only peer">
                            <div class="p-3.5 text-center rounded-xl border border-slate-700 bg-slate-800/80 peer-checked:bg-emerald-600/20 peer-checked:border-emerald-500 peer-checked:text-emerald-300 transition">
                                <span class="text-xl block mb-1">📥</span>
                                <span class="font-semibold text-sm">Entrada</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="salida" x-model="tipo" class="sr-only peer">
                            <div class="p-3.5 text-center rounded-xl border border-slate-700 bg-slate-800/80 peer-checked:bg-rose-600/20 peer-checked:border-rose-500 peer-checked:text-rose-300 transition">
                                <span class="text-xl block mb-1">📤</span>
                                <span class="font-semibold text-sm">Salida</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="traslado" x-model="tipo" class="sr-only peer">
                            <div class="p-3.5 text-center rounded-xl border border-slate-700 bg-slate-800/80 peer-checked:bg-indigo-600/20 peer-checked:border-indigo-500 peer-checked:text-indigo-300 transition">
                                <span class="text-xl block mb-1">⇄</span>
                                <span class="font-semibold text-sm">Traslado</span>
                            </div>
                        </label>

                        <label class="cursor-pointer">
                            <input type="radio" name="tipo" value="ajuste" x-model="tipo" class="sr-only peer">
                            <div class="p-3.5 text-center rounded-xl border border-slate-700 bg-slate-800/80 peer-checked:bg-amber-600/20 peer-checked:border-amber-500 peer-checked:text-amber-300 transition">
                                <span class="text-xl block mb-1">⚙️</span>
                                <span class="font-semibold text-sm">Ajuste Físico</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Selección de Ítem -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Ítem / Producto *</label>
                    <select name="item_id" x-model="itemId" @change="consultarStock()" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                        <option value="">-- Seleccionar Ítem del Catálogo --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" {{ old('item_id', $itemIdSeleccionado) == $item->id ? 'selected' : '' }}>
                                {{ $item->nombre }} (SKU: {{ $item->sku }}) — [{{ $item->categoria->nombre ?? '' }}]
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- CAMPOS PARA ENTRADA -->
                <div x-show="tipo === 'entrada'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Área Destino (Donde ingresa el stock) *</label>
                        <select name="area_destino_id" x-model="areaDestinoId" :required="tipo === 'entrada'" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Seleccionar Área Destino --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">
                                    {{ $area->sucursal->nombre }} → {{ $area->nombre }} (Encargado: {{ $area->encargado->name ?? 'Sin asignar' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- CAMPOS PARA SALIDA -->
                <div x-show="tipo === 'salida'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Área Origen (De donde se descuenta el stock) *</label>
                        <select name="area_origen_id" x-model="areaOrigenId" @change="consultarStock()" :required="tipo === 'salida'" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Seleccionar Área Origen --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">
                                    {{ $area->sucursal->nombre }} → {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- CAMPOS PARA TRASLADO -->
                <div x-show="tipo === 'traslado'" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Área Origen *</label>
                        <select name="area_origen_id" x-model="areaOrigenId" @change="consultarStock()" :required="tipo === 'traslado'" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Seleccionar Área Origen --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">
                                    {{ $area->sucursal->nombre }} → {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Área Destino *</label>
                        <select name="area_destino_id" x-model="areaDestinoId" :required="tipo === 'traslado'" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Seleccionar Área Destino --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">
                                    {{ $area->sucursal->nombre }} → {{ $area->nombre }} (Encargado: {{ $area->encargado->name ?? 'Sin asignar' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Tarjeta de Feedback de Stock en Tiempo Real -->
                <div x-show="(tipo === 'salida' || tipo === 'traslado' || tipo === 'ajuste') && areaOrigenId && itemId" class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between">
                    <div>
                        <span class="text-xs text-slate-400 font-semibold uppercase tracking-wider block">Stock Disponible en Área Seleccionada:</span>
                        <div class="flex items-center space-x-2 mt-0.5">
                            <span class="text-2xl font-black" :class="stockDisponible > 0 ? 'text-emerald-400' : 'text-rose-400'" x-text="stockDisponible + ' unidades'"></span>
                            <span class="text-xs text-slate-500" x-show="encargadoArea" x-text="'(Encargado: ' + encargadoArea + ')'"></span>
                        </div>
                    </div>
                    <button type="button" @click="consultarStock()" class="text-xs text-slate-400 hover:text-white px-2 py-1 bg-slate-700 rounded-lg">
                        🔄 Refrescar
                    </button>
                </div>

                <!-- CAMPOS PARA AJUSTE -->
                <div x-show="tipo === 'ajuste'" class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Área a Ajustar *</label>
                        <select name="area_id" x-model="areaOrigenId" @change="consultarStock()" :required="tipo === 'ajuste'" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">
                            <option value="">-- Seleccionar Área --</option>
                            @foreach ($areas as $area)
                                <option value="{{ $area->id }}">
                                    {{ $area->sucursal->nombre }} → {{ $area->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-300 mb-2">Cantidad Física Real Contada *</label>
                        <input type="number" name="nueva_cantidad" min="0" x-model="nuevaCantidad" :required="tipo === 'ajuste'" placeholder="Ej. 25 (Nueva cantidad real en existencia)" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono font-bold">
                    </div>
                </div>

                <!-- Cantidad para Entrada, Salida, Traslado -->
                <div x-show="tipo !== 'ajuste'">
                    <label class="block text-sm font-semibold text-slate-300 mb-2">Cantidad a Mover *</label>
                    <input type="number" name="cantidad" min="1" x-model="cantidad" :required="tipo !== 'ajuste'" placeholder="Ej. 10" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none font-mono font-bold">
                </div>

                <!-- Motivo / Observación -->
                <div>
                    <label class="block text-sm font-semibold text-slate-300 mb-2">
                        Motivo / Observación <span x-show="tipo === 'ajuste'" class="text-rose-400 font-bold">* (Obligatorio en ajustes)</span>
                    </label>
                    <textarea name="motivo" rows="3" :required="tipo === 'ajuste'" placeholder="Ej. Compra factura #123, Consumo interno, Merma por vencimiento, Corrección por conteo físico..." class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-white placeholder-slate-500 focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 outline-none">{{ old('motivo') }}</textarea>
                </div>

                <div class="pt-6 border-t border-slate-800 flex justify-end gap-3">
                    <a href="{{ route('movimientos.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-xl text-sm font-medium transition">
                        Cancelar
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold rounded-xl text-sm shadow-lg shadow-emerald-900/30 transition">
                        Confirmar Movimiento
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function movimientoForm() {
            return {
                tipo: '{{ old('tipo', $tipoSeleccionado) }}',
                itemId: '{{ old('item_id', $itemIdSeleccionado) }}',
                areaOrigenId: '{{ old('area_origen_id', old('area_id')) }}',
                areaDestinoId: '{{ old('area_destino_id') }}',
                cantidad: '{{ old('cantidad', 1) }}',
                nuevaCantidad: '{{ old('nueva_cantidad', 0) }}',
                stockDisponible: 0,
                encargadoArea: '',

                init() {
                    if (this.itemId && this.areaOrigenId) {
                        this.consultarStock();
                    }
                },

                consultarStock() {
                    if (!this.itemId || !this.areaOrigenId) {
                        this.stockDisponible = 0;
                        this.encargadoArea = '';
                        return;
                    }

                    fetch(`/api/stock-disponible?item_id=${this.itemId}&area_id=${this.areaOrigenId}`)
                        .then(res => res.json())
                        .then(data => {
                            this.stockDisponible = data.stock;
                            this.encargadoArea = data.encargado;
                        })
                        .catch(err => {
                            console.error('Error al consultar stock:', err);
                        });
                },

                validarStock(e) {
                    if ((this.tipo === 'salida' || this.tipo === 'traslado') && parseInt(this.cantidad) > parseInt(this.stockDisponible)) {
                        e.preventDefault();
                        alert(`No hay suficiente stock en el área de origen. Stock disponible: ${this.stockDisponible}, solicitado: ${this.cantidad}.`);
                    }

                    if (this.tipo === 'traslado' && this.areaOrigenId === this.areaDestinoId) {
                        e.preventDefault();
                        alert('El área de origen y el área de destino no pueden ser iguales.');
                    }
                }
            }
        }
    </script>
</x-app-layout>
