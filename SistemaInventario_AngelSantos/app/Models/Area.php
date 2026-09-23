<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Area extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'areas';

    protected $fillable = [
        'sucursal_id',
        'nombre',
        'descripcion',
        'encargado_id',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'estado' => 'boolean',
        ];
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function encargado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'encargado_id');
    }

    public function inventarios(): HasMany
    {
        return $this->hasMany(InventarioArea::class);
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'inventario_area')
                    ->withPivot('id', 'cantidad')
                    ->withTimestamps();
    }

    public function movimientosOrigen(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'area_origen_id');
    }

    public function movimientosDestino(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'area_destino_id');
    }
}
