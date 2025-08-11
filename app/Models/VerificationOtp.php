<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class VerificationOtp extends Model
{


    public const EMAIL_VERIFICATION = 'email_verification';
    public const PHONE_VERIFICATION= 'phone_verification';
    public const PASSWORD_RESET = 'password_reset';

    public const VERIFICATION_TYPE = [
        1 => self::PHONE_VERIFICATION,
        2 => self::EMAIL_VERIFICATION,
        3 => self::PASSWORD_RESET
    ];

    // When getting VERIFICATION_TYPE from DB → convert number to name
    public function getVerificationTypeAttribute($value)
    {
        return self::VERIFICATION_TYPE[$value] ?? null;
    }

    // When setting VERIFICATION_TYPE → allow passing constant and store as number
    public function setVerificationTypeAttribute($value)
    {
        if (is_string($value)) {
            $value = array_search(strtoupper($value), self::VERIFICATION_TYPE);
        }
        $this->attributes['verification_type'] = $value;
    }
    protected $fillable = [
        'otp',
        'verification_type',
        'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime'
    ];

    public function verifiable(): MorphTo
    {
        return $this->morphTo();
    }
}
