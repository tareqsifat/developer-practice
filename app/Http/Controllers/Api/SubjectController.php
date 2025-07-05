<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Models\Subject;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::with('stack')->get();
    }

    public function show($id)
    {
        return Subject::with('stack')->findOrFail($id);
    }

    // Admin methods
    public function store(StoreSubjectRequest $request)
    {
        $subject = Subject::create($request->validated());
        return response()->json($subject, 201);
    }

    public function update(UpdateSubjectRequest $request, $id)
    {
        $subject = Subject::findOrFail($id);
        $subject->update($request->validated());
        return $subject;
    }

    public function destroy($id)
    {
        Subject::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
