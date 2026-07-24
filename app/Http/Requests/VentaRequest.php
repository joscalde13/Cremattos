<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array
    {
        return ['cliente' => ['nullable', 'string', 'max:120'], 'fecha' => ['required', 'date'], 'producto_id' => ['required', 'array', 'min:1'], 'producto_id.*' => ['required', 'exists:productos,id'], 'cantidad' => ['required', 'array', 'min:1'], 'cantidad.*' => ['required', 'integer', 'min:1'], 'precio' => ['required', 'array', 'min:1'], 'precio.*' => ['required', 'numeric', 'min:0'], 'observaciones' => ['nullable', 'string']];
    }
}
