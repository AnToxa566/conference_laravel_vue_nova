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

use App\Http\Controllers\API\StripeWebhookController;

use App\Http\Controllers\API\UserConferenceController;
use App\Http\Controllers\API\UserLectureController;


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json($request->user());
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

        Route::middleware(['user.comment'])->group(function () {
            Route::post('/comments/{id}/update', 'update')->name('comments.update');
        });
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

        Route::middleware(['announcer'])->group(function () {
            Route::post('/lectures/add', 'store')->name('lectures.store');

            Route::middleware(['lecture.owner'])->group(function () {
                Route::post('/lectures/{id}/update', 'update')->name('lectures.update');
                Route::get('/lectures/{id}/delete', 'destroy')->name('lectures.destroy');
            });
        });
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
        Route::post('/user/payments', 'storePaymentMethod')->name('user.storePaymentMethod');
        Route::post('/user/remove-payment', 'removePaymentMethod')->name('user.removePaymentMethod');
    });
});


Route::controller(PlanController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/plans', 'fetchPlans')->name('plans.fetchPlans');

        Route::get('/plans/{plan:slug}', 'fetchDetail')->name('plans.fetchDetail');
        Route::get('/user/current-plan', 'fetchCurrentPlan')->name('plans.fetchCurrentPlan');

        Route::put('/plans/subscription', 'updateSubscription')->name('plans.updateSubscription');
        Route::put('/plans/subscription/cancel', 'cancelSubscription')->name('plans.cancelSubscription');
    });
});


Route::controller(UserConferenceController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/conferences/joined/{user_id}', 'fetchJoinedConferences')->name('conferences.fetchJoinedConferences');

        Route::post('/conferences/join', 'joinConference')->name('conferences.joinConference');
        Route::post('/conferences/cancel', 'cancelParticipation')->name('conferences.cancelParticipation');
    });
});


Route::controller(UserLectureController::class)->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/lectures/favorited/{user_id}', 'fetchFavoriteLectures')->name('lectures.fetchFavoriteLectures');

        Route::get('/lectures/favorite/add/{user_id}/{lecture_id}', 'addFavoriteLecture')->name('lectures.addFavoriteLecture');
        Route::get('/lectures/favorite/remove/{user_id}/{lecture_id}', 'removeFavoriteLecture')->name('lectures.removeFavoriteLecture');
    });
});


Route::controller(StripeWebhookController::class)->group(function () {
    Route::post('webhooks/stripe', 'handleWebhook')->name('webhooks.handleWebhook');
});
