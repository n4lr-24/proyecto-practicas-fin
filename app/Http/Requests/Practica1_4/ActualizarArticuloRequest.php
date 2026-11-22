<?php

namespace App\Http\Requests\Practica1_4;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ActualizarArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->esRol('administrador') ?? false;
    }

    public function rules(): array
    {
        $articulo = $this->route('articulo');

        return [
            'titulo' => ['required', 'string', 'max:150', Rule::unique('articulos', 'titulo')->ignore($articulo)],
            'detalle' => ['required', 'string', 'max:1000'],
            'rubro_id' => ['required', 'integer', 'exists:rubros,id'],
            'precio' => ['required', 'numeric', 'gt:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'disponibilidad' => ['required', 'in:Disponible,Agotado'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'El nombre del producto es obligatorio.',
            'titulo.unique' => 'Ya existe otro producto con ese nombre.',
            'detalle.required' => 'La descripción es obligatoria.',
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
