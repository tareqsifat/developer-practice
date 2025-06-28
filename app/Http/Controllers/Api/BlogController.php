<?php

// app/Http/Controllers/Api/BlogController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogComment;
use App\Services\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    protected $blogService;

    public function __construct(BlogService $blogService)
    {
        $this->blogService = $blogService;
    }

    public function index(Request $request)
    {
        $posts = BlogPost::with('user', 'categories', 'tags')
            ->published()
            ->filter($request)
            ->paginate(10);

        return response()->json($posts);
    }

    public function show($slug)
    {
        $post = BlogPost::with('user', 'categories', 'tags', 'comments.user')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        // Increment views
        $post->increment('views');

        return response()->json($post);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $post = $this->blogService->createPost($request->user(), $data);
        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
            'excerpt' => 'nullable|string',
            'categories' => 'nullable|array',
            'tags' => 'nullable|array',
        ]);

        $post = $this->blogService->updatePost($id, $data);
        return response()->json($post);
    }

    public function addComment(Request $request, $postId)
    {
        $data = $request->validate([
            'content' => 'required|string',
            'parent_id' => 'nullable|exists:blog_comments,id'
        ]);

        $comment = $this->blogService->addComment(
            $request->user(),
            $postId,
            $data['content'],
            $data['parent_id'] ?? null
        );

        return response()->json($comment, 201);
    }

    public function toggleLike(Request $request, $postId)
    {
        $result = $this->blogService->toggleLike($request->user(), $postId);
        return response()->json(['liked' => $result]);
    }
}

