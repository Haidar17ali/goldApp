<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;
use App\Models\GoldManagement;
use App\Models\GoldManagementDetail;
use App\Helpers\StockHelper;
use DB;

class GoldManagementController extends Controller
{
    public function index()
    {
        $managements = GoldManagement::with('details.stock')->latest()->paginate(20);
        return view('pages.gold-management.index', compact('managements'));
    }

    public function create(){
        // Ambil semua karat yang sedang punya stok customer
        $karats = \App\Models\Karat::whereHas('stocks', function ($q) {
            $q->where('type', 'customer')
            ->where('weight', '>', 0);
        })->get();

        // Jenis pengelolaan tetap manual
        $types = [
            'sepuh' => 'Sepuh',
            'patri' => 'Patri',
            'rosok' => 'Rosok',
        ];

        return view('pages.gold-management.create', compact('karats', 'types'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'type' => 'required|in:sepuh,patri,rosok',
            'gold_name' => 'required|string',
            'karat_id' => 'required|integer',
            'stocks' => 'required|array',
            'stocks.*.id' => 'required|integer|exists:stocks,id',
            'stocks.*.gram' => 'required|numeric|min:0.01',
            'total_gram_out' => 'required|numeric|min:0.01',
        ]);

        DB::transaction(function () use ($validated) {
            $management = GoldManagement::create([
                'date' => $validated['date'],
                'type' => $validated['type'],
                'gold_name' => $validated['gold_name'],
                'karat_id' => $validated['karat_id'],
                'total_gram_in' => collect($validated['stocks'])->sum('gram'),
                'total_gram_out' => $validated['total_gram_out'],
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['stocks'] as $stockData) {
                GoldManagementDetail::create([
                    'gold_management_id' => $management->id,
                    'stock_id' => $stockData['id'],
                    'gram' => $stockData['gram'],
                ]);

                // Kurangi stok customer
                StockHelper::moveStock(
                    $stockData['id'],
                    'out',
                    $stockData['gram'],
                    'GoldManagement',
                    $management->id,
                    'Customer stock processed: '.$management->type
                );
            }

            // Tambah stok hasil pengelolaan
            StockHelper::moveStock(
                null,
                'in',
                $validated['total_gram_out'],
                'GoldManagement',
                $management->id,
                'Result of '.$management->type.' process',
                [
                    'gold_name' => $management->gold_name,
                    'karat_id' => $management->karat_id,
                    'type' => $management->type
                ]
            );
        });

        return redirect()->route('pengelolaan-emas.index')->with('status', 'saved');
    }
}
