<?php

namespace App\Models;

use App\Models\DetalleVenta;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $fillable = ['numero', 'cliente', 'fecha', 'total', 'observaciones'];
    protected function casts(): array
    {
        return ['fecha' => 'date', 'total' => 'decimal:2'];
    }
    public function detalles() { return $this->hasMany(DetalleVenta::class); }
}
