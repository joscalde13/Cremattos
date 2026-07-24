<x-layouts.app :title="__('Dashboard')">
    <div class="flex w-full flex-1 flex-col gap-8 bg-[#fffafc] p-4 sm:p-8 dark:bg-zinc-900">
        <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-end">
            <div><p class="text-sm font-medium uppercase tracking-[0.2em] text-emerald-700">Cremattos</p><h1 class="mt-1 text-3xl font-semibold tracking-tight text-zinc-900 dark:text-white">Resumen del negocio</h1><p class="mt-2 text-sm text-zinc-500">Una mirada clara a tus ventas, compras e inventario.</p></div>
            <a href="{{ route('ventas.create') }}" class="inline-flex items-center justify-center rounded-lg bg-[#e78ca9] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#d97898]">Registrar venta</a>
        </div>
        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            @foreach ([['label' => 'Total vendido', 'value' => $totalVendido, 'tone' => 'bg-[#ffe8ef]', 'text' => 'text-[#a63d61]'], ['label' => 'Total invertido', 'value' => $totalInvertido, 'tone' => 'bg-[#e4f5e9]', 'text' => 'text-[#28704b]'], ['label' => 'Ganancia total', 'value' => $gananciaTotal, 'tone' => 'bg-[#fff2ce]', 'text' => 'text-[#986b15]']] as $card)
                <div class="rounded-2xl border border-white bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-800"><div class="flex items-center justify-between"><p class="text-sm text-zinc-500">{{ $card['label'] }}</p><span class="h-2.5 w-2.5 rounded-full {{ $card['tone'] }}"></span></div><p class="mt-4 text-2xl font-semibold {{ $card['text'] }}">Q{{ number_format($card['value'], 2) }}</p></div>
            @endforeach
            @foreach ([['label' => 'Total de ventas', 'value' => $totalVentas], ['label' => 'Total de compras', 'value' => $totalCompras], ['label' => 'Productos registrados', 'value' => $productosRegistrados]] as $card)
                <div class="rounded-2xl border border-white bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-800"><p class="text-sm text-zinc-500">{{ $card['label'] }}</p><p class="mt-4 text-2xl font-semibold text-zinc-900 dark:text-white">{{ number_format($card['value']) }}</p></div>
            @endforeach
        </div>
        
        
    </div>
</x-layouts.app>
