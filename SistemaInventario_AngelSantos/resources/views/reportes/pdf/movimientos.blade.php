<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Movimientos</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1e293b; background: #fff; }

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
        .summary-card { display: table-cell; width: 25%; padding: 6px; text-align: center; }
        .summary-card-inner { background: #f1f5f9; border-radius: 6px; padding: 8px; }
        .summary-card .label { font-size: 7px; text-transform: uppercase; color: #64748b; }
        .summary-card .value { font-size: 14px; font-weight: bold; color: #0f172a; }

        table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
        thead th {
            background: #0f172a;
            color: white;
            padding: 6px 8px;
            text-align: left;
            text-transform: uppercase;
            font-size: 7.5px;
            letter-spacing: 0.5px;
        }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:nth-child(odd) { background: #ffffff; }
        tbody td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; vertical-align: middle; }
        .td-right { text-align: right; }
        .td-center { text-align: center; }

        .badge { padding: 2px 5px; border-radius: 9999px; font-size: 7.5px; font-weight: bold; }
        .badge-entrada  { background: #d1fae5; color: #065f46; }
        .badge-salida   { background: #fee2e2; color: #991b1b; }
        .badge-traslado { background: #dbeafe; color: #1e40af; }
        .badge-ajuste   { background: #fef3c7; color: #92400e; }

        .sku { color: #059669; font-family: monospace; }
        .obs { color: #64748b; font-style: italic; }

        .footer { margin-top: 16px; padding: 8px; border-top: 1px solid #e2e8f0; font-size: 7.5px; color: #94a3b8; display: table; width: 100%; }
        .footer-left { display: table-cell; }
        .footer-right { display: table-cell; text-align: right; }

        .section-title { padding: 0 8px; margin-bottom: 6px; font-size: 11px; font-weight: bold; color: #334155; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔄 Reporte de Movimientos de Inventario</h1>
        <p>{{ $empresa ? $empresa->nombre : 'Todas las Empresas' }}</p>
        <div class="meta">
            Generado: {{ now()->format('d/m/Y H:i') }}<br>
            Total: {{ $movimientos->count() }} registros
        </div>
    </div>

    {{-- Resumen por tipo --}}
    <div class="summary-grid">
        @php
            $entradas  = $movimientos->where('tipo','entrada')->count();
            $salidas   = $movimientos->where('tipo','salida')->count();
            $traslados = $movimientos->where('tipo','traslado')->count();
            $ajustes   = $movimientos->where('tipo','ajuste')->count();
        @endphp
        <div class="summary-card">
            <div class="summary-card-inner">
                <div class="label">Entradas</div>
                <div class="value" style="color:#059669;">{{ $entradas }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-inner">
                <div class="label">Salidas</div>
                <div class="value" style="color:#dc2626;">{{ $salidas }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-inner">
                <div class="label">Traslados</div>
                <div class="value" style="color:#2563eb;">{{ $traslados }}</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-card-inner">
                <div class="label">Ajustes</div>
                <div class="value" style="color:#d97706;">{{ $ajustes }}</div>
            </div>
        </div>
    </div>

    <p class="section-title">Detalle de Movimientos</p>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Ítem</th>
                <th>SKU</th>
                <th class="td-right">Cantidad</th>
                <th>Área Origen</th>
                <th>Área Destino</th>
                <th>Usuario</th>
                <th>Observación</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $mov)
                <tr>
                    <td>{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td class="td-center">
                        <span class="badge badge-{{ $mov->tipo }}">{{ ucfirst($mov->tipo) }}</span>
                    </td>
                    <td>{{ $mov->item->nombre }}</td>
                    <td class="sku">{{ $mov->item->sku }}</td>
                    <td class="td-right"><strong>{{ number_format($mov->cantidad) }}</strong> {{ $mov->item->unidadMedida->simbolo ?? '' }}</td>
                    <td>{{ $mov->areaOrigen->nombre ?? '—' }}</td>
                    <td>{{ $mov->areaDestino->nombre ?? '—' }}</td>
                    <td>{{ $mov->usuario->name ?? '—' }}</td>
                    <td class="obs">{{ Str::limit($mov->observacion ?? '—', 40) }}</td>
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
