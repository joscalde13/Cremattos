<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompraRequest extends FormRequest
{
    public function authorize(): bool { return $this->user() !== null; }
    public function rules(): array
    {
        return ['fecha' => ['required', 'date'], 'producto_id' => ['required', 'exists:productos,id'], 'cantidad' => ['required', 'integer', 'min:1'], 'observaciones' => ['nullable', 'string']];
    }
}
