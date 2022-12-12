<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
}
