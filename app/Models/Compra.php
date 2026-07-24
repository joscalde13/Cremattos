<?php

namespace App\Models;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    protected $table = 'compras';
    protected $fillable = ['numero', 'fecha', 'producto_id', 'cantidad', 'precio_compra', 'total', 'observaciones'];
    protected function casts(): array
    {
        return ['fecha' => 'date', 'precio_compra' => 'decimal:2', 'total' => 'decimal:2'];
    }
    public function producto() { return $this->belongsTo(Producto::class); }
}
