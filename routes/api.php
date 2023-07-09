<?php

use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SearchConroller;

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

Route::group(['as' => 'api.', 'middleware'=>'jwt:api'], function () {
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
});
