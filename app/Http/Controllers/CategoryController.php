<?php

// ============================================
// 4. CATEGORY CONTROLLER - CategoryController.php
// ============================================
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|min:1',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        auth()->user()->categories()->create([
            'name' => trim($request->name),
            'color' => $request->color
        ]);

        return redirect()->back()->with('success', 'Category created successfully!');
    }

    public function update(Request $request, Category $category)
    {
        // Ensure user owns this category
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:50|min:1',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $category->update([
            'name' => trim($request->name),
            'color' => $request->color
        ]);

        return redirect()->back()->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        // Ensure user owns this category
        if ($category->user_id !== auth()->id()) {
            abort(403);
        }

        // Set category_id to null for all tasks in this category
        $category->tasks()->update(['category_id' => null]);
        
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully!');
    }
}