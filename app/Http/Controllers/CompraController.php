<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompraRequest;
use App\Models\Compra;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function index(Request $request): View
    {
        $compras = Compra::with('producto')->latest('fecha')->paginate(10);
        return view('compras.index', compact('compras'));
    }
    public function create(Request $request): View
    {
        $selectedProductId = $request->integer('producto');

        return view('compras.create', [
            'compra' => new Compra([
                'fecha' => now(),
                'producto_id' => $selectedProductId ?: null,
            ]),
            'productos' => Producto::where('estado', true)
                ->when($selectedProductId, fn ($query) => $query->orWhere('id', $selectedProductId))
                ->orderBy('nombre')
                ->get(),
        ]);
    }
    public function edit(Compra $compra): View { return view('compras.edit', ['compra' => $compra, 'productos' => Producto::where('estado', true)->orWhere('id', $compra->producto_id)->orderBy('nombre')->get()]); }
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
            $newProduct = Producto::whereKey($data['producto_id'])->lockForUpdate()->firstOrFail();

            $oldQuantity = (int) $compra->cantidad;
            $newQuantity = (int) $data['cantidad'];
            $isChangingProduct = (int) $compra->producto_id !== (int) $data['producto_id'];

            if ($isChangingProduct) {
                if ($oldProduct && $oldProduct->stock < $oldQuantity) {
                    throw ValidationException::withMessages([
                        'cantidad' => 'No se puede editar la compra porque el stock ya fue utilizado.',
                    ]);
                }

                if ($oldProduct) {
                    $oldProduct->decrement('stock', $oldQuantity);
                }

                $newProduct->increment('stock', $newQuantity);
            } elseif ($oldProduct) {
                $delta = $newQuantity - $oldQuantity;

                if ($delta > 0) {
                    $oldProduct->increment('stock', $delta);
                } elseif ($delta < 0) {
                    $unitsToRemove = abs($delta);
                    if ($oldProduct->stock < $unitsToRemove) {
                        throw ValidationException::withMessages([
                            'cantidad' => 'No se puede reducir la compra porque parte del stock ya fue utilizado.',
                        ]);
                    }
                    $oldProduct->decrement('stock', $unitsToRemove);
                }
            }

            $data['precio_compra'] = $newProduct->precio_venta;
            $data['total'] = $data['cantidad'] * $data['precio_compra'];
            $compra->update($data);
        });
        return to_route('compras.index')->with('success', 'Compra actualizada y stock recalculado.');
    }
    public function agregarCantidad(Request $request, Compra $compra): RedirectResponse
    {
        $data = $request->validate([
            'cantidad' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($compra, $data) {
            $compra = Compra::whereKey($compra->id)->lockForUpdate()->firstOrFail();
            $producto = Producto::withTrashed()->whereKey($compra->producto_id)->lockForUpdate()->firstOrFail();

            $cantidadExtra = (int) $data['cantidad'];
            $precioUnitario = (float) ($compra->precio_compra ?: $producto->precio_venta);

            $compra->cantidad = (int) $compra->cantidad + $cantidadExtra;
            $compra->total = (float) $compra->total + ($cantidadExtra * $precioUnitario);
            $compra->save();

            $producto->increment('stock', $cantidadExtra);
        });

        return back()->with('success', 'Cantidad agregada en la misma compra y stock actualizado.');
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
