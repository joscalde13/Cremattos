<x-layouts.app :title="__('Compras')">
    <div class="min-h-full bg-[#fffafc] p-4 sm:p-8 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl">
            <x-flash />

            <div class="mb-6 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <div>
                    <p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-700">Inversión</p>
                    <h1 class="mt-1 text-3xl font-semibold text-zinc-900 dark:text-white">Compras</h1>
                </div>

                <a
                    href="{{ route('compras.create') }}"
                    class="rounded-lg bg-emerald-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-800"
                >
                    Registrar compra
                </a>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm dark:bg-zinc-800">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="border-b bg-zinc-50 text-xs uppercase text-zinc-500 dark:bg-zinc-900">
                            <tr>
                                <th class="px-5 py-4">Fecha</th>
                                <th class="px-5 py-4">Producto</th>
                                <th class="px-5 py-4">Cantidad comprada</th>
                                <th class="px-5 py-4 text-right">Total</th>
                                <th class="px-5 py-4 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($compras as $compra)
                                <tr>
                                    <td class="px-5 py-4">{{ $compra->fecha->format('d/m/Y') }}</td>
                                    <td class="px-5 py-4">{{ $compra->producto?->nombre ?? 'Producto eliminado' }}</td>
                                    <td class="px-5 py-4">{{ (int) $compra->cantidad }}</td>
                                    <td class="px-5 py-4 text-right font-semibold">Q{{ number_format($compra->total, 2) }}</td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('compras.edit', $compra) }}" class="text-emerald-700">Editar</a>

                                        <form action="{{ route('compras.destroy', $compra) }}" method="POST" class="ml-3 inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-rose-600" onclick="return confirm('¿Eliminar esta compra?')">Eliminar</button>
                                        </form>

                                        @if ($compra->producto_id)
                                            <div class="mt-2">
                                                <flux:modal.trigger name="agregar-cantidad-{{ $compra->id }}">
                                                    <flux:button variant="subtle" size="sm" class="border border-emerald-500 text-emerald-700 hover:border-emerald-600 hover:text-emerald-800 dark:border-emerald-700 dark:text-emerald-300" x-data="" x-on:click.prevent="$dispatch('open-modal', 'agregar-cantidad-{{ $compra->id }}')">
                                                        Agregar más cantidad
                                                    </flux:button>
                                                </flux:modal.trigger>

                                                <flux:modal name="agregar-cantidad-{{ $compra->id }}" class="max-w-md" focusable :closable="false">
                                                    <form action="{{ route('compras.agregar-cantidad', $compra) }}" method="POST" class="space-y-5">
                                                        @csrf

                                                        <div>
                                                            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Agregar más cantidad</h3>
                                                            <p class="text-sm text-zinc-500 dark:text-zinc-400">Actualiza esta compra sin crear otro registro.</p>
                                                        </div>

                                                        <div class="rounded-xl border border-emerald-200 bg-emerald-50/70 p-4 dark:border-emerald-900/60 dark:bg-emerald-950/30">
                                                            <div class="mt-3 space-y-1 text-sm text-zinc-700 dark:text-zinc-200">
                                                                <p><span class="font-semibold">Producto:</span> {{ $compra->producto?->nombre ?? 'Producto' }}</p>
                                                                <p><span class="font-semibold">Cantidad actual:</span> {{ (int) $compra->cantidad }}</p>
                                                            </div>
                                                        </div>

                                                        <div>
                                                            <label for="modal_cantidad_{{ $compra->id }}" class="mb-1 block text-sm font-medium text-zinc-700 dark:text-zinc-200">Cantidad a agregar</label>
                                                            <input
                                                                id="modal_cantidad_{{ $compra->id }}"
                                                                name="cantidad"
                                                                type="number"
                                                                min="1"
                                                                value="1"
                                                                required
                                                                class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm focus:border-emerald-600 focus:outline-none dark:border-zinc-600 dark:bg-zinc-900 dark:text-zinc-100"
                                                            >
                                                        </div>

                                                        <div class="flex justify-end gap-2">
                                                            <flux:modal.close>
                                                                <button type="button" class="rounded-lg border border-zinc-300 px-3 py-2 text-sm font-medium text-zinc-700 hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-200 dark:hover:bg-zinc-700">
                                                                    Cancelar
                                                                </button>
                                                            </flux:modal.close>

                                                            <button
                                                                type="submit"
                                                                class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800"
                                                            >
                                                                Guardar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </flux:modal>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-zinc-500">Aún no hay compras registradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-5">{{ $compras->links() }}</div>
        </div>

    </div>
</x-layouts.app>
