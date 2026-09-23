<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = $this->route('empresa')->id ?? $this->route('empresa');

        return [
            'nombre' => ['required', 'string', 'max:255'],
            'identificacion_fiscal' => [
                'required',
                'string',
                'max:50',
                Rule::unique('empresas', 'identificacion_fiscal')->ignore($empresaId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'correo' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}
