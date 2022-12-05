<?php

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


    public function fetchByLectureId($lecture_id)
    {
        $comments = Comment::where('lecture_id', $lecture_id)
                            ->join('users', 'comments.user_id', '=', 'users.id')
                            ->select('comments.*', 'users.first_name', 'users.last_name')
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

        $userComment = Comment::join('users', 'comments.user_id', '=', 'users.id')
                                ->select('comments.*', 'users.first_name', 'users.last_name')
                                ->where('comments.id', $comment->id)
                                ->first();

        $res = [
            'comment' => $userComment,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
