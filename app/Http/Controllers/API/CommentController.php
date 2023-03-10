<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Requests\Comment\CommentUpdateRequest;

use App\Models\Comment;
use App\Models\Lecture;
use App\Events\CommentCreated;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class CommentController extends Controller
{
    public function fetchByLectureId(int $lectureId, int $limit, int $page): JsonResponse
    {
        $lecture = Lecture::findOrFail($lectureId);

        $offset = $limit * ($page - 1);
        $comments = $lecture->comments()->latest()->skip($offset)->take($limit)->get();

        foreach ($comments as $comment) {
            $comment->{'user_name'} = $comment->user_name;
        }

        return response()->json($comments);
    }


    public function store(CommentStoreRequest $request): JsonResponse
    {
        $createdComment = Comment::create($request->validated());

        if (!$createdComment) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $createdComment->{'user_name'} = $createdComment->user_name;

        CommentCreated::dispatch($createdComment);

        return response()->json($createdComment);
    }


    public function update(CommentUpdateRequest $request, int $id): JsonResponse
    {
        $updatedComment = tap(Comment::findOrFail($id))->update($request->validated());

        $updatedComment->{'user_name'} = $updatedComment->user_name;

        return response()->json($updatedComment);
    }
}
