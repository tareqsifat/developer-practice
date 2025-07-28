<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gateway',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payload',
        'response',
        'package',
        'points_awarded',
    ];

    protected $casts = [
        'payload' => 'array',
        'response' => 'array',
        'package' => 'array',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccessful()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function markAsCompleted($response = null)
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'response' => $response,
        ]);
    }

    public function markAsFailed($response = null)
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'response' => $response,
        ]);
    }
}
