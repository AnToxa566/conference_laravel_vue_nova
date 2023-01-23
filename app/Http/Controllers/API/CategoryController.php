<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use App\Models\Category;
use Illuminate\Http\JsonResponse;


class CategoryController extends Controller
{
    public function fetchAll(): JsonResponse
    {
        $categories = Category::withCount('conferences', 'lectures')->get();

        foreach ($categories as $category) {
            $children = $category->childs()->get();
            $category->{'children'} = count($children) ? $children : [];
        }

        return response()->json($categories);
    }
}
