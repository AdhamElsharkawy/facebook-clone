<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Seo;
use App\Http\Traits\SeoTrait;

class PostController extends Controller
{
    use GeneralTrait, SeoTrait;

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
            // remove the post that has a date didn't come yet
            if ($post->created_at > now()) {
                $posts->forget($post->id);
            }
        }

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            ['posts' => $posts],
            $this->seo('get posts', 'home-page', $seo->description, $seo->keywords),
            'user updated successfully',
        );
    } // end of index

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validation = $this->apiValidationTrait($request->all(), [
            'thread' => 'required|string|max:255',
            "created_at" => "nullable|date",
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'polls' => 'nullable|array',
            'polls.*.poll' => 'required|string|max:255',
            'poll_end_date' =>  'required_if:polls,!null|date|after:today',
        ]);
        if ($validation) {
            return $validation;
        }

        if ($request->images) {
            $images = [];
            foreach ($request->images as $image) {
                $images[] = $this->img($image, 'images/posts/');
            }
        }

        $post = Post::create([
            'thread' => $request->thread,
            'images' => $request->images ? json_encode($images) : null,
            'poll_end_date' => $request->poll_end_date ? $request->poll_end_date : null,
            'user_id' => auth('api')->user()->id,
            'created_at' => $request->created_at ? $request->created_at : now(),
        ]);

        if ($request->polls) {
            foreach ($request->polls as $poll) {
                $post->polls()->create([
                    'poll' => $poll['poll'],
                ]);
            }
        }

        return $this->apiSuccessResponse(
            ['post' => $post->load('polls')],
            $this->seo('create post', 'home-page'),
            'post created successfully',
        );
    } // end of store

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
