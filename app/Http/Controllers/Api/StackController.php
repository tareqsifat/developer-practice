<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStackRequest;
use App\Http\Requests\UpdateStackRequest;
use App\Models\Stack;

class StackController extends Controller
{
    public function index()
    {
        return Stack::get();
    }

    public function show($id)
    {
        return Stack::findOrFail($id);
    }

    // Admin methods
    public function store(StoreStackRequest $request)
    {
        $stack = Stack::create($request->validated());
        return response()->json($stack, 201);
    }

    public function update(UpdateStackRequest $request, $id)
    {
        $stack = Stack::findOrFail($id);
        $stack->update($request->validated());
        return $stack;
    }

    public function destroy($id)
    {
        Stack::findOrFail($id)->delete();
        return response()->json(['message' => 'Stack deleted successfully.'], 204);
    }
}
