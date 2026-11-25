<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('events')->orderBy('name')->get();

        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => ['required', 'string', 'max:255', 'unique:categories,name']]);
        Category::create($data);

        return back()->with('success', 'Category added.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('categories', 'name')->ignore($category)],
        ]);
        $category->update($data);

        return back()->with('success', 'Category renamed.');
    }

    public function destroy(Category $category)
    {
        if ($category->events()->exists()) {
            return back()->with('error', 'This category is used by events and cannot be deleted.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted.');
    }
}
