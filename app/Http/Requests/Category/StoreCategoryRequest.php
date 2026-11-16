<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseApiRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreCategoryRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
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
            'name.max' => "El campo 'name' no debe exceder los 255 caracteres",
            'name.unique' => 'Ya existe una categoría con este nombre',
            'description.string' => "El campo 'description' debe ser una cadena de texto",
        ];
    }
}
