<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MarketplaceController extends Controller
{

    public function index(Request $request)
    {
        $query = Marketplace::withCount('transactionMarketplaces');

        if ($request->filled('search')) {

            $query->where(function ($q) use ($request) {

                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }

        $marketplaces = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('pages.marketplaces.index', compact('marketplaces'));
    }

    public function create()
    {
        return view('pages.marketplaces.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:100'],
            'code' => ['required', 'max:50', 'alpha_dash', 'unique:marketplaces,code'],
            'logo' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        Marketplace::create($validated);

        return redirect()
            ->route('marketplace.index')
            ->with('success', 'Marketplace berhasil ditambahkan.');
    }

    public function show(Marketplace $marketplace)
    {
        return redirect()->route('marketplaces.edit', $marketplace);
    }

    public function edit(Marketplace $marketplace)
    {
        return view('pages.marketplaces.edit', compact('marketplace'));
    }

    public function update(Request $request, Marketplace $marketplace)
    {
        $validated = $request->validate([
            'name' => ['required', 'max:100'],
            'code' => [
                'required',
                'max:50',
                'alpha_dash',
                Rule::unique('marketplaces', 'code')->ignore($marketplace->id),
            ],
            'logo' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['required', 'boolean'],
        ]);

        $marketplace->update($validated);

        return redirect()
            ->route('marketplace.index')
            ->with('success', 'Marketplace berhasil diperbarui.');
    }

    public function destroy(Marketplace $marketplace)
    {
        if ($marketplace->transactionMarketplaces()->exists()) {
            return back()->with('error', 'Marketplace sudah digunakan pada transaksi.');
        }

        $marketplace->delete();

        return back()->with('success', 'Marketplace berhasil dihapus.');
    }
}
