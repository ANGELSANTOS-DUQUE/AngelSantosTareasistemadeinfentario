<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAreaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'encargado_id' => ['nullable', 'exists:users,id'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}
