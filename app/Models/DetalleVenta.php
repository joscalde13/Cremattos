<?php

namespace App\Models;

use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';
    protected $fillable = ['venta_id', 'producto_id', 'cantidad', 'precio', 'subtotal'];
    protected function casts(): array
    {
        return ['precio' => 'decimal:2', 'subtotal' => 'decimal:2'];
    }
    public function venta() { return $this->belongsTo(Venta::class); }
    public function producto() { return $this->belongsTo(Producto::class); }
}
