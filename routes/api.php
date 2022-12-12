<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LectureController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\ConferenceController;
use App\Http\Controllers\API\UserConferenceController;
use App\Http\Controllers\API\UserLectureController;
use App\Http\Controllers\API\CategoryController;

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

/* Auth Routes */
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/profile/update', [AuthController::class, 'update']);

    Route::get('/logout', [AuthController::class, 'logout']);
/* Auth Routes */


/* Conference Routes */
    Route::get('/conferences', [ConferenceController::class, 'fetchAll']);
    Route::get('/conferences/{id}', [ConferenceController::class, 'fetchDetail']);

    Route::post('/conferences/add', [ConferenceController::class, 'store']);

    Route::get('/conferences/{id}/edit', [ConferenceController::class, 'edit']);
    Route::post('/conferences/{id}/update', [ConferenceController::class, 'update']);

    Route::get('/conferences/{id}/delete', [ConferenceController::class, 'destroy']);
/* Conference Routes */


/* User Conferences Routes */
    Route::get('/conferences/joined/{user_id}', [UserConferenceController::class, 'fetchJoinedConferences']);
    Route::get('/conferences/join/{user_id}/{conference_id}', [UserConferenceController::class, 'joinConference']);
    Route::get('/conferences/cancel/{user_id}/{conference_id}', [UserConferenceController::class, 'cancelParticipation']);
/* User Conferences Routes */


/* Lecture Routes */
    Route::get('/lectures', [LectureController::class, 'fetchAll']);
    Route::get('/lectures/{id}', [LectureController::class, 'fetchById']);

    Route::post('/lectures/add', [LectureController::class, 'store']);
    Route::post('/lectures/{id}/update', [LectureController::class, 'update']);
    Route::get('/lectures/delete/{user_id}/{conference_id}', [LectureController::class, 'destroy']);
/* Lecture Routes */


/* User Lectures Routes */
    Route::get('/lectures/favorited/{user_id}', [UserLectureController::class, 'fetchFavoriteLectures']);

    Route::get('/lectures/favorite/add/{user_id}/{lecture_id}', [UserLectureController::class, 'addFavoriteLecture']);
    Route::get('/lectures/favorite/remove/{user_id}/{lecture_id}', [UserLectureController::class, 'removeFavoriteLecture']);
/* User Lectures Routes */


/* Comment Routes */
    Route::get('/comments/{lecture_id}/limit/{limit}/page/{page}', [CommentController::class, 'fetchByLectureId']);

    Route::post('/comments/add', [CommentController::class, 'store']);
    Route::post('/comments/{id}/update', [CommentController::class, 'update']);
/* Comment Routes */


/* Category Routes */
    Route::get('/category', [CategoryController::class, 'fetchAll']);
/* Category Routes */
