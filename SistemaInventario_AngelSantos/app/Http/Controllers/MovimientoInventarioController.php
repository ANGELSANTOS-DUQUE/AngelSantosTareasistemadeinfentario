<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMovimientoRequest;
use App\Models\Area;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\MovimientoInventario;
use App\Services\InventarioService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovimientoInventarioController extends Controller
{
    protected InventarioService $inventarioService;

    public function __construct(InventarioService $inventarioService)
    {
        $this->inventarioService = $inventarioService;
    }

    /**
     * Bitácora inmutable de movimientos.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = MovimientoInventario::with(['item.unidadMedida', 'usuario', 'areaOrigen.sucursal', 'areaDestino.sucursal']);

        // Filtrar por empresa
        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $query->whereHas('item', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            });
        }

        // Filtro por tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->input('tipo'));
        }

        // Filtro por ítem
        if ($request->filled('item_id')) {
            $query->where('item_id', $request->input('item_id'));
        }

        // Filtro por usuario
        if ($request->filled('usuario_id')) {
            $query->where('usuario_id', $request->input('usuario_id'));
        }

        // Filtro por rango de fechas
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->input('fecha_inicio'));
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->input('fecha_fin'));
        }

        $movimientos = $query->latest('created_at')->paginate(15)->withQueryString();

        $empresaId = $user->isSuperAdmin() ? null : $user->empresa_id;
        $items = Item::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();

        return view('movimientos.index', compact('movimientos', 'items'));
    }

    /**
     * Formulario interactivo para registrar movimientos.
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $tipoSeleccionado = $request->input('tipo', 'entrada');
        $itemIdSeleccionado = $request->input('item_id');

        $empresaId = $user->isSuperAdmin() ? null : $user->empresa_id;

        $items = Item::where('estado', true)
            ->when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))
            ->with(['categoria', 'unidadMedida', 'inventariosArea'])
            ->get();

        $areas = Area::where('estado', true)
            ->when($empresaId, function ($q) use ($empresaId) {
                $q->whereHas('sucursal', fn($sq) => $sq->where('empresa_id', $empresaId));
            })
            ->with(['sucursal', 'encargado'])
            ->get();

        return view('movimientos.create', compact('items', 'areas', 'tipoSeleccionado', 'itemIdSeleccionado'));
    }

    /**
     * Procesa y registra un nuevo movimiento vía InventarioService.
     */
    public function store(StoreMovimientoRequest $request)
    {
        $tipo = $request->input('tipo');
        $itemId = (int) $request->input('item_id');
        $usuarioId = (int) Auth::id();
        $motivo = $request->input('motivo');

        try {
            if ($tipo === 'entrada') {
                $areaDestinoId = (int) $request->input('area_destino_id');
                $cantidad = (int) $request->input('cantidad');
                $this->inventarioService->registrarEntrada($itemId, $areaDestinoId, $cantidad, $usuarioId, $motivo);
                $mensaje = 'Entrada de stock registrada correctamente.';
            } elseif ($tipo === 'salida') {
                $areaOrigenId = (int) $request->input('area_origen_id');
                $cantidad = (int) $request->input('cantidad');
                $this->inventarioService->registrarSalida($itemId, $areaOrigenId, $cantidad, $usuarioId, $motivo);
                $mensaje = 'Salida de stock registrada correctamente.';
            } elseif ($tipo === 'traslado') {
                $areaOrigenId = (int) $request->input('area_origen_id');
                $areaDestinoId = (int) $request->input('area_destino_id');
                $cantidad = (int) $request->input('cantidad');
                $this->inventarioService->registrarTraslado($itemId, $areaOrigenId, $areaDestinoId, $cantidad, $usuarioId, $motivo);
                $mensaje = 'Traslado de inventario ejecutado con éxito y responsable actualizado.';
            } elseif ($tipo === 'ajuste') {
                $areaId = (int) $request->input('area_id');
                $nuevaCantidad = (int) $request->input('nueva_cantidad');
                $this->inventarioService->registrarAjuste($itemId, $areaId, $nuevaCantidad, $usuarioId, $motivo);
                $mensaje = 'Ajuste de inventario aplicado y justificación auditada.';
            }

            return redirect()->route('movimientos.index')->with('success', $mensaje ?? 'Movimiento registrado con éxito.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Endpoint API JSON para consultar stock disponible en tiempo real.
     */
    public function stockDisponible(Request $request)
    {
        $itemId = (int) $request->query('item_id');
        $areaId = (int) $request->query('area_id');

        $stock = $this->inventarioService->getStock($itemId, $areaId);
        $area = Area::with('encargado')->find($areaId);

        return response()->json([
            'item_id' => $itemId,
            'area_id' => $areaId,
            'stock' => $stock,
            'encargado' => $area && $area->encargado ? $area->encargado->name : 'Sin encargado',
        ]);
    }
}
