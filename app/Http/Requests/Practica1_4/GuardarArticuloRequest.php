<?php

namespace App\Http\Requests\Practica1_4;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class GuardarArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esRol('administrador') ?? false;
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'min:3', 'max:150', 'unique:articulos,titulo'],
            'detalle' => ['required', 'string', 'min:5', 'max:1000'],
            'rubro_id' => ['required', 'integer', 'exists:rubros,id'],
            'precio' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'disponibilidad' => ['required', 'in:Disponible,Agotado'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                $stock = $this->input('stock');
                $disponibilidad = $this->input('disponibilidad');

                if (is_numeric($stock) && (int) $stock === 0 && $disponibilidad === 'Disponible') {
                    $validator->errors()->add('disponibilidad', 'Un producto con 0 existencias no puede marcarse como Disponible.');
                }

                if (is_numeric($stock) && (int) $stock > 0 && $disponibilidad === 'Agotado') {
                    $validator->errors()->add('disponibilidad', 'Un producto con existencias mayores a 0 no puede marcarse como Agotado.');
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El nombre del producto es obligatorio.',
            'titulo.min' => 'El nombre del producto debe tener al menos 3 caracteres.',
            'titulo.unique' => 'Ya existe un producto con ese nombre.',
            'detalle.required' => 'La descripción es obligatoria.',
            'detalle.min' => 'La descripción debe tener al menos 5 caracteres.',
            'rubro_id.required' => 'Selecciona una categoría.',
            'rubro_id.exists' => 'La categoría seleccionada no existe.',
            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un valor numérico.',
            'precio.gt' => 'El precio debe ser mayor que cero.',
            'stock.required' => 'Las existencias son obligatorias.',
            'stock.integer' => 'Las existencias deben ser un número entero.',
            'stock.min' => 'Las existencias deben ser igual o mayor que cero.',
            'disponibilidad.required' => 'Selecciona el estado del producto.',
            'disponibilidad.in' => 'El estado debe ser Disponible o Agotado.',
        ];
    }
}
