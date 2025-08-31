<?php

// ============================================
// 5. API CONTROLLERS - Api/TaskController.php
// ============================================
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->tasks()->with('category');

        // Apply filters
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('category')) {
            $query->inCategory($request->category);
        }

        if ($request->filled('status')) {
            $query->completed($request->status);
        }

        $tasks = $query->orderBy('created_at', 'desc')->paginate(10);

        return TaskResource::collection($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|min:1',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        $task = $request->user()->tasks()->create([
            'title' => trim($request->title),
            'category_id' => $request->category_id,
            'completed' => false
        ]);

        return new TaskResource($task->load('category'));
    }

    public function show(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new TaskResource($task->load('category'));
    }

    public function update(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'title' => 'sometimes|required|string|max:255|min:1',
            'category_id' => 'nullable|exists:categories,id',
            'completed' => 'sometimes|boolean'
        ]);

        $task->update($request->only(['title', 'category_id', 'completed']));

        return new TaskResource($task->load('category'));
    }

    public function destroy(Request $request, Task $task)
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task->delete();

        return response()->json(['message' => 'Task deleted successfully'], 200);
    }
}