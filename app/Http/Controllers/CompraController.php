<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompraRequest;
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function index(Request $request): View
    {
        $compras = Compra::with([
            'producto' => fn ($query) => $query->withSum('detallesVenta as vendidas_total', 'cantidad'),
        ])->latest('fecha')->paginate(10);
        return view('compras.index', compact('compras'));
    }
    public function create(): View { return view('compras.create', ['compra' => new Compra(['fecha' => now()]), 'productos' => Producto::where('estado', true)->orderBy('nombre')->get()]); }
    public function edit(Compra $compra): View { return view('compras.edit', ['compra' => $compra, 'productos' => Producto::where('estado', true)->orWhereKey($compra->producto_id)->orderBy('nombre')->get()]); }
    public function store(CompraRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $data['numero'] = 'C-'.now()->format('YmdHis').'-'.random_int(10, 99);
            $producto = Producto::whereKey($data['producto_id'])->lockForUpdate()->firstOrFail();
            $data['precio_compra'] = $producto->precio_venta;
            $data['total'] = $data['cantidad'] * $data['precio_compra'];
            $compra = Compra::create($data);
            $producto->increment('stock', $compra->cantidad);
        });
        return to_route('compras.index')->with('success', 'Compra registrada y stock actualizado.');
    }
    public function update(CompraRequest $request, Compra $compra): RedirectResponse
    {
        DB::transaction(function () use ($request, $compra) {
            $data = $request->validated();
            $oldProduct = Producto::withTrashed()->whereKey($compra->producto_id)->lockForUpdate()->first();
            if ($oldProduct && $oldProduct->stock < $compra->cantidad) abort(422, 'No se puede editar la compra porque el stock ya fue utilizado.');
            if ($oldProduct) {
                $oldProduct->decrement('stock', $compra->cantidad);
            }
            $newProduct = Producto::whereKey($data['producto_id'])->lockForUpdate()->firstOrFail();
            $data['precio_compra'] = $newProduct->precio_venta;
            $data['total'] = $data['cantidad'] * $data['precio_compra'];
            $compra->update($data);
            $newProduct->increment('stock', $data['cantidad']);
        });
        return to_route('compras.index')->with('success', 'Compra actualizada y stock recalculado.');
    }
    public function destroy(Compra $compra): RedirectResponse
    {
        DB::transaction(function () use ($compra) {
            $producto = Producto::withTrashed()->whereKey($compra->producto_id)->lockForUpdate()->first();
            if ($producto && $producto->stock >= $compra->cantidad) {
                $producto->decrement('stock', $compra->cantidad);
            }
            $compra->delete();
        });
        return to_route('compras.index')->with('success', 'Compra eliminada y stock recalculado.');
    }
    public function show(Compra $compra): View { return view('compras.show', compact('compra')); }
}
