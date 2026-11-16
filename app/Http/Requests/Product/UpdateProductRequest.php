<?php

namespace App\Http\Requests\Product;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class UpdateProductRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
            'category_id' => ['sometimes', 'required', 'integer', 'exists:categories,id'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => "El campo 'name' es obligatorio",
            'name.string' => "El campo 'name' debe ser una cadena de texto",
            'name.max' => "El campo 'name' no debe superar los 255 caracteres",
            'price.required' => "El campo 'price' es obligatorio",
            'price.numeric' => "El campo 'price' debe ser un número válido",
            'price.min' => "El campo 'price' no puede ser negativo",
            'stock.required' => "El campo 'stock' es obligatorio",
            'stock.integer' => "El campo 'stock' debe ser un número entero",
            'stock.min' => "El campo 'stock' no puede ser negativo",
            'category_id.required' => "El campo 'category_id' es obligatorio",
            'category_id.integer' => "El campo 'category_id' debe ser un identificador válido",
            'category_id.exists' => 'La categoría seleccionada no existe',
        ];
    }
}
