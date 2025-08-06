<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Question;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    public function approve(User $user)
    {
        return $user->hasRole('admin') || $user->hasRole('moderator');
    }
}
