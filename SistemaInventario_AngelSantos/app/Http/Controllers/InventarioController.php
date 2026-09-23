<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InventarioController extends Controller
{
    /**
     * Módulo de Inventario por Área (Stock consolidado / filtrado).
     */
    public function stock(Request $request)
    {
        $user = Auth::user();
        $query = InventarioArea::with(['item.categoria', 'item.unidadMedida', 'area.sucursal.empresa', 'area.encargado']);

        // Filtrar por empresa
        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $query->whereHas('area.sucursal', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            });
        }

        // Filtro por sucursal
        if ($request->filled('sucursal_id')) {
            $query->whereHas('area', function ($q) use ($request) {
                $q->where('sucursal_id', $request->input('sucursal_id'));
            });
        }

        // Filtro por área
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->whereHas('item', function ($q) use ($request) {
                $q->where('categoria_id', $request->input('categoria_id'));
            });
        }

        // Búsqueda por nombre de ítem o SKU
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $inventarios = $query->latest()->paginate(15)->withQueryString();

        $empresaId = $user->isSuperAdmin() ? null : $user->empresa_id;
        $sucursales = Sucursal::where('estado', true)->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $areas = Area::where('estado', true)->when($empresaId, fn($q) => $q->whereHas('sucursal', fn($sq) => $sq->where('empresa_id', $empresaId)))->get();
        $categorias = Categoria::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();

        return view('inventario.stock', compact('inventarios', 'sucursales', 'areas', 'categorias'));
    }
}
