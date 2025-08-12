<?php

namespace App\Services;

use App\Events\SendEmailEvent;
use App\Models\User;
use App\Models\VerificationOtp;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class AuthService extends BaseService
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    /**
     * Register a new user
     */
    public function register(array $userData): array
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
            'role' => $userData['role'] ?? 'user',
            'is_active' => true,
        ]);

        // Create user profile
        $user->profile()->create([
            'bio' => '',
            'skills' => [],
            'experience_level' => 'beginner',
            'github_username' => $userData['github_username'] ?? null,
            'linkedin_url' => $userData['linkedin_url'] ?? null,
            'website_url' => $userData['website_url'] ?? null,
        ]);

        // Assign default role
        // $user->assignRole($user->role);
        $tokenName = 'DevPractice-' . $user->id . '-' . time();
        $verification_otp = (new OtpService)->generateOtp($user, VerificationOtp::EMAIL_VERIFICATION);
        event(new SendEmailEvent($user, $verification_otp));
        return [
            'user' => $user->load('profile'),
            'token' => $token = $user->createToken($tokenName),
            'expires_at' => $tokenResult['expires_at'],
        ];
    }

    /**
     * Login user
     */
    public function login(array $credentials): ?array
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return null;
        }

        if (!$user->is_active) {
            throw new \Exception('Account is deactivated');
        }

        $tokenResult = $this->createToken($user);

        // Update last login
        $user->update(['last_login_at' => now()]);

        return [
            'user' => $user->load('profile'),
            'token' => $tokenResult['token'],
            'expires_at' => $tokenResult['expires_at'],
        ];
    }

    /**
     * Create access token for user
     */
    public function createToken(User $user): array
    {
        $tokenName = 'DevPractice-' . $user->id . '-' . time();

        // Define scopes based on user role
        $scopes = ['user'];
        if ($user->role === 'admin') {
            $scopes[] = 'admin';
        }
        if (in_array($user->role, ['admin', 'contributor'])) {
            $scopes[] = 'contributor';
        }


        return [
            'token' => $token->accessToken,
            'expires_at' => $token->token->expires_at,
        ];
    }

    /**
     * Verify email address
     */
    public function verifyEmail(string $token): bool
    {
        // In a real implementation, you would store verification tokens
        // and validate them here. For simplicity, we'll just mark as verified.

        $user = User::where('email_verification_token', $token)->first();

        if (!$user) {
            return false;
        }

        $user->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        return true;
    }

    /**
     * Send password reset link
     */
    public function sendPasswordResetLink(string $email): void
    {
        $status = Password::sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            throw new \Exception('Failed to send password reset link');
        }
    }

    /**
     * Reset password
     */
    public function resetPassword(string $token, string $email, string $password): bool
    {
        $status = Password::reset([
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
            'token' => $token,
        ], function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();
        });

        return $status === Password::PASSWORD_RESET;
    }

    /**
     * Change user password
     */
    public function changePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return true;
    }

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $profileData): User
    {
        // Update user basic info
        $userFields = array_intersect_key($profileData, array_flip([
            'name', 'bio', 'github_username', 'linkedin_url', 'website_url'
        ]));

        if (!empty($userFields)) {
            $user->update($userFields);
        }

        // Update profile
        $profileFields = array_intersect_key($profileData, array_flip([
            'skills', 'experience_level', 'location', 'timezone', 'preferences'
        ]));

        if (!empty($profileFields)) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                $profileFields
            );
        }

        return $user->fresh(['profile']);
    }

    /**
     * Upload user avatar
     */
    public function uploadAvatar(User $user, $file): string
    {
        $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('avatars', $filename, 'public');

        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete('avatars/' . $user->avatar);
        }

        $user->update(['avatar' => $filename]);

        return $user->avatar_url;
    }

    /**
     * Deactivate user account
     */
    public function deactivateAccount(User $user): bool
    {
        // Revoke all tokens
        $user->tokens()->delete();

        // Deactivate account
        $user->update(['is_active' => false]);

        return true;
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(User $user): array
    {
        return [
            'total_exams_taken' => $user->examAttempts()->count(),
            'completed_exams' => $user->examAttempts()->where('status', 'completed')->count(),
            'average_score' => $user->examAttempts()
                                  ->where('status', 'completed')
                                  ->avg('percentage_score') ?? 0,
            'questions_contributed' => $user->questions()->count(),
            'approved_questions' => $user->questions()->where('is_approved', true)->count(),
            'feedback_given' => $user->feedback()->count(),
            'testimonials_submitted' => $user->testimonials()->count(),
            'interview_requests' => $user->interviewRequests()->count(),
            'interviews_participated' => $user->interviewParticipations()->count(),
        ];
    }
}

