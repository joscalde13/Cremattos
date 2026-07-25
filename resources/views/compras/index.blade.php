<x-layouts.app :title="__('Compras')">
    <div class="min-h-full bg-[#fffafc] p-4 sm:p-8 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl">
            <x-flash />
            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div><p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-700">Inversión</p><h1 class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-white">Compras</h1></div>
                <a href="{{ route('compras.create') }}" class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800">Registrar compra</a>
            </div>
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-zinc-800"><div class="overflow-x-auto"><table class="w-full text-left text-sm"><thead class="border-b bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-900"><tr><th class="px-5 py-4">Fecha</th><th class="px-5 py-4">Producto</th><th class="px-5 py-4">Cantidad</th><th class="px-5 py-4 text-right">Total</th><th class="px-5 py-4 text-right">Acciones</th></tr></thead><tbody class="divide-y">
                @forelse ($compras as $compra)
                    <tr><td class="px-5 py-4">{{ $compra->fecha->format('d/m/Y') }}</td><td class="px-5 py-4">{{ $compra->producto?->nombre ?? 'Producto eliminado' }}</td><td class="px-5 py-4">@php($vendidas = min((int) ($compra->producto?->vendidas_total ?? 0), (int) $compra->cantidad)) @php($restante = max((int) $compra->cantidad - $vendidas, 0)){{ $restante }}</td><td class="px-5 py-4 text-right font-semibold">Q{{ number_format($compra->total, 2) }}</td><td class="px-5 py-4 text-right"><a href="{{ route('compras.edit', $compra) }}" class="text-emerald-700">Editar</a><form action="{{ route('compras.destroy', $compra) }}" method="POST" class="ml-3 inline">@csrf @method('DELETE')<button class="text-rose-600" onclick="return confirm('¿Eliminar esta compra?')">Eliminar</button></form></td></tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-zinc-500">Aún no hay compras registradas.</td></tr>
                @endforelse
            </tbody></table></div></div>
            <div class="mt-5">{{ $compras->links() }}</div>
        </div>
    </div>
</x-layouts.app>
