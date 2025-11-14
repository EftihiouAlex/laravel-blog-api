<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PostsController extends Controller
{
    use AuthorizesRequests;

       /**
     * Display a paginated list of posts, with optional filtering.
     *
     * Available filters:
     * - author (string) → matches author name (LIKE search)
     * - author_id (int) → posts by a specific user ID
     * - tags (csv string) → filter by tag names (e.g. tags=php,laravel)
     * - category (csv string) → filter by category name or slug
     *
     * Relationships:
     * - author
     * - tags
     * - categories
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAllPosts(Request $request)
    {
        $query = Post::with(['author', 'tags', 'categories']);

        // Filter by author ID
        if ($request->has('author_id') && !empty($request->author_id)) {
            $query->where('user_id', $request->author_id);
        }

        // Filter by tags
        if ($request->has('tags') && !empty($request->tags)) {
            $tags = explode(',', $request->tags);
            $tags = array_map('trim', $tags);

            $query->whereHas('tags', function ($q) use ($tags) {
                $q->whereIn('tags.id', $tags);
            });
        }

        // Filter by categories
        if ($request->has('category') && !empty($request->category)) {
            $categories = explode(',', $request->category);
            $categories = array_map('trim', $categories);

            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('slug', $categories)
                ->orWhereIn('categories.id', $categories);
            });
        }
        
        $posts = $query->paginate(10);

        return response()->json($posts);
    }

    /**
     * Create a new post with optional tags and categories.
     *
     * Request fields:
     * - title: required, string, max:255
     * - post_content: required, string
     * - tags: optional array of tag IDs
     * - categories: optional array of category IDs
     *
     * Behaviors:
     * - Automatically generates slug from title
     * - Attaches the authenticated user as author
     * - Automatically attaches the "new" tag to every created post
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function newPost(Request $request) {
        $request->validate([
            'title' => 'required|string|max:255',
            'post_content' => 'required|string',
            'tags' => 'array',
            'tags.*' => 'integer|exists:tags,id',
            'categories' => 'array',
            'categories.*' => 'integer|exists:categories,id',
        ]);

        $post = Post::create([
            'title' => $request->title,
            'post_content' => $request->post_content,
            'slug' => Str::slug($request->title),
            'user_id' => $request->user()->id,
        ]);

        if ($request->filled('tags')) {
            $post->tags()->sync($request->tags);
        }

        if ($request->filled('categories')) {
            $post->categories()->sync($request->categories);
        }

        $newTag = Tag::firstOrCreate(
            ['name' => 'new'],
            ['slug' => 'new']
        );

        $post->tags()->syncWithoutDetaching([$newTag->id]);

        return response()->json([
            'message' => 'Post created successfully!',
            'post' => $post->load(['author', 'tags', 'categories']),
        ], 201);
    }


    /**
     * Display a single post by ID and slug.
     *
     * Route model binding provides the Post instance.
     *
     * Slug must match the post’s stored slug, otherwise 404 is returned.
     *
     * Loaded relationships:
     * - author
     * - tags
     *
     * @param \App\Models\Post $post
     * @param string $slug
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function showPost(Post $post, $slug) {   
        if ($post->slug !== $slug) {
            return response()->json([
                'message' => 'Post slug mismatch'
            ], 404);
        }

        $post->load(['author', 'tags']);

        $response = [
            'id'          => $post->id,
            'title'       => $post->title,
            'slug'        => $post->slug,
            'content'     => $post->post_content,
            'author' => [
                'id'   => $post->author->id,
                'name' => $post->author->name,
            ],
            'tags' => $post->tags->map(function ($tag) {
                return [
                    'id'   => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ];
            })->values(),
        ];

        return response()->json($response);
    }

    /**
     * Update a post’s title or content.
     *
     * Behaviors:
     * - Only updates fields that changed (partial update)
     * - If title changes, slug is regenerated
     * - If any change happens, removes the "new" tag
     * - Returns whether the post was actually edited
     *
     * Request fields:
     * - title: optional, string
     * - post_content: optional, string
     * - tags: optional array of tag IDs (not applied here but validated)
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Post $post
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Post $post) {
        $this->authorize('update', $post);

        $request->validate([
            'title'        => 'sometimes|string|max:255',
            'post_content' => 'sometimes|string',
            'tags'         => 'array',
            'tags.*'       => 'integer|exists:tags,id'
        ]);

        $wasEdited = false;

        if ($request->filled('title') && $request->title !== $post->title) {
            $post->title = $request->title;
            $post->slug = Str::slug($request->title);
            $wasEdited = true;
        }

        if ($request->filled('post_content') && $request->post_content !== $post->post_content) {
            $post->post_content = $request->post_content;
            $wasEdited = true;
        }

        if ($wasEdited) {
            $newTag = Tag::where('slug', 'new')->first();
            if ($newTag) {
                $post->tags()->detach($newTag->id);
            }

            $editedTag = Tag::firstOrCreate(
                ['slug' => 'edited'],
                ['name' => 'Edited']
            );

            $post->save();
            $post->tags()->syncWithoutDetaching([$editedTag->id]);
        }

        $post->load(['author', 'tags']);

        return response()->json([
            'message' => $wasEdited ? 'Post updated successfully!' : 'Nothing changed.',
            'post' => [
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
                'content' => $post->post_content,
                'author' => [
                    'id' => $post->author->id,
                    'name' => $post->author->name,
                ],
                'tags' => $post->tags->map(fn($tag) => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                    'slug' => $tag->slug,
                ]),
            ]
        ]);
    }

     /**
     * Delete a post by ID.
     *
     * Uses findOrFail() so a missing post returns 404.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deletePostById($id) {
        $post = Post::findOrFail($id);

        $this->authorize('delete', $post);

        $post->delete();

        return response()->json(['message' => 'Post deleted']);
    }

    /**
     * Return all posts created by a specific user.
     *
     * Includes:
     * - tags
     * - categories
     *
     * Uses route model binding for the User model.
     *
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function postsByUser(User $user) {
        return response()->json([
            'user' => $user->id,
            'posts' => $user->posts()->with(['tags', 'categories'])->get()
        ]);
    }
}
