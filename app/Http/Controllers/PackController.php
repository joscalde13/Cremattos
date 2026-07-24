<?php

namespace App\Http\Controllers;

use App\Http\Requests\PackRequest;
use App\Models\Pack;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PackController extends Controller
{
    public function index(Request $request): View
    {
        $packs = Pack::with('productos')->when($request->filled('buscar'), fn ($query) => $query->where('nombre', 'like', '%'.$request->string('buscar').'%'))->latest()->paginate(10)->withQueryString();
        return view('packs.index', compact('packs'));
    }
    public function create(): View { return view('packs.create', ['pack' => new Pack(['estado' => true]), 'productos' => Producto::where('estado', true)->orderBy('nombre')->get()]); }
    public function store(PackRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $pack = Pack::create(collect($data)->except(['producto_id', 'cantidad'])->all());
        $this->syncProducts($pack, $data);
        return to_route('packs.index')->with('success', 'Pack registrado correctamente.');
    }
    public function edit(Pack $pack): View { return view('packs.edit', ['pack' => $pack->load('productos'), 'productos' => Producto::where('estado', true)->orWhereHas('packs', fn ($query) => $query->where('pack_id', $pack->id))->orderBy('nombre')->get()]); }
    public function update(PackRequest $request, Pack $pack): RedirectResponse
    {
        $data = $request->validated();
        $pack->update(collect($data)->except(['producto_id', 'cantidad'])->all());
        $this->syncProducts($pack, $data);
        return to_route('packs.index')->with('success', 'Pack actualizado correctamente.');
    }
    public function destroy(Pack $pack): RedirectResponse { $pack->delete(); return to_route('packs.index')->with('success', 'Pack eliminado correctamente.'); }

    private function syncProducts(Pack $pack, array $data): void
    {
        $products = [];
        foreach ($data['producto_id'] ?? [] as $index => $productId) {
            $products[$productId] = ['cantidad' => $data['cantidad'][$index] ?? 1];
        }
        $pack->productos()->sync($products);
    }
}
