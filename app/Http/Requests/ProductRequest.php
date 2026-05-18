<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del producto es requerido',
            'price.required' => 'El precio del producto es requerido',
            'price.numeric' => 'El precio del producto debe ser un número válido',
            'status.required' => 'El estado del producto es requerido',
            'description.required' => 'La descripción es requerida',
            'category_id.required' => 'La categoria es requerida',
            'category_id.numeric' => 'La categoria no tiene un valor válido',
            'category_id.exists' => 'La categoria no existe',
            'productImages.*.required' => 'Al menos una imágen es requerida',            
            'productImages.*.mimes' => 'Solo se admiten archivos JPG, PNG, JPEG',
            'productImages.*.max' => 'La imagen no debe pesar más de 5 MB'
            
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
{
    return [
        'name' => 'required',
        'price' => 'required|numeric',
        'status' => 'required|boolean',
        'description' => 'required',

        // ARRAY
        'productImages' => 'array',

        // CADA ARCHIVO
        'productImages.*' => 'file|mimes:jpg,jpeg,png|max:5120',

        'category_id' => 'required|numeric|exists:categories,id',
    ];
}
}
