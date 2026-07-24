<?php

namespace App\Http\Controllers;

use App\Http\Requests\VentaRequest;
use App\Models\Pack;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class VentaController extends Controller
{
    public function index(Request $request): View
    {
        $ventas = Venta::withCount('detalles')->latest('fecha')->paginate(10);
        return view('ventas.index', compact('ventas'));
    }

    public function create(Request $request): View
    {
        $packs = Pack::with('productos')
            ->where('estado', true)
            ->whereHas('productos')
            ->orderBy('nombre')
            ->get();

        $preloadedPack = null;
        if ($request->filled('pack')) {
            $selectedPack = $packs->firstWhere('id', $request->integer('pack'));
            if ($selectedPack) {
                $preloadedPack = [
                    'nombre' => $selectedPack->nombre,
                    'precio' => (float) $selectedPack->precio,
                    'productos' => $selectedPack->productos->map(fn ($producto) => [
                        'id' => $producto->id,
                        'cantidad' => $producto->pivot->cantidad,
                    ])->values(),
                ];
            }
        }

        return view('ventas.create', [
            'venta' => new Venta(['fecha' => now()]),
            'productos' => Producto::where('estado', true)->orderBy('nombre')->get(),
            'packs' => $packs,
            'preloadedPack' => $preloadedPack,
        ]);
    }

    public function store(VentaRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $data = $request->validated();
            $rows = $this->validatedRows($data);
            $venta = Venta::create(['numero' => 'V-'.now()->format('YmdHis').'-'.random_int(10, 99), 'cliente' => $data['cliente'] ?? null, 'fecha' => $data['fecha'], 'total' => 0, 'observaciones' => $data['observaciones'] ?? null]);
            $this->syncDetails($venta, $rows);
        });
        return to_route('ventas.index')->with('success', 'Venta registrada y stock actualizado.');
    }

    public function sellPack(Pack $pack): RedirectResponse
    {
        DB::transaction(function () use ($pack) {
            $pack->load('productos');
            if ($pack->productos->isEmpty()) {
                throw ValidationException::withMessages(['pack' => 'Este pack todavía no tiene productos configurados.']);
            }

            $totalUnits = $pack->productos->sum(fn ($producto) => $producto->pivot->cantidad);
            $pricePerUnit = (float) $pack->precio / $totalUnits;
            $venta = Venta::create(['numero' => 'V-'.now()->format('YmdHis').'-'.random_int(10, 99), 'fecha' => now(), 'total' => $pack->precio]);

            foreach ($pack->productos as $producto) {
                $lockedProduct = Producto::whereKey($producto->id)->lockForUpdate()->firstOrFail();
                $quantity = (int) $producto->pivot->cantidad;
                if ($lockedProduct->stock < $quantity) {
                    throw ValidationException::withMessages(['pack' => "No hay stock suficiente para {$lockedProduct->nombre}. Disponible: {$lockedProduct->stock}."]);
                }
                $venta->detalles()->create(['producto_id' => $lockedProduct->id, 'cantidad' => $quantity, 'precio' => $pricePerUnit, 'subtotal' => $pricePerUnit * $quantity]);
                $lockedProduct->decrement('stock', $quantity);
            }
        });

        return to_route('ventas.index')->with('success', 'Venta del pack '.$pack->nombre.' registrada por Q'.number_format((float) $pack->precio, 2).'.');
    }

    public function show(Venta $venta): View
    {
        $venta->load('detalles.producto');
        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta): View
    {
        $venta->load('detalles');
        return view('ventas.edit', ['venta' => $venta, 'productos' => Producto::where('estado', true)->orWhereHas('detallesVenta', fn ($query) => $query->where('venta_id', $venta->id))->orderBy('nombre')->get(), 'packs' => Pack::with('productos')->where('estado', true)->whereHas('productos')->orderBy('nombre')->get()]);
    }

    public function update(VentaRequest $request, Venta $venta): RedirectResponse
    {
        DB::transaction(function () use ($request, $venta) {
            $data = $request->validated();
            $rows = $this->validatedRows($data);
            $venta->load('detalles');
            foreach ($venta->detalles as $detalle) Producto::whereKey($detalle->producto_id)->lockForUpdate()->increment('stock', $detalle->cantidad);
            $venta->update(['cliente' => $data['cliente'] ?? null, 'fecha' => $data['fecha'], 'observaciones' => $data['observaciones'] ?? null]);
            $venta->detalles()->delete();
            $this->syncDetails($venta, $rows);
        });
        return to_route('ventas.index')->with('success', 'Venta actualizada y stock recalculado.');
    }

    public function destroy(Venta $venta): RedirectResponse
    {
        DB::transaction(function () use ($venta) {
            foreach ($venta->detalles as $detalle) Producto::whereKey($detalle->producto_id)->lockForUpdate()->increment('stock', $detalle->cantidad);
            $venta->delete();
        });
        return to_route('ventas.index')->with('success', 'Venta eliminada y stock restaurado.');
    }

    private function validatedRows(array $data): array
    {
        $rows = [];
        foreach ($data['producto_id'] as $index => $productoId) {
            $rows[$productoId]['cantidad'] = ($rows[$productoId]['cantidad'] ?? 0) + (int) $data['cantidad'][$index];
            $rows[$productoId]['subtotal'] = ($rows[$productoId]['subtotal'] ?? 0) + ((int) $data['cantidad'][$index] * (float) $data['precio'][$index]);
            $rows[$productoId]['precio'] = $rows[$productoId]['subtotal'] / $rows[$productoId]['cantidad'];
        }
        return $rows;
    }

    private function syncDetails(Venta $venta, array $rows): void
    {
        $total = 0;
        foreach ($rows as $productoId => $row) {
            $producto = Producto::whereKey($productoId)->lockForUpdate()->firstOrFail();
            if ($producto->stock < $row['cantidad']) {
                throw ValidationException::withMessages(['cantidad' => "No hay stock suficiente para {$producto->nombre}. Disponible: {$producto->stock}."]);
            }
            $subtotal = $row['cantidad'] * $row['precio'];
            $venta->detalles()->create(['producto_id' => $producto->id, 'cantidad' => $row['cantidad'], 'precio' => $row['precio'], 'subtotal' => $subtotal]);
            $producto->decrement('stock', $row['cantidad']);
            $total += $subtotal;
        }
        $venta->update(['total' => $total]);
    }
}
