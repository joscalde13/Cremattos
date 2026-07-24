@csrf
@if ($method ?? null)
    @method($method)
@endif
<div class="grid gap-5 sm:grid-cols-2">
    <label class="text-sm font-medium">Fecha<input type="date" name="fecha"
            value="{{ old('fecha', $compra->fecha->format('Y-m-d')) }}" required
            class="mt-2 w-full rounded-lg border-zinc-200"></label><label class="text-sm font-medium">Producto<select
            name="producto_id" required class="mt-2 w-full rounded-lg border-zinc-200">
            <option value="">Seleccionar producto</option>
            @foreach ($productos as $producto)
                <option value="{{ $producto->id }}" @selected(old('producto_id', $compra->producto_id) == $producto->id)>{{ $producto->nombre }}
                    
            @endforeach
        </select></label><label class="text-sm font-medium">Cantidad<input type="number" min="1" name="cantidad"
            value="{{ old('cantidad', $compra->cantidad ?? 1) }}" required
            class="mt-2 w-full rounded-lg border-zinc-200"></label><label
        class="text-sm font-medium sm:col-span-2">Observaciones
        <textarea name="observaciones" rows="3" class="mt-2 w-full rounded-lg border-zinc-200">{{ old('observaciones', $compra->observaciones) }}</textarea>
    </label>
</div>
<div class="mt-6 flex gap-3"><button
        class="rounded-lg bg-emerald-700 px-5 py-2.5 text-sm font-semibold text-white">{{ $submit }}</button><a
        href="{{ route('compras.index') }}" class="rounded-lg border px-5 py-2.5 text-sm">Cancelar</a></div>
