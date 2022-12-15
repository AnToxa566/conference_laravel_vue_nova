<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected function deleteChilds(Category $category): array {
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


    public function fetchAll(): JsonResponse
    {
        $categories = Category::all();

        foreach ($categories as $category) {
            $children = $category->childs()->get();
            $category->{'children'} = count($children) !== 0 ? $children : array();
        }

        return response()->json($categories);
    }


    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $response = Category::create($request->validated());

        if (!$response) {
            return response()->json('Error! Please, try again.', 500);
        }

        $response->{"children"} = array();
        return response()->json($response);
    }


    public function destroy(int $id): JsonResponse
    {
        $category = Category::where('id', $id)->first();

        if (!$category) {
            return response()->json('Error! Please, try again.', 500);
        }

        $deleteCats = CategoryController::deleteChilds($category);
        array_push($deleteCats, $category);

        $category->delete();

        return response()->json($deleteCats);
    }
}
