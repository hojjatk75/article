<?php
/**
 * controller for manage category
 * @author Hojjat koochak zadeh
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\DeleteCategoryRequest;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Http\Requests\Category\ShowCategoryRequest;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use App\Traits\ReturnErrorMessageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class CategoryController extends Controller
{
    use ReturnErrorMessageTrait;

    /**
     * @param CategoryService $categoryService
     */
    public function __construct(
        protected CategoryService $categoryService
    )
    {
    }

    /**
     * @param IndexCategoryRequest $request
     * @return CategoryCollection
     */
    public function index(IndexCategoryRequest $request): CategoryCollection
    {
        $categories = $this->categoryService->paginate($request);
        return new CategoryCollection($categories);
    }

    /**
     * @param StoreCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->store($request->all());
        if($category){
            return Response::json([
                'message' => __('Category stored successfully!'),
                'category' => new CategoryResource($category)
            ]);
        }

        return $this->errorResponse();
    }

    /**
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $updated = $this->categoryService->update($category, $request->all());
        if($updated){
            return Response::json([
                'message' => __('Category updated successfully!'),
                'category' => new CategoryResource($category)
            ]);
        }

        return $this->errorResponse();
    }

    /**
     * @param DeleteCategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(DeleteCategoryRequest $request, Category $category): JsonResponse
    {
        $updated = $this->categoryService->destroy($category);
        if($updated){
            return Response::json([
                'message' => __('Category deleted successfully!'),
            ]);
        }

        return $this->errorResponse();
    }

    /**
     * @param ShowCategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function show(ShowCategoryRequest $request, Category $category): JsonResponse
    {
        return Response::json([
            'category' => new CategoryResource($category)
        ]);
    }
}
