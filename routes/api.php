<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LeaderboardController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SearchConroller;
use App\Http\Controllers\Api\BirthDateController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MentionController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\CollegeController;
use App\Http\Controllers\Api\CertificationController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\EducationController;
use App\Events\NewNotification;

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
    Route::get("profile/posts", [ProfileController::class, 'getMyPosts'])->name('user.posts');
    Route::put("profile/theme", [ProfileController::class, 'updateTheme'])->name('user.update-theme');
    Route::put("profile/social", [ProfileController::class, 'updateSocial'])->name('user.update-social');

    Route::get("companies", [CompanyController::class, 'index'])->name('companies');

    Route::post("experiences", [ExperienceController::class, 'store'])->name('experience.store');
    Route::put("experiences/{experience}", [ExperienceController::class, 'update'])->name('experience.update');
    Route::delete("experiences/{experience}", [ExperienceController::class, 'destroy'])->name('experience.destroy');

    Route::get("colleges", [CollegeController::class, 'index'])->name('college.index');

    Route::post("educations", [EducationController::class, 'store'])->name('education.store');
    Route::put("educations/{education}", [EducationController::class, 'update'])->name('education.update');
    Route::delete("educations/{education}", [EducationController::class, 'destroy'])->name('education.destroy');

    Route::post("certifications", [CertificationController::class, 'store'])->name('certification.store');
    Route::put("certifications/{certification}", [CertificationController::class, 'update'])->name('certification.update');
    Route::delete("certifications/{certification}", [CertificationController::class, 'destroy'])->name('certification.destroy');

    // user apis
    Route::get("users", [UserController::class, 'index'])->name('users');
    Route::get("users/{user}", [UserController::class, 'show'])->name('users.show');

    // mentions apis
    Route::post("mentions", [MentionController::class, 'store'])->name('mentions.store');

    // search apis
    Route::post("search", SearchConroller::class)->name('search');

    // notification apis
    Route::get("notifications", [NotificationController::class, 'getNotifications'])->name('notifications');
    Route::put("notifications/{id}", [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::get("trigger-noitification", function () {
        broadcast(new NewNotification(auth('api')->user(), 'New Notification'));
        return response()->json(['message' => 'Event has been sent!']);
    });

    // leaderboards apis
    Route::get("company/leaderboards", [LeaderboardController::class, 'getCompanyLeaderboards'])->name('company.leaderboards');
    Route::get('department/leaderboards', [LeaderboardController::class, 'getDepartmentLeaderboards'])->name('department.leaderboards');

    // birthdates apis
    Route::get("birthdates", [BirthDateController::class, 'getBirthDates'])->name('birthdates');

    // events apis
    Route::get("events", [EventController::class, 'getEvents'])->name('events');

    // posts apis
    Route::resource("posts", PostController::class)->except(['create', 'edit']);
    Route::put("posts/{post}/like", [PostController::class, 'reactLike'])->name('posts.like');
    Route::put("posts/{post}/undo-like", [PostController::class, 'undoReactLike'])->name('posts.undo-like');
    Route::put("posts/{post}/vote", [PostController::class, 'vote'])->name('posts.poll');
    Route::put("posts/{post}/undo-vote", [PostController::class, 'undoVote'])->name('posts.undo-poll');

    // comments apis
    Route::post("comments/{post}", [CommentController::class, 'store'])->name('comments.store');
    Route::put("comments/{comment}", [CommentController::class, 'update'])->name('comments.update');
    Route::put("comments/{comment}/like", [CommentController::class, 'reactLike'])->name('comments.like');
    Route::put("comments/{comment}/undo-like", [CommentController::class, 'undoReactLike'])->name('comments.undo-like');
    Route::delete("comments/{comment}", [CommentController::class, 'destroy'])->name('comments.destroy');
});
