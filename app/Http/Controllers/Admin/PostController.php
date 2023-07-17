<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Post;
use App\Http\Requests\Admin\Post\StorePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::with(['user'=> function ($query){
            $query->select('id', 'name');
        },'comments.user:id,name','polls'])->latest()->get();


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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        //
    }
}
