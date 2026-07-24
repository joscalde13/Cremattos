<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->decimal('precio', 10, 2);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        DB::table('packs')->insert([
            ['nombre' => 'Kit Yogurt', 'descripcion' => '1 yogurt artesanal de 500 ml y kit de 4 toppings', 'precio' => 40, 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pack Dúo', 'descripcion' => '2 yogures artesanales de 500 ml y 2 kits de toppings', 'precio' => 75, 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pack Familiar', 'descripcion' => '3 yogures artesanales de 500 ml y 3 kits de toppings', 'precio' => 110, 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Pack Semana Energética', 'descripcion' => '5 yogures artesanales de 500 ml y 5 kits de toppings', 'precio' => 180, 'estado' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('packs');
    }
};
