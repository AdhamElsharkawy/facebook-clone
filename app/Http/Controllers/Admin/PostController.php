<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Traits\ImageTrait;
use App\Models\Comment;
use App\Models\Post;
use App\Http\Requests\Admin\Post\StorePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;
use Carbon\Carbon;

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
        }, 'comments.user:id,name', 'polls'])->latest()->get();


        return ['posts' => $posts];

        //        ['comments' => function ($query){
        //            $query->select('user_id');
        //        }]
    }



    //    /**
    //     * Display the specified resource.
    //     */
    //    public function show(Post $post)
    //    {
    //        //
    //    }

    /**
     * Show the form for editing the specified resource.
     */
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
        // $form_data = $request->validated();
        $form_data = $request->all();
        if ($form_data['images']) {
            for ($i = 0; $i < count($request->images); $i++) {
                $post->images[$i] !=  'assets/images/default.png' ? $this->deleteImg($post->images[$i]) : '';
                $form_data['images'][$i] = $this->img($request->images[$i], 'images/posts/');
            }
        } else {
            $form_data['images'] = $post->images;
        }
        if ($request->poll_end_date) {
            $form_data['poll_end_date'] = $form_data['poll_end_date'] ?? Carbon::parse($request->poll_end_date);
        }
        if ($request->comments) {
            $post->comments = json_decode($request->comments);
        }

        $post->update($form_data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if ($post->images) {
            for ($i = 0; $i < count(($post->images)); $i++) {
                $post->images[$i] !=  'assets/images/default.png' ? $this->deleteImg($post->images[$i]) : '';
            }
        }
        $post->polls()->delete();
        $post->delete();
    }
    public function destroyComment($commentId)
    {
        $comment = Comment::findOrFail($commentId);
        $comment->delete();
    }
}
