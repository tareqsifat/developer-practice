<?php
// app/Models/Subject.php (enhanced)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stack_id',
        'name',
        'description',
        'icon',
        'is_active',
        'order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function stack()
    {
        return $this->belongsTo(Stack::class);
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function exams()
    {
        return $this->hasMany(Exam::class);
    }
}

// Migration already exists (000003_create_subjects_table)
