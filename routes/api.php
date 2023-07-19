<?php

use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchConroller;
use App\Http\Controllers\Api\BirthDateController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MentionController;

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
    Route::put("profile", [ProfileController::class, 'update'])->name('user.update');
    Route::put("profile/theme", [ProfileController::class, 'updateTheme'])->name('user.update-theme');
    Route::put("profile/experience", [ProfileController::class, 'updateExperience'])->name('user.update-experience');
    Route::put("profile/education", [ProfileController::class, 'updateEducation'])->name('user.update-education');
    Route::put("profile/certification", [ProfileController::class, 'updateCertification'])->name('user.update-certification');
    Route::put("profile/social", [ProfileController::class, 'updateSocial'])->name('user.update-social');

    // user apis
    Route::get("users", [UserController::class, 'index'])->name('users');

    // mentions apis
    Route::post("mentions", [MentionController::class, 'store'])->name('mentions.store');

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

    // events apis
    Route::get("events", [EventController::class, 'getEvents'])->name('events');

    Route::resource("posts", PostController::class)->except(['create', 'edit']);
    Route::put("posts/{post}/like", [PostController::class, 'reactLike'])->name('posts.like');

    Route::post("comments/{post}", [CommentController::class, 'store'])->name('comments.store');
    Route::put("comments/{comment}", [CommentController::class, 'update'])->name('comments.update');
    Route::put("comments/{comment}/like", [CommentController::class, 'reactLike'])->name('comments.like');
    Route::delete("comments/{comment}", [CommentController::class, 'destroy'])->name('comments.destroy');
});
