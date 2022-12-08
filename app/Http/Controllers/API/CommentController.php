<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Comment;

class CommentController extends Controller
{
    protected function validation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required'],
            'lecture_id' => ['required'],

            'description' => ['string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
    }


    protected function getUserComment($commentId)
    {
        return Comment::join('users', 'comments.user_id', '=', 'users.id')
                                ->select('comments.*', 'users.first_name', 'users.last_name')
                                ->where('comments.id', $commentId)
                                ->first();
    }


    public function fetchByLectureId($lectureId, $limit, $page)
    {
        $comments = Comment::where('lecture_id', $lectureId)
                            ->join('users', 'comments.user_id', '=', 'users.id')
                            ->select('comments.*', 'users.first_name', 'users.last_name')
                            ->skip($limit * ($page -1))
                            ->take($limit)
                            ->get();

        $res = [
            'comments' => $comments,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function store(Request $request)
    {
        CommentController::validation($request);

        $comment = Comment::create($request->all());
        $userComment = CommentController::getUserComment($comment->id);

        $res = [
            'comment' => $userComment,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::where('id', $id)->first();

        if (!$comment) {
            return response()->json(['error' => 'CommentController::update: Comment with the given id were not found.'], 404);
        }

        CommentController::validation($request);

        $comment->update($request->all());
        $userComment = CommentController::getUserComment($comment->id);

        $res = [
            'comment' => $userComment,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
