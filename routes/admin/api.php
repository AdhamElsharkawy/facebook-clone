<?php

use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EventController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SeoController;

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

    //seos
    Route::resource('seos', SeoController::class)->only(['index', 'update']);
});
