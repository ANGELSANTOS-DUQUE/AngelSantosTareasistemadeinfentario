<?php

namespace App\Http\Controllers;

use App\Exports\InventarioExport;
use App\Exports\MovimientosExport;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\InventarioArea;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Models\Sucursal;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    /**
     * Reporte interactivo de inventario actual.
     */
    public function inventario(Request $request)
    {
        $user = Auth::user();
        $query = InventarioArea::with(['item.categoria', 'item.unidadMedida', 'area.sucursal.empresa', 'area.encargado']);

        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;

        if ($empresaId) {
            $query->whereHas('area.sucursal', fn($q) => $q->where('empresa_id', $empresaId));
        }
        if ($request->filled('sucursal_id')) {
            $query->whereHas('area', fn($q) => $q->where('sucursal_id', $request->input('sucursal_id')));
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }
        if ($request->filled('categoria_id')) {
            $query->whereHas('item', fn($q) => $q->where('categoria_id', $request->input('categoria_id')));
        }

        $inventarios = $query->paginate(20)->withQueryString();

        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : collect();
        $sucursales = Sucursal::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $areas = Area::when($empresaId, fn($q) => $q->whereHas('sucursal', fn($sq) => $sq->where('empresa_id', $empresaId)))->get();
        $categorias = Categoria::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();

        return view('reportes.inventario', compact('inventarios', 'empresas', 'sucursales', 'areas', 'categorias'));
    }

    /**
     * Reporte interactivo de movimientos.
     */
    public function movimientos(Request $request)
    {
        $user = Auth::user();
        $query = MovimientoInventario::with(['item.unidadMedida', 'usuario', 'areaOrigen', 'areaDestino']);

        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;

        if ($empresaId) {
            $query->whereHas('item', fn($q) => $q->where('empresa_id', $empresaId));
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->input('usuario_id'));
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_fin'));
        }

        $movimientos = $query->latest('created_at')->paginate(20)->withQueryString();

        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : collect();
        $items = Item::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $usuarios = User::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();

        return view('reportes.movimientos', compact('movimientos', 'empresas', 'items', 'usuarios'));
    }

    /**
     * Exportación de inventario a Excel.
     */
    public function exportarInventarioExcel(Request $request)
    {
        $user = Auth::user();
        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;

        return Excel::download(
            new InventarioExport($empresaId, $request->input('sucursal_id'), $request->input('area_id'), $request->input('categoria_id')),
            'Reporte_Inventario_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Exportación de inventario a PDF.
     */
    public function exportarInventarioPdf(Request $request)
    {
        $user = Auth::user();
        $query = InventarioArea::with(['item.categoria', 'item.unidadMedida', 'area.sucursal.empresa', 'area.encargado']);

        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;

        if ($empresaId) {
            $query->whereHas('area.sucursal', fn($q) => $q->where('empresa_id', $empresaId));
        }
        if ($request->filled('sucursal_id')) {
            $query->whereHas('area', fn($q) => $q->where('sucursal_id', $request->input('sucursal_id')));
        }
        if ($request->filled('area_id')) {
            $query->where('area_id', $request->input('area_id'));
        }
        if ($request->filled('categoria_id')) {
            $query->whereHas('item', fn($q) => $q->where('categoria_id', $request->input('categoria_id')));
        }

        $inventarios = $query->get();
        $empresa = $empresaId ? Empresa::find($empresaId) : null;

        $pdf = Pdf::loadView('reportes.pdf.inventario', compact('inventarios', 'empresa'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Reporte_Inventario_' . now()->format('Ymd_His') . '.pdf');
    }

    /**
     * Exportación de movimientos a Excel.
     */
    public function exportarMovimientosExcel(Request $request)
    {
        $user = Auth::user();
        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;

        return Excel::download(
            new MovimientosExport(
                $empresaId,
                $request->input('tipo'),
                $request->input('item_id'),
                $request->input('fecha_inicio'),
                $request->input('fecha_fin')
            ),
            'Reporte_Movimientos_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    /**
     * Exportación de movimientos a PDF.
     */
    public function exportarMovimientosPdf(Request $request)
    {
        $user = Auth::user();
        $query = MovimientoInventario::with(['item.unidadMedida', 'usuario', 'areaOrigen', 'areaDestino']);

        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;

        if ($empresaId) {
            $query->whereHas('item', fn($q) => $q->where('empresa_id', $empresaId));
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->input('usuario_id'));
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_fin'));
        }

        $movimientos = $query->latest('created_at')->get();
        $empresa = $empresaId ? Empresa::find($empresaId) : null;

        $pdf = Pdf::loadView('reportes.pdf.movimientos', compact('movimientos', 'empresa'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Reporte_Movimientos_' . now()->format('Ymd_His') . '.pdf');
    }
}
