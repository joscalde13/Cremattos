<?php

use App\Models\Producto;
use App\Models\Pack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('una compra aumenta stock y una venta lo descuenta', function () {
    $user = User::factory()->create();
    $producto = Producto::create(['nombre' => 'Yogurt natural', 'sabor' => 'Natural', 'precio_venta' => 40, 'stock' => 0, 'estado' => true]);

    $this->actingAs($user)->post(route('compras.store'), [
        'fecha' => now()->toDateString(),
        'producto_id' => $producto->id,
        'cantidad' => 5,
    ])->assertRedirect(route('compras.index'));

    expect($producto->fresh()->stock)->toBe(5);
    $this->assertDatabaseHas('compras', ['precio_compra' => 40, 'total' => 200]);

    $this->actingAs($user)->post(route('ventas.store'), [
        'fecha' => now()->toDateString(),
        'cliente' => 'Ana',
        'producto_id' => [$producto->id],
        'cantidad' => [2],
        'precio' => [40],
    ])->assertRedirect(route('ventas.index'));

    expect($producto->fresh()->stock)->toBe(3);
    $this->assertDatabaseHas('ventas', ['total' => 80]);
    $this->actingAs($user)->get(route('dashboard'))->assertSee('Q-120.00');
});

test('una venta no permite superar el stock disponible', function () {
    $user = User::factory()->create();
    $producto = Producto::create(['nombre' => 'Yogurt fresa', 'precio_venta' => 35, 'stock' => 1, 'estado' => true]);

    $this->actingAs($user)->post(route('ventas.store'), [
        'fecha' => now()->toDateString(),
        'producto_id' => [$producto->id],
        'cantidad' => [2],
        'precio' => [35],
    ])->assertSessionHasErrors('cantidad');

    expect($producto->fresh()->stock)->toBe(1);
});

test('un pack configurado aparece en ventas con sus productos', function () {
    $user = User::factory()->create();
    $producto = Producto::create(['nombre' => 'Yogurt pack', 'precio_venta' => 40, 'stock' => 5, 'estado' => true]);

    $this->actingAs($user)->post(route('packs.store'), [
        'nombre' => 'Pack especial',
        'descripcion' => 'Pack de prueba',
        'precio' => 35,
        'estado' => 1,
        'producto_id' => [$producto->id],
        'cantidad' => [2],
    ])->assertRedirect(route('packs.index'));

    $pack = Pack::where('nombre', 'Pack especial')->firstOrFail();
    expect($pack->productos()->first()->pivot->cantidad)->toBe(2);

    $this->actingAs($user)->get(route('ventas.create'))->assertSee('Pack especial')->assertSee('Q35.00');

    $this->actingAs($user)->post(route('packs.sell', $pack))
        ->assertRedirect(route('ventas.index'));

    expect($producto->fresh()->stock)->toBe(3);
    $this->assertDatabaseHas('ventas', ['total' => 35]);
});
