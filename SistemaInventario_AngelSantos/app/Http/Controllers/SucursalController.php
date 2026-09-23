<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSucursalRequest;
use App\Http\Requests\UpdateSucursalRequest;
use App\Models\Empresa;
use App\Models\Sucursal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SucursalController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Sucursal::with(['empresa', 'areas'])->withCount('areas');

        // Si el usuario no es superadmin, restringir a su empresa
        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->input('empresa_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('direccion', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        $sucursales = $query->latest()->paginate(10)->withQueryString();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();

        return view('sucursales.index', compact('sucursales', 'empresas'));
    }

    public function create()
    {
        $user = Auth::user();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        return view('sucursales.create', compact('empresas'));
    }

    public function store(StoreSucursalRequest $request)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        Sucursal::create($data);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal creada exitosamente.');
    }

    public function show(Sucursal $sucursal)
    {
        $sucursal->load(['empresa', 'areas.encargado', 'areas.inventarios.item']);
        return view('sucursales.show', compact('sucursal'));
    }

    public function edit(Sucursal $sucursal)
    {
        $user = Auth::user();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        return view('sucursales.edit', compact('sucursal', 'empresas'));
    }

    public function update(UpdateSucursalRequest $request, Sucursal $sucursal)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        $sucursal->update($data);

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal actualizada exitosamente.');
    }

    public function destroy(Sucursal $sucursal)
    {
        // Validar si alguna de sus áreas tiene stock
        $tieneStock = $sucursal->areas()
            ->whereHas('inventarios', function ($q) {
                $q->where('cantidad', '>', 0);
            })->exists();

        if ($tieneStock) {
            return redirect()->route('sucursales.index')
                ->with('error', 'No se puede eliminar la sucursal porque tiene áreas con inventario activo.');
        }

        $sucursal->delete();

        return redirect()->route('sucursales.index')
            ->with('success', 'Sucursal eliminada lógicamente con éxito.');
    }
}
