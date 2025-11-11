<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTopicRequest;
use App\Http\Requests\UpdateTopicRequest;
use App\Services\TopicService;

class TopicController extends Controller
{
    protected $topicService;

    public function __construct(TopicService $topicService)
    {
        $this->topicService = $topicService;
    }

    public function index()
    {
        $result = $this->topicService->getAll();
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->topicService->getById($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function store(StoreTopicRequest $request)
    {
        $result = $this->topicService->create($request->validated());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    public function update(UpdateTopicRequest $request, $id)
    {
        $result = $this->topicService->update($id, $request->validated());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function destroy($id)
    {
        $result = $this->topicService->delete($id);
        return response()->json($result, $result['success'] ? 204 : 404);
    }
}
