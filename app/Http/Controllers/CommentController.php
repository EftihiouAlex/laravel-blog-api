<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Notifications\NewCommentNotification;

class CommentController extends Controller
{

    /**
     * Create a new comment for a specific post.
     *
     * Validates the incoming request, ensures the post exists,
     * attaches the authenticated user as the comment author,
     * and triggers a notification to the post owner (if applicable).
     *
     * Required payload:
     * - comment_content: string (max 500)
     *
     * @param \Illuminate\Http\Request $request
     * @param int $postId  The ID of the post the comment is attached to.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the post is not found.
     */
    public function newComment(Request $request, $postId) {
        // Validate request
        $request->validate([
            'comment_content' => 'required|string|max:500',
        ]);

        // Find post or fail
        $post = Post::findOrFail($postId);

        // Create comment
        $comment = $post->comments()->create([
            'user_id' => $request->user()->id,
            'comment_content' => $request->comment_content,
        ]);

        // Send notification to post author
        if ($post->author && $post->author->id !== $request->user()->id) {
            $post->author->notify(
                new NewCommentNotification($comment)
            );
        }

        return response()->json([
            'message' => 'Comment added successfully!',
            'comment' => $comment->load('user'),
        ], 201);
    }
    /**
     * Retrieve all comments made by a specific user.
     *
     * Includes the post associated with each comment.
     *
     * @param int $userId  The ID of the user whose comments to retrieve.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function commentsByUser($userId) {
        $comments = Comment::where('user_id', $userId)
            ->with('post')
            ->paginate(10);

        return response()->json($comments);
    }
}
