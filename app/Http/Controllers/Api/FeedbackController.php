<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFeedbackRequest;
use App\Models\UserFeedback;

class FeedbackController extends Controller
{
    public function storeQuestionFeedback(StoreFeedbackRequest $request)
    {
        $feedback = UserFeedback::create([
            'user_id' => $request->user()->id,
            'type' => 'question',
            'feedbackable_id' => $request->question_id,
            'feedbackable_type' => 'App\Models\Question',
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending'
        ]);

        return response()->json($feedback, 201);
    }

    public function storeExamFeedback(StoreFeedbackRequest $request)
    {
        $feedback = UserFeedback::create([
            'user_id' => $request->user()->id,
            'type' => 'exam',
            'feedbackable_id' => $request->exam_id,
            'feedbackable_type' => 'App\Models\Exam',
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending'
        ]);

        return response()->json($feedback, 201);
    }

    public function storeGeneralFeedback(StoreFeedbackRequest $request)
    {
        $feedback = UserFeedback::create([
            'user_id' => $request->user()->id,
            'type' => 'general',
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending'
        ]);

        return response()->json($feedback, 201);
    }

    public function userFeedback(Request $request)
    {
        return $request->user()->feedback()->paginate();
    }

    // Admin methods
    public function index()
    {
        return UserFeedback::paginate();
    }

    public function updateStatus(Request $request, $id)
    {
        $feedback = UserFeedback::findOrFail($id);
        $feedback->update(['status' => $request->status]);
        return $feedback;
    }
}
