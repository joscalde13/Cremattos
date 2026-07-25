<x-layouts.app :title="__('Ventas')">
    <div class="min-h-full bg-[#fffafc] p-4 sm:p-8 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl">
            <x-flash />
            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-[#b34c6d]">Ingresos</p>
                    <h1 class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-white">Ventas</h1>
                </div>
                <a href="{{ route('ventas.create') }}"
                    class="rounded-lg bg-[#e78ca9] px-4 py-2.5 text-sm font-semibold text-white hover:bg-[#d97898]">Registrar
                    venta</a>
            </div>
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-zinc-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-900">
                            <tr>
                                <th class="px-5 py-4">Fecha</th>
                                <th class="px-5 py-4">Cliente</th>
                                <th class="px-5 py-4 text-right">Total</th>
                                <th class="px-5 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($ventas as $venta)
                                <tr>
                                    <td class="px-5 py-4">{{ $venta->fecha->format('d/m/Y') }}</td>
                                    <td class="px-5 py-4">{{ $venta->cliente ?: 'Consumidor final' }}</td>
                                    <td class="px-5 py-4 text-right font-semibold">
                                        Q{{ number_format($venta->total, 2) }}</td>
                                    <td class="px-5 py-4 text-right"><a href="{{ route('ventas.edit', $venta) }}"
                                            class="text-[#b34c6d]">Editar</a>
                                        <form action="{{ route('ventas.destroy', $venta) }}" method="POST"
                                            class="ml-3 inline">@csrf @method('DELETE')<button class="text-rose-600"
                                                onclick="return confirm('¿Eliminar esta venta y restaurar stock?')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-10 text-center text-zinc-500">Aún no hay ventas
                                        registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-5">{{ $ventas->links() }}</div>
        </div>
    </div>
</x-layouts.app>
