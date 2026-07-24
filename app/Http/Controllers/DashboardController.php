<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Producto;
use App\Models\Venta;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();
        $ventas = Venta::query();
        $compras = Compra::query();

        $totalVendido = (float) $ventas->sum('total');
        $totalInvertido = (float) $compras->sum('total');

        return view('dashboard', [
            'totalVendido' => $totalVendido,
            'totalInvertido' => $totalInvertido,
            'gananciaTotal' => $totalVendido - $totalInvertido,
            'totalVentas' => $ventas->count(),
            'totalCompras' => $compras->count(),
            'productosRegistrados' => Producto::count(),
            'comprasMes' => ['cantidad' => $compras->whereBetween('fecha', [$inicioMes, $finMes])->count(), 'monto' => (float) $compras->whereBetween('fecha', [$inicioMes, $finMes])->sum('total')],
            'ventasMes' => ['cantidad' => $ventas->whereBetween('fecha', [$inicioMes, $finMes])->count(), 'monto' => (float) $ventas->whereBetween('fecha', [$inicioMes, $finMes])->sum('total')],
        ]);
    }
}
