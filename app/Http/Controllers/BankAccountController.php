<?php
namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        return view('pages.bank-accounts.index');
    }

    public function create()
    {
        return view('pages.bank-accounts.create', [
            'account' => new BankAccount(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        BankAccount::create($validated);

        return redirect()->route('rekening.index')->with('status', 'created');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('pages.bank-accounts.edit', [
            'rekening' => $bankAccount,
        ]);
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank_name' => 'required|string|max:100',
            'account_number' => 'required|string|max:50',
            'account_holder' => 'required|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('rekening.index')->with('status', 'updated');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('rekening.index')->with('status', 'deleted');
    }
}

