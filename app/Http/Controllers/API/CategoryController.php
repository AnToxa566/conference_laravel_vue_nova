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
    protected function deleteCategoryChilds(Category $category): array
    {
        $categories = [];

        foreach($category->childs as $child) {
            array_push($categories, $child);
        }

        for ($i = 0; $i < count($categories); $i++) {
            if ($categories[$i]->childs) {
                foreach($categories[$i]->childs as $child) {
                    array_push($categories, $child);
                }
            }
        }

        for ($i = 0; $i < count($categories); $i++) {
            Category::destroy($categories[$i]->id);
        }

        return $categories;
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
        $createdCategory = Category::create($request->validated());

        if (!$createdCategory) {
            return response()->json(Response::$statusTexts[Response::HTTP_UNPROCESSABLE_ENTITY], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $createdCategory->{"children"} = [];
        return response()->json($createdCategory);
    }


    public function destroy(int $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $deletedCategories = CategoryController::deleteCategoryChilds($category);
        array_push($deletedCategories, $category);

        $category->delete();

        return response()->json($deletedCategories);
    }
}
