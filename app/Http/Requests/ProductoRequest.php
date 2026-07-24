<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array
    {
        return ['nombre' => ['required', 'string', 'max:120'], 'sabor' => ['nullable', 'string', 'max:80'], 'descripcion' => ['nullable', 'string'], 'precio_venta' => ['required', 'numeric', 'min:0'], 'estado' => ['required', 'boolean']];
    }
}
