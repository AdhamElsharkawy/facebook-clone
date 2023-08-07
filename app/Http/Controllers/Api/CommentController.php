<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\SeoTrait;
use App\Http\Traits\GeneralTrait;
use App\Http\Traits\ImageTrait;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\Api\NotificationController;

class CommentController extends Controller
{
    use GeneralTrait, SeoTrait, ImageTrait;

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            'thread' => 'required|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validations) return $validations;
        $post = Post::find($id);
        if (!$post) return $this->notFound();

        $images = [];
        if ($request->images) {
            foreach ($request->images as $image) {
                // $images[] = $this->img($image, 'images/posts/comments/');
                $images[] = $this->uploadS3Image($image, 'images/posts/comments/');
            }
        }

        $comment = $post->comments()->create([
            'thread' => $request->thread,
            'images' => $images !== [] ? json_encode($images) : null,
            'user_id' => auth('api')->user()->id,
        ]);
        NotificationController::newNotification(
            $post->user_id,
            'comment',
            $post->id,
        );

        return $this->apiSuccessResponse(
            ['comment' => $comment],
            $this->seo('create comment', 'home-page'),
            'comment created successfully',
        );
    } // end of store

    /**
     * update the specified resource.
     */
    public function update(Request $request, string $id)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            'thread' => 'required|string|max:255',
            'old_images' => 'nullable|array',  // only old image path that will remain --- without url ---
            'images' => 'nullable|array', // only new images
            'images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validations) return $validations;

        $comment = Comment::find($id);
        if (!$comment) return $this->notFound();

        $images = [];
        if ($request->old_images) {
            foreach ($request->old_images as $image) {
                if (!in_array($image, $request->images)) {
                    // $this->deleteImg($image, 'images/posts/comments/');
                    $this->deleteS3Image($image, 'images/posts/comments/');
                } else {
                    $images[] = $image;
                }
            }
        }

        if ($request->images) {
            foreach ($request->images as $image) {
                // $images[] = $this->img($image, 'images/posts/comments/');
                $images[] = $this->uploadS3Image($image, 'images/posts/comments/');
            }
        }

        $comment->update([
            'thread' => $request->thread,
            'images' => $images !== [] ? json_encode($images) : null,
        ]);

        return $this->apiSuccessResponse(
            ['comment' => $comment],
            $this->seo('update comment', 'home-page'),
            'comment updated successfully',
        );
    } // end of update

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $comment = Comment::find($id);
        if (!$comment) return $this->notFound();

        $comment->likes()->delete();

        if ($comment->images) {
            foreach ($comment->images as $image) {
                // $this->deleteImg($image, 'images/posts/comments/');
                $this->deleteS3Image($image, 'images/posts/comments/');
            }
        }

        $comment->delete();

        return $this->apiSuccessResponse(
            ['comment' => $comment],
            $this->seo('delete comment', 'home-page'),
            'comment deleted successfully',
        );
    } // end of destroy

    /**
     * React to the specified resource.
     */
    public function reactLike(Request $request, string $id)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "reaction" => "required|numeric|in:1,2,3",
        ]);

        if ($validations) return $validations;

        $comment = Comment::find($id);
        if (!$comment) return $this->notFound();

        if ($request->reaction == 0) {
            $comment->likes()->where('user_id', auth('api')->user()->id)->delete();
        } else {
            $comment->likes()->updateOrCreate(
                ['user_id' => auth('api')->user()->id],
                ['reaction' => $request->reaction]
            );
        }

        NotificationController::newNotification(
            $comment->user_id,
            'like',
            $comment->post_id,
        );

        return $this->apiSuccessResponse(
            [],
            $this->seo('react to comment', 'home-page'),
            'reacted to comment successfully',
        );
    } // end of reactLike

    /**
     * Undo react to the specified resource.
     */
    public function undoReactLike(string $id)
    {
        $comment = Comment::find($id);
        if (!$comment) return $this->notFound();

        $comment->likes()->where('user_id', auth('api')->user()->id)->delete();

        return $this->apiSuccessResponse(
            [],
            $this->seo('undo react to comment', 'home-page'),
            'undo reacted to comment successfully',
        );
    } // end of undoReactLike
}
