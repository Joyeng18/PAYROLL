<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::orderBy('category_name', 'asc')->get();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_code' => 'required',
            'category_name' => 'required',
        ]);

        Category::create($request->all());

        createActivity('Create Category', 'Category '.$request->category_name.' created successfully.', request()->getClientIp(true));

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'category_code' => 'required',
            'category_name' => 'required',
        ]);

        $category->update($request->all());

        createActivity('Update Category', 'Category '.$category->category_name.' updated successfully.', request()->getClientIp(true), $category, $request);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {

        createActivity('Delete Category', 'Category '.$category->category_name.' deleted successfully.', request()->getClientIp(true));
        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
