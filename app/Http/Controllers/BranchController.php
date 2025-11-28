<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends BaseController
{
    public function index()
    {
        $branches = Branch::all();
        return view('pages.branches.index', compact('branches'));
    }

    public function create()
    {
        return view('pages.branches.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:branches,code',
            'name' => 'required',
        ]);

        Branch::create($request->all());
        return redirect()->route('cabang.index')->with('status', 'saved');
    }

    public function edit(Branch $branch)
    {
        return view('pages.branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required',
        ]);

        $branch->update($request->all());
        return redirect()->route('cabang.index')->with('status', 'updated');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('cabang.index')->with('status', 'deleted');
    }
}
