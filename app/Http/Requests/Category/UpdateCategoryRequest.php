<?php

namespace App\Http\Requests\Category;

use App\Http\Requests\BaseApiRequest;
use App\Models\Category;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $category = $this->route('category');
        $categoryId = $category instanceof Category ? $category->id : $category;

        return [
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId),
            ],
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
