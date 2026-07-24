<x-layouts.app :title="__('Registrar compra')">
    <div class="min-h-full bg-[#fffafc] p-4 sm:p-8 dark:bg-zinc-900">
        <div class="mx-auto max-w-3xl">
            <h1 class="mb-2 text-3xl font-semibold text-zinc-900 dark:text-white">Registrar compra</h1>
            <p class="mb-6 text-sm text-zinc-500">El stock aumentará automáticamente al guardar.</p><x-flash />
            <form action="{{ route('compras.store') }}" method="POST"
                class="rounded-2xl bg-white p-6 shadow-sm dark:bg-zinc-800">@include('compras._form', ['submit' => 'Guardar compra'])</form>
        </div>
    </div>
</x-layouts.app>
