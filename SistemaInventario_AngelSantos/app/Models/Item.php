<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Item extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'items';

    protected $fillable = [
        'empresa_id',
        'categoria_id',
        'unidad_medida_id',
        'proveedor_id',
        'nombre',
        'sku',
        'descripcion',
        'imagen',
        'costo_unitario',
        'stock_minimo',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'costo_unitario' => 'decimal:2',
            'stock_minimo' => 'integer',
            'estado' => 'boolean',
        ];
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    public function inventariosArea(): HasMany
    {
        return $this->hasMany(InventarioArea::class);
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'inventario_area')
                    ->withPivot('id', 'cantidad')
                    ->withTimestamps();
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class);
    }

    /**
     * Calcula la suma total de stock en todas las áreas.
     */
    public function getStockTotalAttribute(): int
    {
        return (int) $this->inventariosArea()->sum('cantidad');
    }

    /**
     * Retorna el estado del stock frente al mínimo (ok, alerta, agotado).
     */
    public function getStockStatusAttribute(): string
    {
        $total = $this->stock_total;
        if ($total <= 0) {
            return 'danger'; // Agotado
        }
        if ($total <= $this->stock_minimo) {
            return 'warning'; // Cerca o en el mínimo
        }
        return 'success'; // Stock óptimo
    }
}
