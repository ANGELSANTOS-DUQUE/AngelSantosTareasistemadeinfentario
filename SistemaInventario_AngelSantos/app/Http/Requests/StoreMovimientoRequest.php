<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tipo = $this->input('tipo');

        $rules = [
            'tipo' => ['required', 'in:entrada,salida,traslado,ajuste'],
            'item_id' => ['required', 'exists:items,id'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ];

        if ($tipo === 'entrada') {
            $rules['area_destino_id'] = ['required', 'exists:areas,id'];
            $rules['cantidad'] = ['required', 'integer', 'min:1'];
        } elseif ($tipo === 'salida') {
            $rules['area_origen_id'] = ['required', 'exists:areas,id'];
            $rules['cantidad'] = ['required', 'integer', 'min:1'];
        } elseif ($tipo === 'traslado') {
            $rules['area_origen_id'] = ['required', 'exists:areas,id', 'different:area_destino_id'];
            $rules['area_destino_id'] = ['required', 'exists:areas,id'];
            $rules['cantidad'] = ['required', 'integer', 'min:1'];
        } elseif ($tipo === 'ajuste') {
            $rules['area_id'] = ['required', 'exists:areas,id'];
            $rules['nueva_cantidad'] = ['required', 'integer', 'min:0'];
            $rules['motivo'] = ['required', 'string', 'min:5', 'max:500'];
        }

        return $rules;
    }
}
