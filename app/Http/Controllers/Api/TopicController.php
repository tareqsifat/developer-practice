<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Models\Topic;

class TopicController extends Controller
{
    public function index()
    {
        return Topic::with('subject')->get();
    }

    public function show($id)
    {
        return Topic::with('subject')->findOrFail($id);
    }

    // Admin methods
    public function store(StoreTopicRequest $request)
    {
        $topic = Topic::create($request->validated());
        return response()->json($topic, 201);
    }

    public function update(UpdateTopicRequest $request, $id)
    {
        $topic = Topic::findOrFail($id);
        $topic->update($request->validated());
        return $topic;
    }

    public function destroy($id)
    {
        Topic::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
