<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function getUserByEmail($email): bool|User
    {
        $user = User::where('email', $email)->first();
        if(!empty($user)){
            return $user;
        } else {
            return false;
        }
    }

    public function changePassword(User $user, string $password): bool
    {
        return $user->update([
            'password' => Hash::make($password)
        ]);
    }
}
