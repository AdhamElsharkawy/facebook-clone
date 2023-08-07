<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Traits\ImageTrait;
use App\Models\Comment;
use App\Models\Post;
use App\Http\Requests\Admin\Post\StorePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostController extends Controller
{
    use ImageTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['user' => function ($query) {
            $query->select('id', 'name');
        }, 'comments.user:id,name', 'polls'])->paginate(5);


        return ['posts' => $posts];
    }


    public function edit(Post $post)
    {
        return response()->json([
            'post' => $post
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $form_data = $request->except(['polls', 'comments', 'images']);
        $polls_form_data["polls"] = $request->polls ? json_decode($request->polls, true) : null;
        // dd($request->all());

        // dd($form_data);
        if ($form_data['postNow']) {
            $form_data['created_at'] = Carbon::now();
        } else {
            $form_data['created_at'] = Carbon::parse($request->created_at)->format('Y-m-d H:i:s');
        }
        unset($form_data['postNow']);

        if ($request->images) {
            $form_data["images"] = [];
            if ($post->images) {
                for ($i = 0; $i < count(($post->images)); $i++) {
                    if ($post->images[$i] !=  'assets/images/default.png') {
                        $this->deleteS3Image($post->images[$i]);
                    }
                }
            }
            for ($i = 0; $i < count($request->images); $i++) {
                $file_name = $this->uploadS3Image($request->images[$i], 'images/posts');
                array_push($form_data["images"], $file_name);
            }
            $form_data["images"] = json_encode($form_data["images"]);
        }

        if ($request->polls) {
            $polls = $post->polls;
            foreach ($polls as $poll) {
                $poll->users()->detach();
            }
            $post->polls()->delete();
            foreach ($polls_form_data["polls"] as $poll) {
                $post->polls()->create([
                    'poll' => $poll['poll'],
                    'votes' => 0,
                ]);
            }
        } else {
            $post->polls()->delete();
        }
        $post->update($form_data);

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->images) {
            for ($i = 0; $i < count(($post->images)); $i++) {
                $post->images[$i] !=  'assets/images/default.png' ? $this->deleteS3Image($post->images[$i]) : '';
            }
        }
        $polls = $post->polls;
        foreach ($polls as $poll) {
            $poll->users()->detach();
        }
        $post->polls()->delete();
        $post->comments()->delete();
        $post->likes()->delete();
        // $post->comments()->likes()->delete();
        $post->delete();
    }
    // public function destroyComment($commentId)
    // {
    //     $comment = Comment::findOrFail($commentId);
    //     $comment->delete();
    // }
}
