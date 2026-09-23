<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empresa_id' => ['required', 'exists:empresas,id'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'unidad_medida_id' => ['required', 'exists:unidades_medida,id'],
            'proveedor_id' => ['nullable', 'exists:proveedores,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('items', 'sku')->where(function ($q) {
                    return $q->where('empresa_id', $this->input('empresa_id'));
                }),
            ],
            'descripcion' => ['nullable', 'string'],
            'imagen' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'costo_unitario' => ['nullable', 'numeric', 'min:0'],
            'stock_minimo' => ['nullable', 'integer', 'min:0'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}
