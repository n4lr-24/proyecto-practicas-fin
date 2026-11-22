<?php

namespace App\Http\Controllers\Practica1_4;

use App\Http\Controllers\Controller;
use App\Http\Requests\Practica1_4\ActualizarArticuloRequest;
use App\Http\Requests\Practica1_4\GuardarArticuloRequest;
use App\Models\Articulo;
use App\Models\Rubro;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticuloController extends Controller
{
    public function index(Request $request): View
    {
        $opcionesPaginacion = [10, 15, 25, 50];
        $porPagina = (int) $request->query('por_pagina', 10);
        if (! in_array($porPagina, $opcionesPaginacion, true)) {
            $porPagina = 10;
        }

        $camposOrden = ['titulo', 'precio', 'stock', 'created_at'];
        $ordenar = in_array($request->query('ordenar'), $camposOrden, true)
            ? $request->query('ordenar')
            : 'created_at';

        $direccion = in_array($request->query('direccion'), ['asc', 'desc'], true)
            ? $request->query('direccion')
            : 'desc';

        $buscar = trim((string) $request->query('buscar', ''));
        $rubroId = $request->query('rubro_id');
        $precioMin = $request->query('precio_min');
        $precioMax = $request->query('precio_max');
        $disponibilidad = $request->query('disponibilidad');

        $articulos = Articulo::query()
            ->with('rubro')
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->whereRaw('LOWER(titulo) LIKE ?', ['%'.mb_strtolower($buscar).'%']);
            })
            ->when($rubroId !== null && $rubroId !== '', function ($query) use ($rubroId) {
                $query->where('rubro_id', (int) $rubroId);
            })
            ->when($precioMin !== null && $precioMin !== '' && is_numeric($precioMin), function ($query) use ($precioMin) {
                $query->where('precio', '>=', (float) $precioMin);
            })
            ->when($precioMax !== null && $precioMax !== '' && is_numeric($precioMax), function ($query) use ($precioMax) {
                $query->where('precio', '<=', (float) $precioMax);
            })
            ->when(in_array($disponibilidad, ['Disponible', 'Agotado'], true), function ($query) use ($disponibilidad) {
                $query->where('disponibilidad', $disponibilidad);
            })
            ->orderBy($ordenar, $direccion)
            ->paginate($porPagina)
            ->withQueryString();

        $rubros = Rubro::orderBy('titulo')->get();

        return view('practica1_4.index', compact(
            'articulos',
            'rubros',
            'opcionesPaginacion',
            'porPagina',
            'ordenar',
            'direccion'
        ));
    }

    public function create(): View
    {
        $rubros = Rubro::orderBy('titulo')->get();

        return view('practica1_4.create', compact('rubros'));
    }

    public function store(GuardarArticuloRequest $request): RedirectResponse
    {
        Articulo::create($request->validated());

        return redirect()->route('articulos.index')
            ->with('exito', 'Producto registrado correctamente.');
    }

    public function edit(Articulo $articulo): View
    {
        $rubros = Rubro::orderBy('titulo')->get();

        return view('practica1_4.edit', compact('articulo', 'rubros'));
    }

    public function update(ActualizarArticuloRequest $request, Articulo $articulo): RedirectResponse
    {
        $articulo->update($request->validated());

        return redirect()->route('articulos.index')
            ->with('exito', 'Producto actualizado correctamente.');
    }

    public function destroy(Articulo $articulo): RedirectResponse
    {
        $articulo->delete();

        return redirect()->route('articulos.index')
            ->with('exito', 'Producto eliminado correctamente.');
    }
}
