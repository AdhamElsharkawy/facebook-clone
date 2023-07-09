<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\GeneralTrait;
use App\Models\User;

class BirthDateController extends Controller
{

    use GeneralTrait;

    public function getBirthDates()
    {
        $users = User::whereRaw('DAYOFYEAR(birth_date) >= DAYOFYEAR(NOW())')
            ->orderByRaw('DAYOFYEAR(birth_date) ASC')
            ->limit(5)
            ->get();
        foreach ($users as $user) {
            $user->social_links = json_decode($user->social_links);
            if (date('m') != date('m', strtotime($user->birth_date))) {
                $users = $users->except($user->id);
            }
        }
        return $this->apiSuccessResponse(["users" => $users]);
    } // end of getBirthDates
}
