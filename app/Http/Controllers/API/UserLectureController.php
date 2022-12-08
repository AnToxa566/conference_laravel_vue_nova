<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Lecture;

class UserLectureController extends Controller
{
    public function fetchFavoriteLectures($userId)
    {
        $favoriteLectures = User::find($userId)->favoriteLectures()->get()->toArray();
        $lecturesId = array_column($favoriteLectures, 'id');

        $res = [
            'lecturesId' => $lecturesId,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }

    public function addFavoriteLecture($userId, $lectureId)
    {
        $user = User::find($userId);
        $lecture = Lecture::find($lectureId);

        $user->favoriteLectures()->attach($lecture);

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function removeFavoriteLecture($userId, $lectureId)
    {
        $user = User::find($userId);
        $lecture = Lecture::find($lectureId);

        $user->favoriteLectures()->detach($lecture);

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
