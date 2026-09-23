<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUnidadMedidaRequest;
use App\Http\Requests\UpdateUnidadMedidaRequest;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    public function index(Request $request)
    {
        $query = UnidadMedida::withCount('items');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'like', "%{$search}%")
                  ->orWhere('abreviatura', 'like', "%{$search}%");
        }

        $unidades = $query->latest()->paginate(10)->withQueryString();

        return view('unidades.index', compact('unidades'));
    }

    public function create()
    {
        return view('unidades.create');
    }

    public function store(StoreUnidadMedidaRequest $request)
    {
        UnidadMedida::create($request->validated());

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad de medida creada exitosamente.');
    }

    public function edit(UnidadMedida $unidade)
    {
        $unidad = $unidade;
        return view('unidades.edit', compact('unidad'));
    }

    public function update(UpdateUnidadMedidaRequest $request, UnidadMedida $unidade)
    {
        $unidade->update($request->validated());

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    public function destroy(UnidadMedida $unidade)
    {
        if ($unidade->items()->exists()) {
            return redirect()->route('unidades.index')
                ->with('error', "No se puede eliminar la unidad '{$unidade->nombre}' porque está asignada a uno o más ítems.");
        }

        $unidade->delete();

        return redirect()->route('unidades.index')
            ->with('success', 'Unidad de medida eliminada con éxito.');
    }
}
