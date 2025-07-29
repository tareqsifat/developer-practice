<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'points',
        'type',
        'source',
        'source_id',
        'balance_before',
        'balance_after',
        'description',
    ];

    const TYPE_CREDIT = 'credit';
    const TYPE_DEBIT = 'debit';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sourceModel()
    {
        // Dynamically resolve source model
        if ($this->source && class_exists($this->source)) {
            return $this->belongsTo($this->source, 'source_id');
        }

        return null;
    }

    public static function createTransaction(User $user, $points, $type, $source, $sourceId = null)
    {
        $balanceBefore = $user->points_balance;
        $balanceAfter = $type === self::TYPE_CREDIT
            ? $balanceBefore + $points
            : $balanceBefore - $points;

        return self::create([
            'user_id' => $user->id,
            'points' => $points,
            'type' => $type,
            'source' => $source,
            'source_id' => $sourceId,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ]);
    }
}
