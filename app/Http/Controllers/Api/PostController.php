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
        $post = Post::select('id', 'user_id', 'thread', 'images', 'poll_end_date', 'votes', 'created_at')
            ->with(['polls' => function ($query) {
                $query->select('id', 'post_id', 'poll', 'votes', 'user_id');
            }])
            ->with(['comments' => function ($query) {
                $query->select('id', 'post_id', 'user_id', 'thread', "images", 'created_at')
                    ->with(['user' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }]);
            }])
            ->find($id);
        if (!$post) {
            return $this->notFound();
        }

        $post->makeHidden(['id', 'user_id', 'likes']);
        foreach ($post->comments as $comment) {
            $comment->makeHidden(['id', 'post_id', 'user_id', 'likes', 'images']);
            $comment->user->makeHidden('id');
        }

        return $this->apiSuccessResponse(
            ['post' => $post],
            $this->seo('get post', 'home-page'),
            'post retrieved successfully',
        );
    } // end of show

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validation = $this->apiValidationTrait($request->all(), [
            'thread' => 'required|string|max:255',
            "created_at" => "nullable|date",
            'old_images' => 'nullable|array',  // only old image path that will remain --- without url ---
            'images' => 'nullable|array',     // only new images
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'polls' => 'nullable|array',
            'polls.*.id' => 'nullable|numeric|max:255|exists:polls,id', //send poll id if you want to update
            'polls.*.poll' => 'required|string|max:255',
            'poll_end_date' =>  'required_if:polls,!null|date|after:today',
        ]);
        if ($validation) {
            return $validation;
        }

        $post = Post::find($id);
        if (!$post) {
            return $this->notFound();
        }

        $images = [];
        if ($request->old_images) {
            foreach ($request->old_images as $image) {
                if (!in_array($image, $request->images)) {
                    $this->deleteImg($image, 'images/posts/');
                } else {
                    $images[] = $image;
                }
            }
        }

        if ($request->images) {
            foreach ($request->images as $image) {
                $images[] = $this->img($image, 'images/posts/');
            }
        }

        $post->update([
            'thread' => $request->thread,
            'images' => $request->images ? json_encode($images) : null,
            'poll_end_date' => $request->poll_end_date ? $request->poll_end_date : null,
            'created_at' => $request->created_at ? $request->created_at : now(),
        ]);

        if ($request->polls) {
            foreach ($request->polls as $poll) {
                if (isset($poll['id'])) {
                    $post->polls()->find($poll['id'])->update([
                        'poll' => $poll['poll'],
                    ]);
                } else {
                    $post->polls()->create([
                        'poll' => $poll['poll'],
                    ]);
                }
            }
        }

        return $this->apiSuccessResponse(
            ['post' => $post->load('polls')],
            $this->seo('update post', 'home-page'),
            'post updated successfully',
        );
    } // end of update

    public function reactLike(Request $request, string $id)
    {
        $validation = $this->apiValidationTrait($request->all(), [
            "reaction" => "required|numeric|in:1,2,3",
        ]);
        if ($validation) return $validation;

        $post = Post::find($id);
        if (!$post) {
            return $this->notFound();
        }

        if ($request->reaction == 0) {
            $post->likes()->where('user_id', auth('api')->user()->id)->delete();
        } else {
            $post->likes()->updateOrCreate(
                ['user_id' => auth('api')->user()->id],
                ['reaction' => $request->reaction]
            );
        }

        return $this->apiSuccessResponse(
            ['post' => $post->load('likes')],
            $this->seo('react like', 'home-page'),
            'post reacted successfully',
        );
    } // end of reactLike

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) {
            return $this->notFound();
        }

        if ($post->images) {
            foreach (json_decode($post->images) as $image) {
                $this->deleteImg($image, 'images/posts/');
            }
        }

        $post->polls()->delete();
        $post->delete();

        return $this->apiSuccessResponse(
            [],
            $this->seo('delete post', 'home-page'),
            'post deleted successfully',
        );
    } // end of destroy
}
