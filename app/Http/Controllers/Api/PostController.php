<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use GeneralTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::select('id', 'user_id', 'thread', 'images', 'poll_end_date', 'votes', 'created_at')
            ->with(['polls' => function ($query) {
                $query->select('id', 'post_id', 'poll', 'votes', 'user_id');
            }])
            ->with(['comments' => function ($query) {
                $query->select('id', 'post_id', 'user_id', 'thread', "images", 'created_at')
                    ->with(['user' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }]);
            }])
            ->latest()
            ->paginate(10);

            foreach ($posts as $post) {
                $post->makeHidden(['id', 'user_id', 'likes', 'images']);
                foreach ($post->comments as $comment) {
                    $comment->makeHidden(['id', 'post_id', 'user_id', 'likes', 'images']);
                    $comment->user->makeHidden('id');
                }
            }

        $seo = Seo::first();
        return $this->apiSuccessResponse( ['user' => $user],
        $this->seo('Update User', 'profile', $seo->description, $seo->keywords),
        'user updated successfully',
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
