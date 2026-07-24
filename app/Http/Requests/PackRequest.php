<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PackRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array
    {
        return ['nombre' => ['required', 'string', 'max:120'], 'descripcion' => ['required', 'string'], 'precio' => ['required', 'numeric', 'min:0'], 'estado' => ['required', 'boolean'], 'producto_id' => ['required', 'array', 'min:1'], 'producto_id.*' => ['required', 'distinct', 'exists:productos,id'], 'cantidad' => ['required', 'array', 'min:1'], 'cantidad.*' => ['required', 'integer', 'min:1']];
    }
}
