<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\StackController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TopicController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ExamAttemptController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\TestimonialController;
use App\Http\Controllers\Api\InterviewController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\PaymentWebhookController;
use App\Http\Controllers\Api\PointController;
use App\Http\Controllers\Api\StreakController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail']);
});

// Public content routes
Route::get('stacks', [StackController::class, 'index']);
Route::get('stacks/{id}', [StackController::class, 'show']);
Route::get('testimonials', [TestimonialController::class, 'index']);
Route::get('testimonials/featured', [TestimonialController::class, 'featured']);

// Protected routes
Route::middleware('auth:api')->group(function () {

    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('user', [AuthController::class, 'user']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });

    // User management
    Route::prefix('user')->group(function () {
        Route::get('profile', [UserController::class, 'profile']);
        Route::put('profile', [UserController::class, 'updateProfile']);
        Route::post('avatar', [UserController::class, 'uploadAvatar']);
        Route::post('change-password', [UserController::class, 'changePassword']);
        Route::delete('account', [UserController::class, 'deleteAccount']);
    });

    // Dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);
    Route::get('dashboard/statistics', [DashboardController::class, 'statistics']);
    Route::get('dashboard/recent-activity', [DashboardController::class, 'recentActivity']);
    Route::get('dashboard/recommendations', [DashboardController::class, 'recommendations']);

    // Stacks, Subjects, Topics
    Route::get('subjects', [SubjectController::class, 'index']);
    Route::get('subjects/{id}', [SubjectController::class, 'show']);
    Route::get('topics', [TopicController::class, 'index']);
    Route::get('topics/{id}', [TopicController::class, 'show']);

    // Questions
    Route::apiResource('questions', QuestionController::class);
    Route::post('questions/{id}/approve', [QuestionController::class, 'approve'])->middleware('can:approve-questions');
    Route::post('questions/{id}/reject', [QuestionController::class, 'reject'])->middleware('can:approve-questions');

    // Exams
    Route::get('exams', [ExamController::class, 'index']);
    Route::get('exams/{id}', [ExamController::class, 'show']);
    Route::post('exams/{id}/attempts', [ExamAttemptController::class, 'start']);
    Route::get('exam-attempts/{id}', [ExamAttemptController::class, 'show']);
    Route::post('exam-attempts/{id}/submit', [ExamAttemptController::class, 'submit']);
    Route::get('exam-attempts/{id}/results', [ExamAttemptController::class, 'results']);
    Route::get('my-exam-attempts', [ExamAttemptController::class, 'userAttempts']);

    // Feedback
    Route::post('question-feedback', [FeedbackController::class, 'storeQuestionFeedback']);
    Route::post('exam-feedback', [FeedbackController::class, 'storeExamFeedback']);
    Route::post('general-feedback', [FeedbackController::class, 'storeGeneralFeedback']);
    Route::get('my-feedback', [FeedbackController::class, 'userFeedback']);

    // Testimonials
    Route::post('testimonials', [TestimonialController::class, 'store']);
    Route::get('my-testimonials', [TestimonialController::class, 'userTestimonials']);

    // Live Coding Interviews
    Route::prefix('interviews')->group(function () {
        Route::get('/', [InterviewController::class, 'index']);
        Route::post('request', [InterviewController::class, 'requestInterview']);
        Route::get('my-requests', [InterviewController::class, 'myRequests']);
        Route::post('{id}/join', [InterviewController::class, 'joinInterview']);
        Route::post('{id}/leave', [InterviewController::class, 'leaveInterview']);
        Route::get('{id}/participants', [InterviewController::class, 'participants']);
        Route::post('{id}/calendar', [InterviewController::class, 'addToCalendar']);
    });

    // Admin routes
    Route::middleware('can:access-admin')->prefix('admin')->group(function () {

        // Stack management
        Route::apiResource('stacks', StackController::class)->except(['index', 'show']);

        // Subject management
        Route::apiResource('subjects', SubjectController::class)->except(['index', 'show']);

        // Topic management
        Route::apiResource('topics', TopicController::class)->except(['index', 'show']);

        // Exam management
        Route::apiResource('exams', ExamController::class)->except(['index', 'show']);

        // User management
        Route::get('users', [UserController::class, 'index']);
        Route::get('users/{id}', [UserController::class, 'show']);
        Route::put('users/{id}', [UserController::class, 'update']);
        Route::delete('users/{id}', [UserController::class, 'destroy']);
        Route::post('users/{id}/activate', [UserController::class, 'activate']);
        Route::post('users/{id}/deactivate', [UserController::class, 'deactivate']);

        // Question management
        Route::get('questions/pending', [QuestionController::class, 'pending']);
        Route::get('questions/rejected', [QuestionController::class, 'rejected']);

        // Feedback management
        Route::get('feedback', [FeedbackController::class, 'index']);
        Route::put('feedback/{id}/status', [FeedbackController::class, 'updateStatus']);

        // Testimonial management
        Route::get('testimonials/all', [TestimonialController::class, 'adminIndex']);
        Route::post('testimonials/{id}/approve', [TestimonialController::class, 'approve']);
        Route::post('testimonials/{id}/feature', [TestimonialController::class, 'feature']);
        Route::delete('testimonials/{id}', [TestimonialController::class, 'destroy']);

        // Analytics
        Route::get('analytics/overview', [DashboardController::class, 'adminOverview']);
        Route::get('analytics/users', [DashboardController::class, 'userAnalytics']);
        Route::get('analytics/exams', [DashboardController::class, 'examAnalytics']);
        Route::get('analytics/questions', [DashboardController::class, 'questionAnalytics']);
    });
});
Route::apiResource('blog', BlogController::class)->only(['index', 'show']);
Route::middleware('auth:api')->group(function () {
    Route::apiResource('blog', BlogController::class)->except(['index', 'show']);
    Route::post('blog/{post}/comments', [BlogController::class, 'addComment']);
    Route::post('blog/{post}/like', [BlogController::class, 'toggleLike']);
});
Route::middleware('auth:api')->group(function () {
    Route::get('points', [PointController::class, 'index']);
    Route::post('points/purchase', [PointController::class, 'purchasePoints']);
    Route::post('points/reward', [PointController::class, 'rewardPoints'])->middleware('can:reward-points');
});
Route::middleware('auth:api')->group(function () {
    Route::get('streaks', [StreakController::class, 'index']);
    Route::get('streaks/current', [StreakController::class, 'current']);
});
// Add to routes/api.php
Route::prefix('webhooks')->group(function () {
    Route::post('payment/{gateway}', [PaymentWebhookController::class, 'handle']);
});

// Health check
Route::get('health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

