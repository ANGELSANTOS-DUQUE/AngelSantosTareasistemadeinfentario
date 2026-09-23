<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Categoria;
use App\Models\Empresa;
use App\Models\Item;
use App\Models\Proveedor;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Item::with(['empresa', 'categoria', 'unidadMedida', 'proveedor', 'inventariosArea.area']);

        // Restricción por empresa
        if (!$user->isSuperAdmin() && $user->empresa_id) {
            $query->where('empresa_id', $user->empresa_id);
        } elseif ($request->filled('empresa_id')) {
            $query->where('empresa_id', $request->input('empresa_id'));
        }

        // Búsqueda por texto o SKU
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->input('categoria_id'));
        }

        // Filtro por proveedor
        if ($request->filled('proveedor_id')) {
            $query->where('proveedor_id', $request->input('proveedor_id'));
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->boolean('estado'));
        }

        $items = $query->latest()->paginate(10)->withQueryString();

        // Data para filtros
        $empresaId = $user->isSuperAdmin() ? $request->input('empresa_id') : $user->empresa_id;
        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : collect();
        $categorias = Categoria::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $proveedores = Proveedor::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();

        return view('items.index', compact('items', 'empresas', 'categorias', 'proveedores'));
    }

    public function create()
    {
        $user = Auth::user();
        $empresaId = $user->isSuperAdmin() ? request('empresa_id') : $user->empresa_id;

        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        $categorias = Categoria::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $proveedores = Proveedor::when($empresaId, fn($q) => $q->where('empresa_id', $empresaId))->get();
        $unidades = UnidadMedida::all();

        return view('items.create', compact('empresas', 'categorias', 'proveedores', 'unidades'));
    }

    public function store(StoreItemRequest $request)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        // Autogeneración de SKU si no se proporciona
        if (empty($data['sku'])) {
            do {
                $generatedSku = 'SKU-' . strtoupper(Str::random(8));
            } while (Item::where('empresa_id', $data['empresa_id'])->where('sku', $generatedSku)->exists());
            $data['sku'] = $generatedSku;
        }

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        Item::create($data);

        return redirect()->route('items.index')
            ->with('success', 'Ítem registrado exitosamente en el catálogo maestro.');
    }

    public function show(Item $item)
    {
        $item->load(['empresa', 'categoria', 'unidadMedida', 'proveedor', 'inventariosArea.area.sucursal', 'inventariosArea.area.encargado', 'movimientos.usuario']);
        return view('items.show', compact('item'));
    }

    public function inventario(Item $item)
    {
        $item->load(['empresa', 'categoria', 'unidadMedida', 'inventariosArea.area.sucursal', 'inventariosArea.area.encargado']);
        return view('items.inventario', compact('item'));
    }

    public function edit(Item $item)
    {
        $user = Auth::user();
        $empresaId = $item->empresa_id;

        $empresas = $user->isSuperAdmin() ? Empresa::where('estado', true)->get() : Empresa::where('id', $user->empresa_id)->get();
        $categorias = Categoria::where('empresa_id', $empresaId)->get();
        $proveedores = Proveedor::where('empresa_id', $empresaId)->get();
        $unidades = UnidadMedida::all();

        return view('items.edit', compact('item', 'empresas', 'categorias', 'proveedores', 'unidades'));
    }

    public function update(UpdateItemRequest $request, Item $item)
    {
        $data = $request->validated();
        $data['estado'] = $request->has('estado');

        if ($request->hasFile('imagen')) {
            if ($item->imagen && Storage::disk('public')->exists($item->imagen)) {
                Storage::disk('public')->delete($item->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('items', 'public');
        }

        $item->update($data);

        return redirect()->route('items.index')
            ->with('success', 'Ítem actualizado exitosamente.');
    }

    public function destroy(Item $item)
    {
        // Validar si tiene stock mayor a cero en alguna de sus áreas
        $stockTotal = $item->stock_total;

        if ($stockTotal > 0) {
            return redirect()->route('items.index')
                ->with('error', "No se puede eliminar el ítem '{$item->nombre}' porque cuenta con {$stockTotal} unidades de stock activo en inventario. Debe registrar salidas o traslados antes de eliminarlo.");
        }

        $item->delete();

        return redirect()->route('items.index')
            ->with('success', 'Ítem eliminado lógicamente del catálogo.');
    }
}
