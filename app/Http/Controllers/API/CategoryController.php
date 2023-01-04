<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;

use App\Models\Category;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    protected function deleteChilds(Category $category): array
    {
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
        $categories = Category::withCount('conferences', 'lectures')->get();

        foreach ($categories as $category) {
            $children = $category->childs()->get();
            $category->{'children'} = count($children) ? $children : [];
        }

        return response()->json($categories);
    }


    public function store(CategoryStoreRequest $request): JsonResponse
    {
        $response = Category::create($request->validated());

        if (!$response) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $response->{"children"} = [];
        return response()->json($response);
    }


    public function destroy(int $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(Response::$statusTexts[Response::HTTP_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $deleteCats = CategoryController::deleteChilds($category);
        array_push($deleteCats, $category);

        $category->delete();

        return response()->json($deleteCats);
    }
}
