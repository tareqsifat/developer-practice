<?php

namespace App\Services;

use App\Models\BlogComment;
use App\Models\BlogLike;
use App\Models\BlogPost;
use App\Models\User;

class BlogService extends BaseService
{
    protected function getModelClass(): string
    {
        return BlogPost::class;
    }

    public function createPost(User $user, array $data)
    {
        $post = $this->create(array_merge($data, [
            'user_id' => $user->id,
            'status' => 'pending'
        ]));

        // Award points for content creation
        app(PointService::class)->awardPoints($user, 100, 'blog_creation', $post->id);

        return $post;
    }

    public function publishPost($postId)
    {
        $post = $this->find($postId);
        $post->update(['status' => 'published']);
        return $post;
    }

    public function getPopularPosts($limit = 10)
    {
        return BlogPost::withCount('likes', 'comments')
            ->orderByDesc('likes_count')
            ->orderByDesc('comments_count')
            ->limit($limit)
            ->get();
    }
    public function addComment(User $user, $postId, $content, $parentId = null)
    {
        $post = BlogPost::findOrFail($postId);

        $comment = BlogComment::create([
            'blog_post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => $parentId,
            'content' => $content,
            'is_approved' => !$post->moderate_comments
        ]);

        // Award points for commenting
        if (!$parentId) {
            app(PointService::class)->awardPoints($user, 10, 'blog_comment', $comment->id);
        }

        return $comment;
    }

    public function toggleLike(User $user, $postId)
    {
        $post = BlogPost::findOrFail($postId);
        $like = BlogLike::where('blog_post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($like) {
            $like->delete();
            return false;
        }

        BlogLike::create([
            'blog_post_id' => $post->id,
            'user_id' => $user->id
        ]);

        // Award points for first like
        if ($post->likes()->count() === 0) {
            app(PointService::class)->awardPoints($user, 5, 'blog_like', $post->id);
        }

        return true;
    }

}
