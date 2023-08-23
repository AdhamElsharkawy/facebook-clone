<?php

use App\Http\Controllers\Admin\CertificationController;
use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EducationController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\ExperienceController;
use App\Http\Controllers\Admin\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SeoController;
use App\Events\NewNotification;
use App\Models\User;

//don't forget it has an admin prefix
require __DIR__ . '/auth.php';
Route::group(['middleware' => ['admin:sanctum'], 'as' => 'admin.'], function () {
    //dashboard
    Route::get('dashboard', [DashboardController::class, 'index']);
    //users
    Route::resource('users', UserController::class)->except(['show', 'create']);
    Route::delete('users/delete/all', [UserController::class, 'destroyAll']);
    Route::resource('departments', DepartmentController::class)->except(['show', 'create']);
    Route::delete('departments/delete/all', [DepartmentController::class, 'destroyAll']);
    Route::resource('events', EventController::class)->except(['show', 'create']);
    Route::delete('events/delete/all', [EventController::class, 'destroyAll']);
    Route::resource('colleges', CollegeController::class)->except(['show', 'create']);
    Route::delete('colleges/delete/all', [CollegeController::class, 'destroyAll']);
    Route::resource('companies', CompanyController::class)->except(['show', 'create']);
    Route::delete('companies/delete/all', [CompanyController::class, 'destroyAll']);
    Route::resource('educations', EducationController::class)->except(['show', 'create']);
    Route::delete('educations/delete/all', [EducationController::class, 'destroyAll']);
    Route::resource('experiences', ExperienceController::class)->except(['show', 'create']);
    Route::delete('experiences/delete/all', [ExperienceController::class, 'destroyAll']);
    Route::resource('certifications', CertificationController::class)->except(['show', 'create']);
    Route::delete('certifications/delete/all', [CertificationController::class, 'destroyAll']);
    Route::resource('posts', PostController::class)->except(['show', 'create','store']);
    Route::resource('comments', CommentController::class)->except(['show', 'create','store','edit','update']);
    Route::delete('comments/delete/all', [CommentController::class, 'destroyAll']);
    //seos
    Route::resource('seos', SeoController::class)->only(['index', 'update']);

});

Route::get("trigger-noitification", function () {
    broadcast(new NewNotification(User::find(1), 'New Notification'));
    return response()->json(['message' => 'Event has been sent!']);
});