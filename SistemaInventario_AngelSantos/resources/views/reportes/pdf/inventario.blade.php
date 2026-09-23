<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Inventario</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1e293b; background: #fff; }

        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 100%);
            color: white;
            padding: 18px 24px;
            margin-bottom: 20px;
        }
        .header h1 { font-size: 18px; font-weight: bold; margin-bottom: 4px; }
        .header p { font-size: 10px; opacity: 0.7; }
        .header .meta { float: right; text-align: right; font-size: 9px; opacity: 0.8; margin-top: -30px; }

        .summary-grid { display: table; width: 100%; margin-bottom: 16px; padding: 0 8px; }
        .summary-card { display: table-cell; width: 33%; padding: 10px 8px; text-align: center; }
        .summary-card-inner { background: #f1f5f9; border-radius: 6px; padding: 8px; }
        .summary-card .label { font-size: 8px; text-transform: uppercase; color: #64748b; }
        .summary-card .value { font-size: 16px; font-weight: bold; color: #0f172a; }

        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        thead th {
            background: #0f172a;
            color: white;
            padding: 7px 10px;
            text-align: left;
            text-transform: uppercase;
            font-size: 8px;
            letter-spacing: 0.5px;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:nth-child(odd) { background: #ffffff; }
        tbody td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .td-right { text-align: right; }
        .td-center { text-align: center; }

        .badge { padding: 2px 6px; border-radius: 9999px; font-size: 8px; font-weight: bold; }
        .badge-ok { background: #d1fae5; color: #065f46; }
        .badge-bajo { background: #fef3c7; color: #92400e; }
        .badge-agotado { background: #fee2e2; color: #991b1b; }

        .sku { color: #059669; font-family: monospace; font-size: 9px; }

        .footer { margin-top: 20px; padding: 10px 8px; border-top: 1px solid #e2e8f0; font-size: 8px; color: #94a3b8; display: table; width: 100%; }
        .footer-left { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }

        .section-title { padding: 0 8px; margin-bottom: 6px; font-size: 11px; font-weight: bold; color: #334155; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📦 Reporte de Inventario Actual</h1>
        <p>{{ $empresa ? $empresa->nombre : 'Todas las Empresas' }}</p>
        <div class="meta">
            Generado: {{ now()->format('d/m/Y H:i') }}<br>
            Total registros: {{ $inventarios->count() }}
        </div>
    </div>

    {{-- Resumen --}}
    <div class="summary-grid">
        <div class="summary-card">
            <div class="summary-card-inner">
                <div class="label">Total Registros</div>
                <div class="value">{{ $inventarios->count() }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-inner">
                <div class="label">Unidades Totales</div>
                <div class="value">{{ number_format($inventarios->sum('cantidad')) }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-inner">
                <div class="label">Ítems en Alerta</div>
                <div class="value" style="color: #dc2626;">
                    {{ $inventarios->filter(fn($i) => $i->cantidad <= $i->item->stock_minimo)->count() }}
                </div>
            </div>
        </div>
    </div>

    <p class="section-title">Detalle de Existencias</p>

    <table>
        <thead>
            <tr>
                <th>SKU</th>
                <th>Ítem</th>
                <th>Categoría</th>
                <th>Área</th>
                <th>Sucursal</th>
                <th>Unidad</th>
                <th class="td-right">Cantidad</th>
                <th class="td-right">Stock Mín.</th>
                <th class="td-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventarios as $inv)
                @php
                    $stockTotal = $inv->item->inventariosArea->sum('cantidad');
                    $alerta = $stockTotal <= 0 ? 'agotado' : ($stockTotal <= $inv->item->stock_minimo ? 'bajo' : 'ok');
                @endphp
                <tr>
                    <td class="sku">{{ $inv->item->sku }}</td>
                    <td>{{ $inv->item->nombre }}</td>
                    <td>{{ $inv->item->categoria->nombre ?? '—' }}</td>
                    <td>{{ $inv->area->nombre }}</td>
                    <td>{{ $inv->area->sucursal->nombre ?? '—' }}</td>
                    <td>{{ $inv->item->unidadMedida->simbolo ?? '—' }}</td>
                    <td class="td-right"><strong>{{ number_format($inv->cantidad) }}</strong></td>
                    <td class="td-right">{{ number_format($inv->item->stock_minimo) }}</td>
                    <td class="td-center">
                        @if($alerta === 'agotado')
                            <span class="badge badge-agotado">Agotado</span>
                        @elseif($alerta === 'bajo')
                            <span class="badge badge-bajo">Bajo</span>
                        @else
                            <span class="badge badge-ok">OK</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding: 20px; color: #94a3b8;">Sin registros</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div class="footer-left">Sistema de Control de Inventario — Documento Confidencial</div>
        <div class="footer-right">{{ now()->format('d/m/Y H:i:s') }}</div>
    </div>
</body>
</html>
