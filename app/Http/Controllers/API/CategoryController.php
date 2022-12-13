<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;

class CategoryController extends Controller
{
    public function fetchAll()
    {
        $categories = Category::all();
        $subcategories = array();

        foreach ($categories as $category) {
            $children = $category->subcategory()->get();

            if (count($children) !== 0) {
                array_push($subcategories, (object) [
                    'category_id' => $category->id,
                    'children' => $children,
                ]);
            }
        }

        $res = [
            'categories' => $categories,
            'subcategories' => $subcategories,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'parent_id' => ['required', 'numeric'],
            'title' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $input = $request->all();
        $category = Category::create($input);

        $res = [
            'category' => $category,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }


    public function destroy($id)
    {
        $category = Category::where('id', $id)->first();

        if (!$category) {
            return response()->json(['error' => 'CategoryController::destroy: Category with the given id were not found.'], 404);
        }

        $category->subcategory()->delete();
        $category->delete();

        $res = [
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
