<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Exam;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy
{
    use HandlesAuthorization;

    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Exam $exam)
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Exam $exam)
    {
        return $user->hasRole('admin');
    }
}
