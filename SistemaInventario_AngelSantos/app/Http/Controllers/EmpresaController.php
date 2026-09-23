<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmpresaRequest;
use App\Http\Requests\UpdateEmpresaRequest;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmpresaController extends Controller
{
    public function index(Request $request)
    {
        $query = Empresa::withCount(['sucursales', 'usuarios', 'items']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('identificacion_fiscal', 'like', "%{$search}%")
                  ->orWhere('correo', 'like', "%{$search}%");
            });
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        $empresas = $query->latest()->paginate(10)->withQueryString();

        return view('empresas.index', compact('empresas'));
    }

    public function create()
    {
        return view('empresas.create');
    }

    public function store(StoreEmpresaRequest $request)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        Empresa::create($data);

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa creada exitosamente.');
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['sucursales.areas.encargado', 'usuarios', 'categorias', 'proveedores']);
        return view('empresas.show', compact('empresa'));
    }

    public function edit(Empresa $empresa)
    {
        return view('empresas.edit', compact('empresa'));
    }

    public function update(UpdateEmpresaRequest $request, Empresa $empresa)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        if ($request->hasFile('logo')) {
            if ($empresa->logo && Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $empresa->update($data);

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa actualizada exitosamente.');
    }

    public function destroy(Empresa $empresa)
    {
        // Validar si tiene ítems con stock activo
        $tieneStock = $empresa->items()
            ->whereHas('inventariosArea', function ($q) {
                $q->where('cantidad', '>', 0);
            })->exists();

        if ($tieneStock) {
            return redirect()->route('empresas.index')
                ->with('error', 'No se puede eliminar la empresa porque tiene ítems con stock activo en sus áreas.');
        }

        $empresa->delete();

        return redirect()->route('empresas.index')
            ->with('success', 'Empresa eliminada lógicamente con éxito.');
    }
}
