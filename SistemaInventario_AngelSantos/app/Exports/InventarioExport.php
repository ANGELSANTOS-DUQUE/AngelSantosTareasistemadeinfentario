<?php

namespace App\Exports;

use App\Models\InventarioArea;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventarioExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $empresaId;
    protected $sucursalId;
    protected $areaId;
    protected $categoriaId;

    public function __construct($empresaId = null, $sucursalId = null, $areaId = null, $categoriaId = null)
    {
        $this->empresaId = $empresaId;
        $this->sucursalId = $sucursalId;
        $this->areaId = $areaId;
        $this->categoriaId = $categoriaId;
    }

    public function collection()
    {
        $query = InventarioArea::with(['item.categoria', 'item.unidadMedida', 'area.sucursal.empresa', 'area.encargado']);

        if ($this->empresaId) {
            $query->whereHas('area.sucursal', fn($q) => $q->where('empresa_id', $this->empresaId));
        }
        if ($this->sucursalId) {
            $query->whereHas('area', fn($q) => $q->where('sucursal_id', $this->sucursalId));
        }
        if ($this->areaId) {
            $query->where('area_id', $this->areaId);
        }
        if ($this->categoriaId) {
            $query->whereHas('item', fn($q) => $q->where('categoria_id', $this->categoriaId));
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'SKU',
            'Ítem / Producto',
            'Categoría',
            'Empresa',
            'Sucursal',
            'Área / Almacén',
            'Encargado Responsable',
            'Stock Actual',
            'Unidad',
            'Stock Mínimo',
            'Estado Stock',
        ];
    }

    public function map($row): array
    {
        $status = 'Óptimo';
        if ($row->cantidad <= 0) {
            $status = 'Agotado';
        } elseif ($row->cantidad <= $row->item->stock_minimo) {
            $status = 'Bajo Mínimo';
        }

        return [
            $row->item->sku,
            $row->item->nombre,
            $row->item->categoria->nombre ?? 'N/A',
            $row->area->sucursal->empresa->nombre ?? 'N/A',
            $row->area->sucursal->nombre ?? 'N/A',
            $row->area->nombre ?? 'N/A',
            $row->area->encargado->name ?? 'Sin asignar',
            $row->cantidad,
            $row->item->unidadMedida->abreviatura ?? 'UND',
            $row->item->stock_minimo,
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF10B981']]],
        ];
    }
}
