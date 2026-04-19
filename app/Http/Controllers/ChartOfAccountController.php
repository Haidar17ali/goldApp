<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ChartOfAccountController extends BaseController
{

    public function index()
    {
        $accounts = ChartOfAccount::whereNull('parent_id')
            ->with('childrenRecursive')
            ->orderBy('code')
            ->get();

        return view('pages.coa.index', compact('accounts'));
    }


    public function children($id)
    {
        $accounts = ChartOfAccount::where('parent_id', $id)
            ->orderBy('code')
            ->get();

        return response()->json($accounts);
    }

    public function create()
    {
        $parents = ChartOfAccount::orderBy('code')->get();

        return view('pages.coa.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:chart_of_accounts,code'
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'category' => [
                'required',
                'in:asset,liability,equity,revenue,expense'
            ],

            'parent_id' => [
                'nullable',
                'exists:chart_of_accounts,id'
            ],

            'is_active' => [
                'required',
                'boolean'
            ],
        ]);

        try {

            DB::beginTransaction();

            /*
        |--------------------------------------------------------------------------
        | Tentukan normal balance otomatis
        |--------------------------------------------------------------------------
        */

            $normalBalance = match ($validated['category']) {
                'asset', 'expense' => 'debit',
                'liability', 'equity', 'revenue' => 'credit',
            };

            /*
        |--------------------------------------------------------------------------
        | Simpan COA
        |--------------------------------------------------------------------------
        */

            ChartOfAccount::create([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'category' => $validated['category'],
                'normal_balance' => $normalBalance,
                'parent_id' => $validated['parent_id'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            DB::commit();

            return redirect()
                ->route('coa.index')
                ->with('success', 'Account berhasil dibuat');
        } catch (\Throwable $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat account');
        }
    }

    public function edit($id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $parents = ChartOfAccount::where('id', '!=', $id)
            ->orderBy('code')
            ->get();

        return view('pages.coa.edit', compact('account', 'parents'));
    }

    public function update(Request $request, $id)
    {
        $account = ChartOfAccount::findOrFail($id);

        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                'unique:chart_of_accounts,code,' . $account->id
            ],

            'name' => [
                'required',
                'string',
                'max:255'
            ],

            'category' => [
                'required',
                'in:asset,liability,equity,revenue,expense'
            ],

            'parent_id' => [
                'nullable',
                'exists:chart_of_accounts,id'
            ],

            'is_active' => [
                'required',
                'boolean'
            ],
        ]);

        try {

            DB::beginTransaction();

            /*
        |--------------------------------------------------------------------------
        | Cegah akun menjadi parent dirinya sendiri
        |--------------------------------------------------------------------------
        */

            if ($validated['parent_id'] == $account->id) {
                return back()
                    ->withInput()
                    ->with('error', 'Akun tidak boleh menjadi parent dirinya sendiri');
            }

            /*
        |--------------------------------------------------------------------------
        | Tentukan normal balance otomatis
        |--------------------------------------------------------------------------
        */

            $normalBalance = match ($validated['category']) {
                'asset', 'expense' => 'debit',
                'liability', 'equity', 'revenue' => 'credit',
            };

            /*
        |--------------------------------------------------------------------------
        | Update data
        |--------------------------------------------------------------------------
        */

            $account->update([
                'code' => $validated['code'],
                'name' => $validated['name'],
                'category' => $validated['category'],
                'normal_balance' => $normalBalance,
                'parent_id' => $validated['parent_id'] ?? null,
                'is_active' => $validated['is_active'],
            ]);

            DB::commit();

            return redirect()
                ->route('coa.index')
                ->with('success', 'Account berhasil diupdate');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal mengupdate account');
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {

            $account = ChartOfAccount::findOrFail($id);

            if ($account->journalItems()->exists()) {

                return back()->with(
                    'error',
                    'Akun sudah digunakan di transaksi jurnal dan tidak bisa dihapus'
                );
            }


            $this->deleteChildren($account);

            $account->delete();

            DB::commit();

            return redirect()
                ->route('coa.index')
                ->with('success', 'Account berhasil dihapus');
        } catch (\Throwable $e) {

            DB::rollBack();

            return back()->with('error', 'Gagal menghapus account');
        }
    }

    private function deleteChildren($account)
    {
        foreach ($account->childrenRecursive as $child) {

            $this->deleteChildren($child);

            $child->delete();
        }
    }
}
