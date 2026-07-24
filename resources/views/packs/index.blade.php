<x-layouts.app :title="__('Packs especiales')">
    <div class="min-h-full bg-[#fffafc] p-4 sm:p-8 dark:bg-zinc-900">
        <div class="mx-auto max-w-6xl">
            <x-flash />
            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end"><div><p class="text-sm font-medium uppercase tracking-[0.2em] text-[#b34c6d]">Ofertas</p><h1 class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-white">Packs especiales</h1></div><a href="{{ route('packs.create') }}" class="rounded-lg bg-[#e78ca9] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#d97898]">Nuevo pack</a></div>
            <form class="mb-5 flex gap-2"><input name="buscar" value="{{ request('buscar') }}" placeholder="Buscar pack" class="w-full max-w-sm rounded-lg border-zinc-200 bg-white text-sm"><button class="rounded-lg bg-zinc-800 px-4 text-sm font-medium text-white">Buscar</button></form>
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($packs as $pack)
                    @php($canSell = $pack->estado && $pack->productos->isNotEmpty())
                    <article class="pack-card rounded-2xl bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:bg-zinc-800 {{ $canSell ? 'cursor-pointer ring-1 ring-transparent hover:ring-emerald-300' : '' }}" @if($canSell) data-sell-form="sell-pack-{{ $pack->id }}" data-pack-name="{{ $pack->nombre }}" data-pack-price="{{ number_format($pack->precio, 2) }}" @endif>
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <h2 class="text-xl font-semibold">{{ $pack->nombre }}</h2>
                                <p class="mt-2 text-sm leading-6 text-zinc-500">{{ $pack->descripcion }}</p>
                            </div>
                            <p class="text-xl font-semibold text-[#b34c6d]">Q{{ number_format($pack->precio, 2) }}</p>
                        </div>

                        <div class="mt-5 flex items-center justify-between">
                            <span class="rounded-full px-2.5 py-1 text-xs {{ $pack->estado ? 'bg-emerald-100 text-emerald-700' : 'bg-zinc-100 text-zinc-500' }}">{{ $pack->estado ? 'Activo' : 'Inactivo' }}</span>
                            <div class="pack-actions flex items-center gap-3">
                                <a href="{{ route('packs.edit', $pack) }}" class="text-sm font-medium text-[#b34c6d]">Editar</a>
                                <form action="{{ route('packs.destroy', $pack) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-rose-600" onclick="return confirm('¿Eliminar este pack?')">Eliminar</button>
                                </form>
                            </div>
                        </div>

                        @if ($canSell)
                            <p class="mt-4 text-sm font-medium text-emerald-700">Toca esta tarjeta para vender este pack automáticamente.</p>
                            <form id="sell-pack-{{ $pack->id }}" action="{{ route('packs.sell', $pack) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        @elseif (!$pack->estado)
                            <button type="button" disabled class="mt-5 w-full cursor-not-allowed rounded-xl bg-zinc-200 px-5 py-4 text-base font-semibold text-zinc-500">Pack inactivo</button>
                        @else
                            <p class="mt-5 rounded-xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-center text-sm text-zinc-600">Este pack aún no tiene productos vinculados.</p>
                        @endif
                    </article>
                @empty
                    <div class="rounded-2xl bg-white p-10 text-center text-zinc-500 md:col-span-2">Aún no hay packs registrados.</div>
                @endforelse
            </div>
            <div class="mt-5">{{ $packs->links() }}</div>
        </div>
    </div>
</x-layouts.app>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.pack-card[data-sell-form]').forEach(card => {
        card.addEventListener('click', event => {
            if (event.target.closest('.pack-actions')) return;

            const formId = card.dataset.sellForm;
            const form = document.getElementById(formId);
            if (!form) return;

            const packName = card.dataset.packName;
            const packPrice = card.dataset.packPrice;
            const shouldSell = window.confirm(`¿Agregar a venta el pack ${packName} por Q${packPrice}?`);
            if (shouldSell) form.requestSubmit();
        });
    });
});
</script>
