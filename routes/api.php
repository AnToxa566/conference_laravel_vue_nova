<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\ConferenceController;
use App\Http\Controllers\API\UserConferenceController;

use App\Http\Controllers\API\LectureController;
use App\Http\Controllers\API\UserLectureController;

use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CountryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json(['name' => $request->user()->name]);
});


Route::controller(AuthController::class)->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::post('/register', 'register')->name('auth.register');
        Route::post('/login', 'login')->name('auth.login');
    });

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/profile/update', 'update')->name('auth.update');
        Route::get('/logout', 'logout')->name('auth.logout');
    });
});


Route::controller(ConferenceController::class)->group(function () {
    Route::get('/conferences', 'fetchAll')->name('conferences.fetchAll');

    Route::middleware(['auth:sanctum'])->get('/conferences/{id}', 'fetchDetail')->name('conferences.fetchDetail');
    Route::middleware(['auth:sanctum'])->post('/conferences/filtered', 'fetchFiltered')->name('conferences.fetchFiltered');
    Route::middleware(['auth:sanctum'])->get('/conferences/search/{search}/limit/{limit}', 'fetchSearchedConferences')->name('conferences.fetchSearchedConferences');

    Route::middleware(['admin', 'auth:sanctum'])->group(function () {
        Route::post('/conferences/add', 'store')->name('conferences.store');
        Route::post('/conferences/{id}/update', 'update')->name('conferences.update');
        Route::get('/conferences/{id}/delete', 'destroy')->name('conferences.destroy');
    });
});


Route::controller(UserConferenceController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/conferences/joined/{user_id}', 'fetchJoinedConferences')->name('conferences.fetchJoinedConferences');

        Route::get('/conferences/join/{user_id}/{conference_id}', 'joinConference')->name('conferences.joinConference');
        Route::get('/conferences/cancel/{user_id}/{conference_id}', 'cancelParticipation')->name('conferences.cancelParticipation');
    });
});


Route::controller(LectureController::class)->group(function () {
    Route::get('/lectures', 'fetchAll')->name('lectures.fetchAll');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/lectures/{id}', 'fetchById')->name('lectures.fetchById');
        Route::post('/lectures/filtered', 'fetchFiltered')->name('lectures.fetchFiltered');
        Route::get('/lectures/search/{search}/limit/{limit}', 'fetchSearchedLectures')->name('lectures.fetchSearchedLectures');
        Route::get('/lectures/{id}/presentation/download', 'downloadPresentation')->name('lectures.downloadPresentation');

        Route::middleware(['announcer'])->post('/lectures/add', 'store')->name('lectures.store');
        Route::middleware(['user.lecture'])->post('/lectures/{id}/update', 'update')->name('lectures.update');
        Route::middleware(['user.lecture'])->get('/lectures/{id}/delete', 'destroy')->name('lectures.destroy');
    });
});


Route::controller(UserLectureController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/lectures/favorited/{user_id}', 'fetchFavoriteLectures')->name('lectures.fetchFavoriteLectures');

        Route::get('/lectures/favorite/add/{user_id}/{lecture_id}', 'addFavoriteLecture')->name('lectures.addFavoriteLecture');
        Route::get('/lectures/favorite/remove/{user_id}/{lecture_id}', 'removeFavoriteLecture')->name('lectures.removeFavoriteLecture');
    });
});


Route::controller(CommentController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/comments/{lecture_id}/limit/{limit}/page/{page}', 'fetchByLectureId')->name('comments.fetchByLectureId');

        Route::post('/comments/add', 'store')->name('comments.store');
        Route::middleware(['user.comment'])->post('/comments/{id}/update', 'update')->name('comments.update');
    });
});


Route::controller(CategoryController::class)->group(function () {
    Route::get('/category', 'fetchAll')->name('category.fetchAll');

    Route::middleware(['admin', 'auth:sanctum'])->group(function () {
        Route::post('/category/add', 'store')->name('category.store');
        Route::get('/category/{id}/delete', 'destroy')->name('category.destroy');
    });
});


Route::controller(CountryController::class)->group(function () {
    Route::get('/country', 'fetchAll')->name('country.fetchAll');
});
