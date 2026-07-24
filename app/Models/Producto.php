<?php

namespace App\Models;

use App\Models\Compra;
use App\Models\DetalleVenta;
use App\Models\Pack;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'productos';

    protected $fillable = ['nombre', 'sabor', 'descripcion', 'precio_venta', 'stock', 'estado', 'imagen'];

    protected function casts(): array
    {
        return ['precio_venta' => 'decimal:2', 'estado' => 'boolean'];
    }

    public function compras()
    {
        return $this->hasMany(Compra::class);
    }

    public function detallesVenta()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function packs()
    {
        return $this->belongsToMany(Pack::class)->withPivot('cantidad');
    }
}
