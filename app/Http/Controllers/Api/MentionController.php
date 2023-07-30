<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Traits\SeoTrait;
use App\Http\Traits\GeneralTrait;
use App\Models\Mention;
use App\Models\Seo;
use App\Http\Controllers\Api\NotificationController;

class MentionController extends Controller
{
    use GeneralTrait, SeoTrait;

    public function store(Request $request)
    {
        $validations = $this->apiValidationTrait($request->all(), [
            "post_id" => "required_if:comment_id,null|exists:posts,id",
            "comment_id" => "required_if:post_id,null|exists:comments,id",
            "mentions" => "required|array",
            "mentions.*.mentioned_id" => "required|exists:users,id",
        ]);
        if ($validations) return $validations;

        $mentions = [];
        foreach ($request->mentions as $mention) {
            array_push($mentions, Mention::create([
                "post_id" => $request->post_id,
                "comment_id" => $request->comment_id,
                "mentioned_user_id" => $mention["mentioned_id"],
                "user_id" => auth('api')->user()->id,
            ]));

            NotificationController::newNotification(
                $mention["mentioned_id"],
                "mention",
                $request->post_id ?? null,
                $request->comment_id ?? null
            );
        }

        return $this->apiSuccessResponse(
            ['mentions' => $mentions],
            $this->seo('mentions', 'home-page'),
            "Mentioned successfully"
        );
    } //end of store
}
