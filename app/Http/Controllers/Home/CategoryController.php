<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeCategoryCollection;
use App\Http\Resources\HomeCategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    )
    {
    }

    public function index(Request $request): HomeCategoryCollection
    {
        $categories = $this->categoryService->homePaginate($request);
        return new HomeCategoryCollection($categories);
    }

    public function show(Category $category): \Illuminate\Http\JsonResponse
    {
        return Response::json([
            'category' => new HomeCategoryResource($category)
        ]);
    }
}
