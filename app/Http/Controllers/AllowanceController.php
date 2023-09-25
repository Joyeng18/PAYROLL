<?php

namespace App\Http\Controllers;

use App\Models\Allowance;
use App\Models\Category;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    public function index()
    {
        $allowances = Allowance::all();
        $categories = Category::all();
        return view('allowances.index', compact('allowances','categories'));
    }

    public function create()
    {
        return view('allowances.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'allowance_code' => 'required',
            'allowance_name' => 'required',
            'allowance_amount' => 'required',
            'allowance_range' => 'required',
            'category_id' => 'required',
        ]);

        Allowance::create($request->all());

        return redirect()->route('allowances.index')->with('success', 'Allowance created successfully.');
    }

    public function edit(Allowance $allowance)
    {
        return view('allowances.edit', compact('allowance'));
    }

    public function update(Request $request, Allowance $allowance)
    {
        $request->validate([
            'allowance_code' => 'required',
            'allowance_name' => 'required',
            'allowance_amount' => 'required',
        ]);

        $allowance->update($request->all());

        return redirect()->route('allowances.index')->with('success', 'Allowance updated successfully.');
    }

    public function destroy(Allowance $allowance)
    {
        $allowance->delete();

        return redirect()->route('allowances.index')->with('success', 'Allowance deleted successfully.');
    }
}
