<?php

use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchConroller;
use App\Http\Controllers\Api\BirthDateController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

require __DIR__ . '/auth.php';

Route::group(['as' => 'api.', 'middleware' => 'jwt:api'], function () {
    // profile apis
    Route::put("user", [ProfileController::class, 'update'])->name('user.update');
    Route::put("user/theme", [ProfileController::class, 'updateTheme'])->name('user.update-theme');
    Route::put("user/experience", [ProfileController::class, 'updateExperience'])->name('user.update-experience');
    Route::put("user/education", [ProfileController::class, 'updateEducation'])->name('user.update-education');
    Route::put("user/certification", [ProfileController::class, 'updateCertification'])->name('user.update-certification');
    Route::put("user/social", [ProfileController::class, 'updateSocial'])->name('user.update-social');

    // search apis
    Route::post("search", SearchConroller::class)->name('search');

    // notification apis
    Route::get("notifications", [NotificationController::class, 'getNotifications'])->name('notifications');
    Route::put("notifications/{id}", [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');

    // leaderboards apis
    Route::get("company/leaderboards", [LeaderboardController::class, 'getCompanyLeaderboards'])->name('company.leaderboards');
    Route::get('department/leaderboards', [LeaderboardController::class, 'getDepartmentLeaderboards'])->name('department.leaderboards');

    // birthdates apis
    Route::get("birthdates", [BirthDateController::class, 'getBirthDates'])->name('birthdates');
});
