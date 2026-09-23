<?php

namespace App\Exports;

use App\Models\MovimientoInventario;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MovimientosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $empresaId;
    protected $tipo;
    protected $itemId;
    protected $fechaInicio;
    protected $fechaFin;

    public function __construct($empresaId = null, $tipo = null, $itemId = null, $fechaInicio = null, $fechaFin = null)
    {
        $this->empresaId = $empresaId;
        $this->tipo = $tipo;
        $this->itemId = $itemId;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function collection()
    {
        $query = MovimientoInventario::with(['item.unidadMedida', 'usuario', 'areaOrigen', 'areaDestino']);

        if ($this->empresaId) {
            $query->whereHas('item', fn($q) => $q->where('empresa_id', $this->empresaId));
        }
        if ($this->tipo) {
            $query->where('tipo', $this->tipo);
        }
        if ($this->itemId) {
            $query->where('item_id', $this->itemId);
        }
        if ($this->fechaInicio) {
            $query->whereDate('created_at', '>=', $this->fechaInicio);
        }
        if ($this->fechaFin) {
            $query->whereDate('created_at', '<=', $this->fechaFin);
        }

        return $query->latest('created_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Fecha y Hora',
            'Tipo de Movimiento',
            'SKU',
            'Ítem / Producto',
            'Cantidad',
            'Unidad',
            'Área Origen',
            'Área Destino',
            'Usuario Responsable',
            'Motivo / Justificación',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->created_at->format('d/m/Y H:i:s'),
            strtoupper($row->tipo),
            $row->item->sku,
            $row->item->nombre,
            $row->cantidad,
            $row->item->unidadMedida->abreviatura ?? 'UND',
            $row->areaOrigen->nombre ?? '—',
            $row->areaDestino->nombre ?? '—',
            $row->usuario->name ?? 'Sistema',
            $row->motivo ?? '—',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']]],
        ];
    }
}
