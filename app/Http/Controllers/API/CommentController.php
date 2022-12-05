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

            'published_at' => ['required', 'date'],
            'description' => ['string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }
    }


    public function fetchByLectureId($lecture_id)
    {
        $comments = Comment::where('lecture_id', $lecture_id)->get();

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

        $res = [
            'comment' => $comment,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
