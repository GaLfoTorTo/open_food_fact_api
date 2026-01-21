<?php

namespace App\Infraestructure\Http\Requests;

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "code" => "required",
            "status" => "required",
            "url" => "required",
            "creator" => "required",
            "created_t" => "required",
            "last_modified_t" => "required",
            "product_name" => "required",
            "quantity" => "required",
            "brands" => "required",
            "categories" => "required",
            "ingredients_text" => "required",
            "main_category" => "required",
        ];
    }

    public function message(){
        return [
            "code.required" => "O Código é obrigatório!",
            "status.required" => "O Status é obrigatório!",
            "url.required" => "A URL é obrigatório!",
            "creator.required" => "O Criador é obrigatório!",
            "created_t.required" => "O campo é obrigatório!",
            "last_modified_t.required" => "O campo é obrigatório!",
            "product_name.required" => "O Nome do Produto é obrigatório!",
            "quantity.required" => "A Quantidade é obrigatório!",
            "brands.required" => "A Marca é obrigatório!",
            "categories.required" => "Ao menos 1 Categoria deve ser informada!",
            "main_category.required" => "A Categoria principal é obrigatório!",
        ];
    }
}
