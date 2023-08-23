<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;


Route::group(['as' => 'api.'], function () {
    // authintication
    Route::controller(AuthController::class)->group(function () {
        Route::post('login', 'login')->name('login');
        Route::post('register', 'register')->name('register');

        Route::post('logout', 'logout')->name('logout');
        Route::post('refresh', 'refresh')->name('refresh');

        Route::post('/broadcasting/auth', function () {
            return auth('api')->tokenById(auth('api')->user()->id);
        });
    });
});
