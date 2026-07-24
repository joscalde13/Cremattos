@csrf
@if ($method ?? null) @method($method) @endif

<div class="grid gap-5 sm:grid-cols-2">
    <label class="text-sm font-medium">Cliente (opcional)<input name="cliente" value="{{ old('cliente', $venta->cliente) }}" class="mt-2 w-full rounded-lg border-zinc-200"></label>
    <label class="text-sm font-medium">Fecha<input type="date" name="fecha" value="{{ old('fecha', $venta->fecha->format('Y-m-d')) }}" required class="mt-2 w-full rounded-lg border-zinc-200"></label>
</div>

<div class="mt-6">
    <div class="mb-3 flex items-center justify-between"><h2 class="font-semibold">Productos</h2><button type="button" id="add-row" class="text-sm font-medium text-[#b34c6d]">+ Agregar producto</button></div>
    <div id="packs-data" data-preloaded-pack='@json($preloadedPack ?? null)'></div>
    @if ($packs->isNotEmpty())
        <div class="mb-5">
            <span class="mb-3 block text-sm font-medium text-zinc-500">Packs especiales</span>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($packs as $pack)
                    @php
                        $packPayload = [
                            'nombre' => $pack->nombre,
                            'precio' => (float) $pack->precio,
                            'productos' => $pack->productos->map(function ($producto) {
                                return [
                                    'id' => $producto->id,
                                    'cantidad' => $producto->pivot->cantidad,
                                ];
                            })->values()->all(),
                        ];
                    @endphp
                    <button type="button" class="pack-button rounded-xl border border-[#e78ca9] bg-[#fff6f9] px-4 py-4 text-left text-base font-semibold text-[#b34c6d] shadow-sm transition hover:-translate-y-0.5 hover:bg-[#ffeaf2] hover:shadow-md" data-pack='@json($packPayload)'>
                        <span class="block">{{ $pack->nombre }}</span>
                        <span class="mt-1 block text-sm font-medium">Q{{ number_format($pack->precio, 2) }}</span>
                        <span class="mt-3 inline-flex rounded-lg bg-[#b34c6d] px-3 py-1.5 text-xs font-semibold text-white">Agregar a venta</span>
                    </button>
                @endforeach
            </div>
        </div>
    @endif
    <div id="sale-rows" class="space-y-3">
        @php
            $rows = old('producto_id')
                ? collect(old('producto_id'))->map(function ($id, $index) {
                    return [
                        'producto_id' => $id,
                        'cantidad' => old('cantidad')[$index],
                        'precio' => old('precio')[$index],
                    ];
                })
                : ($venta->detalles->isNotEmpty()
                    ? $venta->detalles->map(function ($detail) {
                        return [
                            'producto_id' => $detail->producto_id,
                            'cantidad' => $detail->cantidad,
                            'precio' => $detail->precio,
                        ];
                    })
                    : collect([['producto_id' => '', 'cantidad' => 1, 'precio' => '']]));
        @endphp
        @foreach ($rows as $row)
            <div class="sale-row grid gap-2 sm:grid-cols-[1fr_110px_130px_auto]"><select name="producto_id[]" required class="rounded-lg border-zinc-200"><option value="">Producto</option>@foreach ($productos as $producto)<option value="{{ $producto->id }}" data-price="{{ $producto->precio_venta }}" @selected($row['producto_id'] == $producto->id)>{{ $producto->nombre }} ({{ $producto->stock }})</option>@endforeach</select><input type="number" min="1" name="cantidad[]" value="{{ $row['cantidad'] }}" required class="rounded-lg border-zinc-200"><input type="number" step="0.01" min="0" name="precio[]" value="{{ $row['precio'] }}" required class="rounded-lg border-zinc-200"><button type="button" class="remove-row rounded-lg border border-rose-200 px-3 text-rose-600">Quitar</button></div>
        @endforeach
    </div>
</div>

<label class="mt-6 block text-sm font-medium">Observaciones<textarea name="observaciones" rows="3" class="mt-2 w-full rounded-lg border-zinc-200">{{ old('observaciones', $venta->observaciones) }}</textarea></label>
<div class="mt-6 flex gap-3"><button class="rounded-lg bg-[#e78ca9] px-5 py-2.5 text-sm font-semibold text-white">{{ $submit }}</button><a href="{{ route('ventas.index') }}" class="rounded-lg border px-5 py-2.5 text-sm">Cancelar</a></div>

<template id="row-template"><div class="sale-row grid gap-2 sm:grid-cols-[1fr_110px_130px_auto]"><select name="producto_id[]" required class="rounded-lg border-zinc-200"><option value="">Producto</option>@foreach ($productos as $producto)<option value="{{ $producto->id }}" data-price="{{ $producto->precio_venta }}">{{ $producto->nombre }} ({{ $producto->stock }})</option>@endforeach</select><input type="number" min="1" name="cantidad[]" value="1" required class="rounded-lg border-zinc-200"><input type="number" step="0.01" min="0" name="precio[]" value="0" required class="rounded-lg border-zinc-200"><button type="button" class="remove-row rounded-lg border border-rose-200 px-3 text-rose-600">Quitar</button></div></template>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const rows = document.querySelector('#sale-rows');
    const packDataElement = document.querySelector('#packs-data');
    const addRow = () => rows.append(document.querySelector('#row-template').content.cloneNode(true));

    const addPackToTable = pack => {
        if (!pack || !Array.isArray(pack.productos) || pack.productos.length === 0) return;

        rows.querySelectorAll('.sale-row').forEach(row => {
            if (!row.querySelector('select').value) row.remove();
        });

        const totalUnits = pack.productos.reduce((total, product) => total + Number(product.cantidad), 0);
        if (totalUnits <= 0) return;

        pack.productos.forEach(product => {
            addRow();
            const row = rows.lastElementChild;
            row.querySelector('select').value = product.id;
            row.querySelector('input[name="cantidad[]"]').value = product.cantidad;
            row.querySelector('input[name="precio[]"]').value = (pack.precio * product.cantidad / totalUnits).toFixed(2);
        });
    };

    document.querySelector('#add-row').addEventListener('click', addRow);
    document.querySelectorAll('.pack-button').forEach(button => button.addEventListener('click', () => {
        const pack = JSON.parse(button.dataset.pack);
        addPackToTable(pack);
    }));

    if (packDataElement?.dataset.preloadedPack && packDataElement.dataset.preloadedPack !== 'null') {
        const preloadedPack = JSON.parse(packDataElement.dataset.preloadedPack);
        addPackToTable(preloadedPack);
    }

    rows.addEventListener('change', event => {
        if (event.target.matches('select')) {
            const option = event.target.selectedOptions[0];
            const price = event.target.closest('.sale-row').querySelector('input[name="precio[]"]');
            if (option.dataset.price) price.value = option.dataset.price;
        }
    });
    rows.addEventListener('click', event => {
        if (event.target.matches('.remove-row') && rows.querySelectorAll('.sale-row').length > 1) event.target.closest('.sale-row').remove();
    });
});
</script>
