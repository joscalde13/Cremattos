<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pack_producto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pack_id')->constrained('packs')->cascadeOnDelete();
            $table->foreignId('producto_id')->constrained('productos')->restrictOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
            $table->unique(['pack_id', 'producto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pack_producto');
    }
};
