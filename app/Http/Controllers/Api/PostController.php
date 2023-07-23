<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Models\Seo;
use App\Http\Traits\SeoTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendMail;

class PostController extends Controller
{
    use GeneralTrait, SeoTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::select('id', 'user_id', 'thread', 'images', 'poll_end_date', 'created_at')
            ->with(['polls' => function ($query) {
                $query->select('id', 'post_id', 'poll', 'votes')->with(['users:id,name,image']);
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
            $post->makeHidden(['user_id', 'likes', 'images']);
            foreach ($post->comments as $comment) {
                $comment->makeHidden(['post_id', 'user_id', 'likes', 'images']);
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
            'front_link' => 'required|url|max:255',
            'send_all' => 'nullable|boolean',
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
            'front_link' => $request->front_link,
            'user_id' => auth('api')->user()->id,
            'created_at' => $request->created_at ? $request->created_at : now(),
        ]);

        if ($request->send_all && Auth::user()->role == "manager") {
            $users = User::whereIn("role", ["user", "manager", "team_leader"])->get();

            Bus::batch(
                $users->map(function ($user) use ($post) {
                    return new SendMail($user, $post);
                })
            )->dispatch();
        }

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
        $post = Post::select('id', 'user_id', 'thread', 'images', 'poll_end_date', 'created_at')
            ->with(['polls' => function ($query) {
                $query->select('id', 'post_id', 'poll', 'votes')->with(['users:id,name,image']);
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

        $post->makeHidden(['user_id', 'likes']);
        foreach ($post->comments as $comment) {
            $comment->makeHidden(['post_id', 'user_id', 'likes', 'images']);
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
            'front_link' => 'required|url|max:255',
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
            'front_link' => $request->front_link,
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

    public function vote(Request $request, string $id)
    {
        $validation = $this->apiValidationTrait($request->all(), [
            "poll_id" => "required|numeric|exists:polls,id",
        ]);
        if ($validation) return $validation;

        $post = Post::find($id);
        if (!$post) {
            return $this->notFound();
        }

        $poll = $post->polls()->find($request->poll_id);
        if (!$poll) {
            return $this->notFound();
        }

        // check if the user already voted
        if ($poll->users()->where('user_id', auth('api')->user()->id)->first()) {
            return response()->json([
                'status' => false,
                'message' => 'you already voted',
            ], 422);
        }

        $poll->update([
            'votes' => $poll->votes + 1,
        ]);
        $poll->users()->syncWithoutDetaching(auth('api')->user()->id);

        return $this->apiSuccessResponse(
            ['post' => $post->load('polls')],
            $this->seo('vote', 'home-page'),
            'post voted successfully',
        );
    } // end of vote
}
