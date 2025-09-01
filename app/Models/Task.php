<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'completed',
        'user_id',
        'category_id' // Added category_id
    ];

    protected $casts = [
        'completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Scope for searching
    public function scopeSearch($query, $search)
    {
        return $query->where('title', 'like', '%' . $search . '%');
    }

    // Scope for filtering by category
    public function scopeInCategory($query, $categoryId)
    {
        return $query->when($categoryId, function ($q) use ($categoryId) {
            return $q->where('category_id', $categoryId);
        });
    }

    // Scope for filtering by completion status
    public function scopeCompleted($query, $status)
    {
        if ($status === 'completed') {
            return $query->where('completed', true);
        } elseif ($status === 'pending') {
            return $query->where('completed', false);
        }
        return $query;
    }
}