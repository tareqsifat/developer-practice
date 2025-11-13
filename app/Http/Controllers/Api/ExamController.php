<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AdminExamService;
use App\Http\Requests\StoreExamRequest;
use App\Http\Requests\UpdateExamRequest;

class ExamController extends Controller
{
    protected $examService;

    public function __construct(AdminExamService $examService)
    {
        $this->examService = $examService;
    }

    public function index()
    {
        $result = $this->examService->getAll();
        return response()->json($result);
    }

    public function show($id)
    {
        $result = $this->examService->getById($id);
        return response()->json($result, $result['success'] ? 200 : 404);
    }

    public function store(StoreExamRequest $request)
    {
        $result = $this->examService->create($request->validated());
        return response()->json($result, $result['success'] ? 201 : 400);
    }

    public function update(UpdateExamRequest $request, $id)
    {
        $result = $this->examService->update($id, $request->validated());
        return response()->json($result, $result['success'] ? 200 : 400);
    }

    public function destroy($id)
    {
        $result = $this->examService->delete($id);
        return response()->json($result, $result['success'] ? 204 : 404);
    }
}
