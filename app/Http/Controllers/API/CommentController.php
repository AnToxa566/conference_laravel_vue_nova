<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CommentRequest;

use App\Models\Comment;
use App\Models\Lecture;
use Illuminate\Http\JsonResponse;

use App\Mail\CommentAdded;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function fetchByLectureId(int $lectureId, int $limit, int $page): JsonResponse
    {
        $lecture = Lecture::find($lectureId);

        if (!$lecture) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $offset = $limit * ($page - 1);
        $comments = $lecture->comments()->latest()->skip($offset)->take($limit)->get();

        foreach ($comments as $comment) {
            $comment->{'user_name'} = $comment->user->first_name . ' ' . $comment->user->last_name;
        }

        return response()->json($comments);
    }


    public function store(CommentRequest $request): JsonResponse
    {
        $response = Comment::create($request->validated());

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response->{'user_name'} = $response->user->first_name . ' ' . $response->user->last_name;

        Mail::to($response->lecture->user)->send(new CommentAdded($response));

        return response()->json($response);
    }


    public function update(CommentRequest $request, int $id): JsonResponse
    {
        $response = tap(Comment::find($id))->update($request->validated());

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $response->{'user_name'} = $response->user->first_name . ' ' . $response->user->last_name;

        return response()->json($response);
    }
}
