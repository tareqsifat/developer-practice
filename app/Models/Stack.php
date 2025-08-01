<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stack extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'icon',
        'color',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function subjects()
    {
        return $this->hasMany(Subject::class, 'stack_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'stack_id');
    }

    public function exams()
    {
        return $this->hasMany(Exam::class, 'stack_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('storage/stack-icons/' . $this->icon);
        }
        
        return null;
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions()->where('is_approved', true)->count();
    }

    public function getExamsCountAttribute()
    {
        return $this->exams()->where('is_active', true)->count();
    }

    // Helper Methods
    public function getPopularityScore()
    {
        // Calculate based on number of exam attempts
        return $this->exams()
                   ->withCount('attempts')
                   ->get()
                   ->sum('attempts_count');
    }

    public function getDifficultyDistribution()
    {
        return $this->questions()
                   ->where('is_approved', true)
                   ->selectRaw('difficulty, COUNT(*) as count')
                   ->groupBy('difficulty')
                   ->pluck('count', 'difficulty')
                   ->toArray();
    }
}

