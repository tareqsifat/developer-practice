<?php
// app/Models/UserFeedback.php (enhanced)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feedbackable_id',
        'feedbackable_type',
        'rating',
        'comment',
        'type',
        'status'
    ];

    const TYPE_QUESTION = 'question';
    const TYPE_EXAM = 'exam';
    const TYPE_GENERAL = 'general';

    const STATUS_PENDING = 'pending';
    const STATUS_REVIEWED = 'reviewed';
    const STATUS_RESOLVED = 'resolved';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedbackable()
    {
        return $this->morphTo();
    }

    public function scopeForQuestion($query)
    {
        return $query->where('feedbackable_type', Question::class);
    }

    public function scopeForExam($query)
    {
        return $query->where('feedbackable_type', Exam::class);
    }
}

