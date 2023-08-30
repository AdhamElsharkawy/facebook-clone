<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\Post;
use App\Models\User;
use App\Models\Seo;
use App\Http\Traits\SeoTrait;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Company;
use App\Http\Traits\ImageTrait;

class ProfileController extends Controller
{
    use GeneralTrait, SeoTrait, ImageTrait;

    public static function getPosts($user_id)
    {
        $posts = Post::where('user_id', $user_id)
            ->select('id', 'user_id', 'thread', 'images', 'poll_end_date', 'poll_caption', 'created_at')
            ->with(['polls' => function ($query) {
                // select only name and image for the user
                $query->select('id', 'post_id', 'poll', 'votes')->with(['users:id,name,image']);
            }])
            ->with(['comments' => function ($query) {
                $query->select('id', 'post_id', 'user_id', 'thread', "images", 'created_at')
                    ->with(['user' => function ($query) {
                        $query->select('id', 'name', 'image', 'department_id')
                            ->with(['department:id,name']);
                    }]);
            }])
            ->latest()
            ->paginate(10);

        if ($posts->count() == 0) return $posts;

        foreach ($posts as $post) {
            $post->makeHidden(['likes']);
            foreach ($post->comments as $comment) {
                $comment->makeHidden(['post_id', 'user_id', 'likes']);
            }
        }
        return $posts;
    } //end of getPosts

    public static function getProfileData($user_email)
    {
        $user = User::where('email', $user_email)
            ->select("id", "title", "name", "email", "image", "mobile", "status", "birth_date", "global_score", "team_score", "social_links", "front_theme", "department_id")
            ->with(['department:id,name'])
            ->with([
                'experiences' => function ($query) {
                    $query->select("id", "title", "type", "start_date", "end_date", "is_current", "user_id", "company_id")
                        ->with(['company' => function ($query) {
                            $query->select('id', 'name', 'image');
                        }])->orderBy('start_date', 'desc');
                },
            ])
            ->with(['educations' => function ($query) {
                $query->select("id", "degree", "major", "start_date", "end_date", "is_current", "user_id", "location", "college_id")
                    ->with(['college' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }])->orderBy('start_date', 'desc');
            }])
            ->with(['certifications' => function ($query) {
                $query->select("id", "major", "location", "start_date", "end_date", "is_current", "valid_until", "confirmation_link", "user_id", "college_id")
                    ->with(['college' => function ($query) {
                        $query->select('id', 'name', 'image');
                    }])->orderBy('start_date', 'desc');
            }])
            ->first();

        $user->social_links = json_decode($user->social_links);
        $user->makeHidden(['image']);
        // hide the unnecessary fields
        if ($user->department) {
            $user->makeHidden(['department_id']);
        }
        if ($user->experiences) {
            foreach ($user->experiences as $experience) {
                $experience->makeHidden(['user_id', 'company_id']);
                $experience->company->makeHidden(['image']);
            }
        }
        if ($user->educations) {
            foreach ($user->educations as $education) {
                $education->makeHidden(['user_id', 'college_id']);
                $education->college->makeHidden(['image']);
            }
        }
        if ($user->certifications) {
            foreach ($user->certifications as $certification) {
                $certification->makeHidden(['user_id', 'college_id']);
                $certification->college->makeHidden(['image']);
            }
        }

        return $user;
    } //end of getProfileData

    public function getMyPosts(){
        $user = Auth::guard('api')->user();
        $posts = self::getPosts($user->id);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            ['posts' => $posts],
            $this->seo('My Posts', 'profile', $seo->description, $seo->keywords),
            'my posts retrieved successfully',
        );
    } // end of getMyPosts

    public function update(Request $request)
    {
        $validator = $this->apiValidationTrait($request->all(), [
            'title' => 'required|string',
            'mobile' => 'required|string|min:9|max:15',
            "image" => "nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048",
        ]);
        if ($validator) return $validator;
        $form_data = $request->only(['title', 'mobile']);
        $user = Auth::guard('api')->user();
        if ($request->image) {
            // check if user image is  user.png
            $user->image != 'assets/images/user.png' ? $this->deleteS3Image($user->image) : '';
            $form_data['image'] = $this->uploadS3Image($request->image, 'images/users');
            // $form_data['image'] = $this->img($request->image, 'images/users/');
        } else {
            $form_data['image'] = $user->image;
        }

        $user = Auth::guard('api')->user();
        $user->update($form_data);

        $seo = Seo::first();
        return $this->apiSuccessResponse(
            ['user' => $user],
            $this->seo('Update User', 'profile', $seo->description, $seo->keywords),
            'user updated successfully',
        );
    } //end of updateUserDate

    public function updateTheme(Request $request)
    {
        $rules = [
            'theme' => 'required|in:light,dark'
        ];
        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return $this->apiValidationTrait($request->all(), $rules);
        }
        $user = Auth::guard('api')->user();
        $user->update(['front_theme' => $request->theme]);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            null,
            $this->seo('Update User Theme', 'profile', $seo->description, $seo->keywords),
            'user theme updated successfully',
        );
    } //end of updateTheme

    public function updateExperience(Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'type' => 'required|in:1,2',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'is_current' => 'required|boolean',
            'company' => 'nullable|string',
        ];
        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return $this->apiValidationTrait($request->all(), $rules);
        }

        $form_data = $request->except('company');
        $form_data['company_id'] = $this->updateOrCreateCompany($request->company);

        $user = Auth::guard('api')->user();
        $user->experiences()->create($form_data);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            null,
            $this->seo('Update User Experience', 'profile', $seo->description, $seo->keywords),
            'user experience updated successfully',
        );
    } //end of updateExperience

    public function updateOrCreateCompany($company_name)
    {
        if ($company_name) {
            $company = Company::where('name', 'like', '%' . $company_name . '%')->first();
            if (!$company) {
                $company = Company::create(['name' => $company_name]);
            }
        }
        return $company->id;
    } //end of updateOrCreateCompany

    public function updateEducation(Request $request)
    {
        $rules = [
            'degree' => 'required|string',
            'major' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'is_current' => 'required|boolean',
            'location' => 'nullable|string',
            'college' => 'nullable|string',
        ];
        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return $this->apiValidationTrait($request->all(), $rules);
        }

        $form_data = $request->except('college');
        $form_data['college_id'] = $this->updateOrCreateCollege($request->college);

        $user = Auth::guard('api')->user();
        $user->educations()->create($form_data);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            null,
            $this->seo('Update User Education', 'profile', $seo->description, $seo->keywords),
            'user education updated successfully',
        );
    } //end of updateEducation

    public function updateOrCreateCollege($college_name)
    {
        if ($college_name) {
            $college = College::where('name', 'like', '%' . $college_name . '%')->first();
            if (!$college) {
                $college = College::create(['name' => $college_name]);
            }
        }
        return $college->id;
    } //end of updateOrCreateCollege

    public function updateCertification(Request $request)
    {
        $rules = [
            'major' => 'required|string',
            'location' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'is_current' => 'required|boolean',
            'valid_until' => 'nullable|date',
            'confirmation_link' => 'nullable|url',
            'college' => 'nullable|string',
        ];
        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return $this->apiValidationTrait($request->all(), $rules);
        }

        $form_data = $request->except('college');
        $form_data['college_id'] = $this->updateOrCreateCollege($request->college);

        $user = Auth::guard('api')->user();
        $user->certifications()->create($form_data);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            null,
            $this->seo('Update User Certification', 'profile', $seo->description, $seo->keywords),
            'user certification updated successfully',
        );
    } //end of updateCertification

    public function updateSocial(Request $request)
    {
        $rules = [
            'social_links' => 'required|array',
            'social_links.*' => 'required|url',
        ];
        try {
            $request->validate($rules);
        } catch (\Exception $e) {
            return $this->apiValidationTrait($request->all(), $rules);
        }

        $user = Auth::guard('api')->user();
        $user->update(['social_links' => json_encode($request->social_links)]);
        $seo = Seo::first();
        return $this->apiSuccessResponse(
            null,
            $this->seo('Update User Social Links', 'profile', $seo->description, $seo->keywords),
            'user social links updated successfully',
        );
    } //end of updateSocial
}
