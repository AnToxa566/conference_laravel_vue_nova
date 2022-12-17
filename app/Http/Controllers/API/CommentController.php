<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CommentRequest;

use App\Models\Comment;
use App\Models\Lecture;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function fetchByLectureId(int $lectureId, int $limit, int $page): JsonResponse
    {
        $lecture = Lecture::find($lectureId);

        if (!$lecture) {
            return response()->json('Error! Please, try again.', 500);
        }

        $offset = $limit * ($page - 1);
        $response = $lecture->comments()->latest()->skip($offset)->take($limit)->get();

        foreach ($response as $comment) {
            $comment->{'first_name'} = $comment->user->first_name;
            $comment->{'last_name'} = $comment->user->last_name;
        }

        return response()->json($response);
    }


    public function store(CommentRequest $request): JsonResponse
    {
        $response = Comment::create($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        $response->{'first_name'} = $response->user->first_name;
        $response->{'last_name'} = $response->user->last_name;

        return response()->json($response);
    }


    public function update(CommentRequest $request, int $id): JsonResponse
    {
        $response = tap(Comment::find($id))->update($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        $response->{'first_name'} = $response->user->first_name;
        $response->{'last_name'} = $response->user->last_name;

        return response()->json($response);
    }
}
