<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $productos = Producto::query()->latest()->paginate(10);
        return view('productos.index', compact('productos'));
    }
    public function create(): View { return view('productos.create', ['producto' => new Producto(['estado' => true])]); }
    public function store(ProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['stock'] = 0;
        Producto::create($data);
        return to_route('productos.index')->with('success', 'Producto registrado correctamente.');
    }
    public function edit(Producto $producto): View { return view('productos.edit', compact('producto')); }
    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $data = $request->validated();
        $producto->update($data);
        return to_route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }
    public function destroy(Producto $producto): RedirectResponse { $producto->delete(); return to_route('productos.index')->with('success', 'Producto eliminado correctamente.'); }
}
