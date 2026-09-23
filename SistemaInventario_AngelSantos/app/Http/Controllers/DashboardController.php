<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;

        // 1. Total de ítems en catálogo
        $totalItems = Item::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->count();

        // 2. Total de unidades en stock consolidado
        $totalUnidades = InventarioArea::when($empresaId, function ($q) use ($empresaId) {
            $q->whereHas('item', fn($iq) => $iq->where('empresa_id', $empresaId));
        })->sum('cantidad');

        // 3. Ítems en alerta de stock mínimo o agotados
        $itemsAlerta = Item::with(['categoria', 'unidadMedida', 'inventariosArea'])
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get()
            ->filter(function ($item) {
                return $item->stock_total <= $item->stock_minimo;
            })
            ->values();

        // 4. Últimos 10 movimientos registrados
        $ultimosMovimientos = MovimientoInventario::with(['item.unidadMedida', 'usuario', 'areaOrigen', 'areaDestino'])
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->whereHas('item', fn($iq) => $iq->where('empresa_id', $empresaId));
            })
            ->latest('created_at')
            ->limit(10)
            ->get();

        // 5. Stock por Sucursal (para gráfico Chart.js)
        $sucursales = Sucursal::with(['areas.inventarios'])
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get();

        $labelsSucursales = [];
        $dataSucursales = [];
        foreach ($sucursales as $suc) {
            $labelsSucursales[] = $suc->nombre;
            $dataSucursales[] = $suc->areas->sum(fn($a) => $a->inventarios->sum('cantidad'));
        }

        // 6. Stock por Categoría (para gráfico Chart.js)
        $categorias = Categoria::with(['items.inventariosArea'])
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->get();

        $labelsCategorias = [];
        $dataCategorias = [];
        foreach ($categorias as $cat) {
            $labelsCategorias[] = $cat->nombre;
            $dataCategorias[] = $cat->items->sum(fn($it) => $it->inventariosArea->sum('cantidad'));
        }

        // Empresas para selector en caso de Super Admin
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : collect();

        return view('dashboard', compact(
            'totalItems',
            'totalUnidades',
            'itemsAlerta',
            'ultimosMovimientos',
            'labelsSucursales',
            'dataSucursales',
            'labelsCategorias',
            'dataCategorias',
            'empresas',
            'empresaId'
        ));
    }
}
