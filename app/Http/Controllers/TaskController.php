<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = auth()->user()->tasks()->with('category');

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->inCategory($request->category);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->completed($request->status);
        }

        // Get paginated tasks
        $tasks = $query->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        // Get user's categories
        $categories = auth()->user()->categories()->get();

        return Inertia::render('TaskManager', [
            'tasks' => $tasks,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status']),
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
                'info' => session('info'),
                'warning' => session('warning'),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255|min:1',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        // Verify category belongs to user if provided
        if ($request->category_id) {
            $category = Category::where('id', $request->category_id)
                               ->where('user_id', auth()->id())
                               ->firstOrFail();
        }

        auth()->user()->tasks()->create([
            'title' => trim($request->title),
            'category_id' => $request->category_id,
            'completed' => false
        ]);

        return redirect()->back()->with('success', 'Task created successfully!');
    }

    public function update(Request $request, Task $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'title' => 'required|string|max:255|min:1',
            'category_id' => 'nullable|exists:categories,id'
        ]);

        // Verify category belongs to user if provided
        if ($request->category_id) {
            $category = Category::where('id', $request->category_id)
                               ->where('user_id', auth()->id())
                               ->firstOrFail();
        }

        $task->update([
            'title' => trim($request->title),
            'category_id' => $request->category_id
        ]);

        return redirect()->back()->with('success', 'Task updated successfully!');
    }

    public function toggle(Task $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->update([
            'completed' => !$task->completed
        ]);

        $status = $task->completed ? 'completed' : 'reopened';
        return redirect()->back()->with('success', "Task {$status}!");
    }

    public function destroy(Task $task)
    {
        // Ensure user owns this task
        if ($task->user_id !== auth()->id()) {
            abort(403);
        }

        $task->delete();

        return redirect()->back()->with('success', 'Task deleted successfully!');
    }
}