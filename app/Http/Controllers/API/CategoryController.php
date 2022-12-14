<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;

class CategoryController extends Controller
{
    protected function deleteChilds($category){
        $cats = [];

        foreach($category->childs as $child) {
            array_push($cats, $child);
        }

        for ($i = 0; $i < count($cats); $i++) {
            if ($cats[$i]->childs) {
                foreach($cats[$i]->childs as $child) {
                    array_push($cats, $child);
                }
            }
        }

        for ($i = 0; $i < count($cats); $i++) {
            $category = Category::where('id', $cats[$i]->id)->first();
            $category->delete();
        }

        return $cats;
   }


    public function fetchAll()
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            $children = $category->childs()->get();
            $category->{"children"} = count($children) !== 0 ? $children : array();
        }

        $res = [
            'categories' => $categories,
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

        $category->{"children"} = array();

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

        $deleteCats = CategoryController::deleteChilds($category);
        array_push($deleteCats, $category);

        $category->delete();

        $res = [
            'items' => $deleteCats,
            'status' => 'ok',
        ];

        return response()->json($res, 201);
    }
}
