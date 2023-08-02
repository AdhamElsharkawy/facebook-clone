<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Comment;
use App\Http\Requests\Admin\Comment\StoreCommentRequest;
use App\Http\Requests\Admin\Comment\UpdateCommentRequest;
use App\Models\User;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // i want to select the comment and the name of the user who post the post

        $comments = Comment::with(['user' => function ($query) {
            $query->select('id', 'name');
        }, 'post' => function ($query) {
            $query->select('id', 'user_id','thread')->with(['user' => function ($query) {
                $query->select('id', 'name');
            }]);
        }])->latest()->paginate(10);

        return [
            'comments' => $comments,
        ];
    }

    public function destroy(Comment $comment)
    {
        if ($comment->images) {
            for ($i = 0; $i < count(($comment->images)); $i++) {
                $comment->images[$i] !=  'assets/images/default.png' ? $this->deleteS3Image($comment->images[$i]) : '';
            }
        }
        


        
        $comment->likes()->whereNull('post_id')->delete();
        $comment->likes()->update(['comment_id' => null]);
        // $comment->likes()->delete();
        $comment->delete();
    }
    public function destroyAll(Request $request)
    {
        $comments = Comment::whereIn('id', $request->comments)->get();
        foreach ($comments as $comment) {
            $comment->delete();
        }
        return response()->json(['message' => __('Comments Deleted Successfully')]);
    } // end of destroy all function
}
