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
        $leaderboard = User::where('role', 'user')->orderBy('score', 'desc')->paginate(5);
        return $this->apiSuccessResponse($leaderboard);
    } //end of getCompanyLeaderboards

    public function getDepartmentLeaderboards()
    {
        $leaderboard = User::where('role', 'user')->where('department_id', auth('api')->user()->department_id)->orderBy('score', 'desc')->paginate(5);
        return $this->apiSuccessResponse($leaderboard);
    } //end of getDepartmentLeaderboards
}
