<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InterviewParticipant extends Model
{
    protected $table = 'interview_participants';

    protected $fillable = [
        'interview_id',
        'user_id',
        'joined_at',
        'left_at',
        'role'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function interview()
    {
        return $this->belongsTo(Interview::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isOnline()
    {
        return $this->joined_at && !$this->left_at;
    }

    public function duration()
    {
        if (!$this->joined_at) return 0;
        if (!$this->left_at) return now()->diffInSeconds($this->joined_at);

        return $this->left_at->diffInSeconds($this->joined_at);
    }
}
