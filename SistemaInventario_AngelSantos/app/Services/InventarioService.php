<?php

namespace App\Services;

use App\Models\Area;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use Exception;
use Illuminate\Support\Facades\DB;

class InventarioService
{
    /**
     * Obtiene la cantidad de stock actual de un ítem en un área específica.
     */
    public function getStock(int $itemId, int $areaId): int
    {
        $inv = InventarioArea::where('item_id', $itemId)
            ->where('area_id', $areaId)
            ->first();

        return $inv ? (int) $inv->cantidad : 0;
    }

    /**
     * Registra una Entrada de inventario.
     */
    public function registrarEntrada(int $itemId, int $areaDestinoId, int $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new Exception("La cantidad de entrada debe ser mayor a cero.");
        }

        return DB::transaction(function () use ($itemId, $areaDestinoId, $cantidad, $usuarioId, $motivo) {
            $inv = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaDestinoId],
                ['cantidad' => 0]
            );

            $inv->increment('cantidad', $cantidad);

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'entrada',
                'cantidad' => $cantidad,
                'area_origen_id' => null,
                'area_destino_id' => $areaDestinoId,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo ?? 'Ingreso de nuevo stock',
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Registra una Salida de inventario.
     */
    public function registrarSalida(int $itemId, int $areaOrigenId, int $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new Exception("La cantidad de salida debe ser mayor a cero.");
        }

        return DB::transaction(function () use ($itemId, $areaOrigenId, $cantidad, $usuarioId, $motivo) {
            $stockActual = $this->getStock($itemId, $areaOrigenId);

            if ($stockActual < $cantidad) {
                throw new Exception("Stock insuficiente en el área seleccionada. Stock disponible: {$stockActual}, solicitado: {$cantidad}.");
            }

            $inv = InventarioArea::where('item_id', $itemId)
                ->where('area_id', $areaOrigenId)
                ->first();

            $inv->decrement('cantidad', $cantidad);

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'salida',
                'cantidad' => $cantidad,
                'area_origen_id' => $areaOrigenId,
                'area_destino_id' => null,
                'usuario_id' => $usuarioId,
                'motivo' => $motivo ?? 'Salida / Consumo / Merma',
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Registra un Traslado de inventario entre áreas de la misma empresa.
     */
    public function registrarTraslado(int $itemId, int $areaOrigenId, int $areaDestinoId, int $cantidad, int $usuarioId, ?string $motivo = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new Exception("La cantidad a trasladar debe ser mayor a cero.");
        }

        if ($areaOrigenId === $areaDestinoId) {
            throw new Exception("El área origen y destino no pueden ser la misma.");
        }

        return DB::transaction(function () use ($itemId, $areaOrigenId, $areaDestinoId, $cantidad, $usuarioId, $motivo) {
            // Validar que ambas áreas pertenezcan a la misma empresa
            $areaOrigen = Area::with('sucursal')->findOrFail($areaOrigenId);
            $areaDestino = Area::with('sucursal', 'encargado')->findOrFail($areaDestinoId);

            if ($areaOrigen->sucursal->empresa_id !== $areaDestino->sucursal->empresa_id) {
                throw new Exception("Los traslados solo están permitidos entre áreas de la misma empresa.");
            }

            $stockActual = $this->getStock($itemId, $areaOrigenId);

            if ($stockActual < $cantidad) {
                throw new Exception("Stock insuficiente en el área origen. Disponible: {$stockActual}, requerido: {$cantidad}.");
            }

            // Descontar en origen
            $invOrigen = InventarioArea::where('item_id', $itemId)
                ->where('area_id', $areaOrigenId)
                ->first();
            $invOrigen->decrement('cantidad', $cantidad);

            // Incrementar en destino
            $invDestino = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaDestinoId],
                ['cantidad' => 0]
            );
            $invDestino->increment('cantidad', $cantidad);

            $encargadoDestino = $areaDestino->encargado ? $areaDestino->encargado->name : 'Sin encargado';
            $motivoCompleto = $motivo ? "{$motivo} (Responsable reasignado a: {$encargadoDestino})" : "Traslado a {$areaDestino->nombre} (Responsable: {$encargadoDestino})";

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'traslado',
                'cantidad' => $cantidad,
                'area_origen_id' => $areaOrigenId,
                'area_destino_id' => $areaDestinoId,
                'usuario_id' => $usuarioId,
                'motivo' => $motivoCompleto,
                'created_at' => now(),
            ]);
        });
    }

    /**
     * Registra un Ajuste manual de inventario (positivo o negativo).
     */
    public function registrarAjuste(int $itemId, int $areaId, int $nuevaCantidad, int $usuarioId, string $motivo): MovimientoInventario
    {
        if (empty(trim($motivo))) {
            throw new Exception("El motivo del ajuste es obligatorio para auditoría.");
        }

        if ($nuevaCantidad < 0) {
            throw new Exception("La cantidad final de stock no puede ser negativa.");
        }

        return DB::transaction(function () use ($itemId, $areaId, $nuevaCantidad, $usuarioId, $motivo) {
            $stockActual = $this->getStock($itemId, $areaId);
            $diferencia = $nuevaCantidad - $stockActual;

            if ($diferencia === 0) {
                throw new Exception("La cantidad ingresada es igual al stock actual existente.");
            }

            $inv = InventarioArea::firstOrCreate(
                ['item_id' => $itemId, 'area_id' => $areaId],
                ['cantidad' => 0]
            );

            $inv->cantidad = $nuevaCantidad;
            $inv->save();

            $tipoAjuste = $diferencia > 0 ? "Ajuste positivo (+{$diferencia})" : "Ajuste negativo ({$diferencia})";
            $motivoAuditado = "[{$tipoAjuste}] Anterior: {$stockActual} -> Nuevo: {$nuevaCantidad}. Justificación: {$motivo}";

            return MovimientoInventario::create([
                'item_id' => $itemId,
                'tipo' => 'ajuste',
                'cantidad' => abs($diferencia),
                'area_origen_id' => $diferencia < 0 ? $areaId : null,
                'area_destino_id' => $diferencia > 0 ? $areaId : null,
                'usuario_id' => $usuarioId,
                'motivo' => $motivoAuditado,
                'created_at' => now(),
            ]);
        });
    }
}
