<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAreaRequest;
use App\Http\Requests\UpdateAreaRequest;
use App\Models\Area;
use App\Models\Sucursal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Area::with(['sucursal.empresa', 'encargado'])->withCount('inventarios');

        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $query->whereHas('sucursal', function ($q) use ($user) {
                $q->where('empresa_id', $user->empresa_id);
            });
        }

        if ($request->filled('sucursal_id')) {
            $query->where('sucursal_id', $request->input('sucursal_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhereHas('encargado', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        $areas = $query->latest()->paginate(10)->withQueryString();

        $sucursalesQuery = Sucursal::where('estado', true);
        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $sucursalesQuery->where('empresa_id', $user->empresa_id);
        }
        $sucursales = $sucursalesQuery->get();

        return view('areas.index', compact('areas', 'sucursales'));
    }

    public function create()
    {
        $user = Auth::user();

        $sucursalesQuery = Sucursal::where('estado', true);
        $usuariosQuery = User::where('estado', true);

        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $sucursalesQuery->where('empresa_id', $user->empresa_id);
            $usuariosQuery->where('empresa_id', $user->empresa_id);
        }

        $sucursales = $sucursalesQuery->get();
        $usuarios = $usuariosQuery->get();

        return view('areas.create', compact('sucursales', 'usuarios'));
    }

    public function store(StoreAreaRequest $request)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        Area::create($data);

        return redirect()->route('areas.index')
            ->with('success', 'Área creada exitosamente.');
    }

    public function show(Area $area)
    {
        $area->load(['sucursal.empresa', 'encargado', 'inventarios.item.categoria', 'inventarios.item.unidadMedida']);
        return view('areas.show', compact('area'));
    }

    public function edit(Area $area)
    {
        $user = Auth::user();

        $sucursalesQuery = Sucursal::where('estado', true);
        $usuariosQuery = User::where('estado', true);

        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $sucursalesQuery->where('empresa_id', $user->empresa_id);
            $usuariosQuery->where('empresa_id', $user->empresa_id);
        }

        $sucursales = $sucursalesQuery->get();
        $usuarios = $usuariosQuery->get();

        return view('areas.edit', compact('area', 'sucursales', 'usuarios'));
    }

    public function update(UpdateAreaRequest $request, Area $area)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        $area->update($data);

        return redirect()->route('areas.index')
            ->with('success', 'Área actualizada exitosamente.');
    }

    public function destroy(Area $area)
    {
        // Validar si tiene stock activo
        $stockTotal = $area->inventarios()->sum('cantidad');

        if ($stockTotal > 0) {
            return redirect()->route('areas.index')
                ->with('error', "No se puede eliminar el área '{$area->nombre}' porque cuenta con {$stockTotal} unidades de inventario activo. Debe trasladar o dar salida al stock antes de eliminarla.");
        }

        $area->delete();

        return redirect()->route('areas.index')
            ->with('success', 'Área eliminada lógicamente con éxito.');
    }
}
