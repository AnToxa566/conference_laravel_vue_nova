<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\User;
use Illuminate\Http\JsonResponse;


class UserLectureController extends Controller
{
    public function fetchFavoriteLectures(int $userId): JsonResponse
    {
        return response()->json(array_column(User::findOrFail($userId)->favoriteLectures()->get()->toArray(), 'id'));
    }


    public function addFavoriteLecture(int $userId, int $lectureId): JsonResponse
    {
        User::findOrFail($userId)->favoriteLectures()->attach($lectureId);

        return response()->json(null, 204);
    }


    public function removeFavoriteLecture(int $userId, int $lectureId): JsonResponse
    {
        User::findOrFail($userId)->favoriteLectures()->detach($lectureId);

        return response()->json(null, 204);
    }
}
