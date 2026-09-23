<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProveedorRequest;
use App\Http\Requests\UpdateProveedorRequest;
use App\Models\Empresa;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Proveedor::with('empresa')->withCount('items');

        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->input('empresa_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('contacto', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%")
                  ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $proveedores = $query->latest()->paginate(10)->withQueryString();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();

        return view('proveedores.index', compact('proveedores', 'empresas'));
    }

    public function create()
    {
        $user = Auth::user();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        return view('proveedores.create', compact('empresas'));
    }

    public function store(StoreProveedorRequest $request)
    {
        Proveedor::create($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    public function edit(Proveedor $proveedore)
    {
        $proveedor = $proveedore;
        $user = Auth::user();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        return view('proveedores.edit', compact('proveedor', 'empresas'));
    }

    public function update(UpdateProveedorRequest $request, Proveedor $proveedore)
    {
        $proveedore->update($request->validated());

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    public function destroy(Proveedor $proveedore)
    {
        $proveedore->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado lógicamente con éxito.');
    }
}
