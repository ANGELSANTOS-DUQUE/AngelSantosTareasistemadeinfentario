<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoriaRequest;
use App\Http\Requests\UpdateCategoriaRequest;
use App\Models\Categoria;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Categoria::with('empresa')->withCount('items');

        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->input('empresa_id'));
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $categorias = $query->latest()->paginate(10)->withQueryString();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();

        return view('categorias.index', compact('categorias', 'empresas'));
    }

    public function create()
    {
        $user = Auth::user();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        return view('categorias.create', compact('empresas'));
    }

    public function store(StoreCategoriaRequest $request)
    {
        Categoria::create($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function edit(Categoria $categoria)
    {
        $user = Auth::user();
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        return view('categorias.edit', compact('categoria', 'empresas'));
    }

    public function update(UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Categoria $categoria)
    {
        // Validar si tiene ítems con stock activo
        $tieneStock = $categoria->items()
            ->whereHas('inventariosArea', function ($q) {
                $q->where('cantidad', '>', 0);
            })->exists();

        if ($tieneStock) {
            return redirect()->route('categorias.index')
                ->with('error', "No se puede eliminar la categoría '{$categoria->nombre}' porque tiene ítems con stock activo en inventario.");
        }

        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría eliminada lógicamente con éxito.');
    }
}
