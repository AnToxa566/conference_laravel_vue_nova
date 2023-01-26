<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;

use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\ConferenceController;
use App\Http\Controllers\API\CountryController;

use App\Http\Controllers\API\LectureController;

use App\Http\Controllers\API\MeetingController;

use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PlanController;

use App\Http\Controllers\API\UserConferenceController;
use App\Http\Controllers\API\UserLectureController;


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
    Route::post('/register', 'register')->name('auth.register');
    Route::post('/login', 'login')->name('auth.login');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/auth/check', 'checkAuth')->name('auth.check');

        Route::post('/profile/update', 'update')->name('auth.update');
        Route::get('/logout', 'logout')->name('auth.logout');
    });
});


Route::controller(CategoryController::class)->group(function () {
    Route::get('/categories', 'fetchAll')->name('categories.fetchAll');
});


Route::controller(CommentController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/comments/{lecture_id}/limit/{limit}/page/{page}', 'fetchByLectureId')->name('comments.fetchByLectureId');

        Route::post('/comments/add', 'store')->name('comments.store');
        Route::middleware(['user.comment'])->post('/comments/{id}/update', 'update')->name('comments.update');
    });
});


Route::controller(ConferenceController::class)->group(function () {
    Route::get('/conferences', 'fetchAll')->name('conferences.fetchAll');

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/conferences/{id}', 'fetchDetail')->name('conferences.fetchDetail');
        Route::post('/conferences/filtered', 'fetchFiltered')->name('conferences.fetchFiltered');
        Route::get('/conferences/search/{search}/limit/{limit}', 'fetchSearchedConferences')->name('conferences.fetchSearchedConferences');
    });
});


Route::controller(CountryController::class)->group(function () {
    Route::get('/countries', 'fetchAll')->name('countries.fetchAll');
});


Route::controller(LectureController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/lectures', 'fetchAll')->name('lectures.fetchAll');

        Route::get('/lectures/{id}', 'fetchById')->name('lectures.fetchById');
        Route::post('/lectures/filtered', 'fetchFiltered')->name('lectures.fetchFiltered');
        Route::get('/lectures/search/{search}/limit/{limit}', 'fetchSearchedLectures')->name('lectures.fetchSearchedLectures');
        Route::get('/lectures/{id}/presentation/download', 'downloadPresentation')->name('lectures.downloadPresentation');

        Route::middleware(['announcer'])->post('/lectures/add', 'store')->name('lectures.store');
        Route::middleware(['can.update.lecture'])->post('/lectures/{id}/update', 'update')->name('lectures.update');
        Route::middleware(['can.delete.lecture'])->get('/lectures/{id}/delete', 'destroy')->name('lectures.destroy');
    });
});


Route::controller(MeetingController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/meetings/api', 'fetchAllFromAPI')->name('meetings.fetchAllFromAPI');
        Route::get('/meetings/db', 'fetchAllFromDB')->name('meetings.fetchAllFromDB');

        Route::get('/meetings/{meetingId}/update', 'update')->name('meetings.update');
    });
});


Route::controller(PaymentController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user/setup-intent', 'getSetupIntent')->name('user.getSetupIntent');

        Route::get('/user/payment-methods', 'getPaymentMethods')->name('user.getPaymentMethods');
        Route::post('/user/payments', 'storePaymentMethods')->name('user.storePaymentMethods');
        Route::post('/user/remove-payment', 'removePaymentMethod')->name('user.removePaymentMethod');
    });
});


Route::controller(PlanController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/plans', 'fetchPlans')->name('plans.fetchPlans');
        Route::get('/plans/{plan:slug}', 'fetchDetail')->name('plans.fetchDetail');

        Route::put('/plans/subscription', 'updateSubscription')->name('plans.updateSubscription');
    });
});


Route::controller(UserConferenceController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/conferences/joined/{user_id}', 'fetchJoinedConferences')->name('conferences.fetchJoinedConferences');

        Route::get('/conferences/join/{user_id}/{conference_id}', 'joinConference')->name('conferences.joinConference');
        Route::get('/conferences/cancel/{user_id}/{conference_id}', 'cancelParticipation')->name('conferences.cancelParticipation');
    });
});


Route::controller(UserLectureController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/lectures/favorited/{user_id}', 'fetchFavoriteLectures')->name('lectures.fetchFavoriteLectures');

        Route::get('/lectures/favorite/add/{user_id}/{lecture_id}', 'addFavoriteLecture')->name('lectures.addFavoriteLecture');
        Route::get('/lectures/favorite/remove/{user_id}/{lecture_id}', 'removeFavoriteLecture')->name('lectures.removeFavoriteLecture');
    });
});
