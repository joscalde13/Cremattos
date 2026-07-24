<?php

namespace App\Models;

use App\Models\Producto;
use Illuminate\Database\Eloquent\Model;

class Pack extends Model
{
    protected $table = 'packs';
    protected $fillable = ['nombre', 'descripcion', 'precio', 'estado'];
    protected function casts(): array
    {
        return ['precio' => 'decimal:2', 'estado' => 'boolean'];
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class)->withPivot('cantidad');
    }
}
