<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;

class UserController extends Controller
{
    public static function getPosts($user_id)
    {
        $posts = Post::where('user_id', $user_id)
            ->select('id', 'user_id', 'thread', 'images', 'poll_end_date', 'votes', 'created_at')
            ->with(['polls' => function ($query) {
                $query->select('id', 'post_id', 'poll', 'votes', 'user_id');
            }])
            ->with(['comments' => function ($query) {
                $query->select('id', 'post_id', 'user_id', 'thread', "images", 'created_at')
                    ->with(['user' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }]);
            }])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        foreach ($posts as $post) {
            $post->makeHidden(['id', 'user_id', 'likes', 'images']);
            foreach ($post->comments as $comment) {
                $comment->makeHidden(['id', 'post_id', 'user_id', 'likes', 'images']);
                $comment->user->makeHidden('id');
            }
        }
        return $posts;
    } //end of getPosts

    public static function getProfileData($user_email)
    {
        $user = User::where('email', $user_email)
            ->select("id", "title", "name", "email", "image", "mobile", "status", "birth_date", "score", "social_links", "front_theme", "department_id")
            ->with(['department:id,name'])
            ->with([
                'experiences' => function ($query) {
                    $query->select("title", "type", "start_date", "end_date", "is_current", "user_id", "company_id")
                        ->with(['company' => function ($query) {
                            $query->select('id', 'name', 'image');
                        }]);
                },
            ])
            ->with(['educations' => function ($query) {
                $query->select("degree", "major", "start_date", "end_date", "is_current", "user_id", "location", "college_id")
                    ->with(['college' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }]);
            }])
            ->with(['certifications' => function ($query) {
                $query->select("major", "location", "start_date", "end_date", "is_current", "valid_until", "confirmation_link", "user_id", "college_id")
                    ->with(['college' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }]);
            }])
            ->first();

        // hide the unnecessary fields
        $user->makeHidden(['id', 'email', 'image', 'mobile', 'status', 'score', 'social_links', 'front_theme', 'department_id']);
        $user->department->makeHidden('id');
        foreach ($user->experiences as $experience) {
            $experience->makeHidden(['user_id', 'company_id']);
            $experience->company->makeHidden(['id', 'image']);
        }
        foreach ($user->educations as $education) {
            $education->makeHidden(['user_id', 'college_id']);
            $education->college->makeHidden(['id', 'image']);
        }
        foreach ($user->certifications as $certification) {
            $certification->makeHidden(['user_id', 'college_id']);
            $certification->college->makeHidden(['id', 'image']);
        }

        return $user;
    } //end of getProfileData
}
