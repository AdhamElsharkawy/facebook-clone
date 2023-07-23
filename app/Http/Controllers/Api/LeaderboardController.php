<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Traits\GeneralTrait;

class LeaderboardController extends Controller
{
    use GeneralTrait;

    public function getCompanyLeaderboards()
    {
        $users = User::select("title", "name", "image", "global_score", "team_score", "department_id")
            ->where('role', 'user')
            ->with(['department' => function ($q) {
                $q->select('id', 'name');
            }])
            ->orderBy('global_score', 'desc')
            ->paginate(5);

        foreach ($users as $user) {
            $user->makeHidden(['department_id', "image"]);
            $user->department->makeHidden(['id']);
        }

        return $this->apiSuccessResponse($users);
    } //end of getCompanyLeaderboards

    public function getDepartmentLeaderboards()
    {
        $users = User::select("title", "name", "image", "global_score", "team_score", "department_id")
            ->where('role', 'user')
            ->where('department_id', auth('api')->user()->department_id)
            ->with(['department' => function ($q) {
                $q->select('id', 'name');
            }])
            ->orderBy('team_score', 'desc')
            ->paginate(5);

        foreach ($users as $user) {
            $user->makeHidden(['department_id', "image"]);
            $user->department->makeHidden(['id']);
        }

        return $this->apiSuccessResponse($users);
    } //end of getDepartmentLeaderboards
}
