<?php

// ============================================
// 6. API CATEGORY CONTROLLER - Api/CategoryController.php
// ============================================
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = $request->user()->categories()->withCount('tasks')->get();
        return CategoryResource::collection($categories);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|min:1',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $category = $request->user()->categories()->create([
            'name' => trim($request->name),
            'color' => $request->color
        ]);

        return new CategoryResource($category);
    }

    public function update(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:50|min:1',
            'color' => 'sometimes|required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/'
        ]);

        $category->update($request->only(['name', 'color']));

        return new CategoryResource($category);
    }

    public function destroy(Request $request, Category $category)
    {
        if ($category->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $category->tasks()->update(['category_id' => null]);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}