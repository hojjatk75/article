<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Services;

use App\Enums\CategoryStatus;
use App\Http\Requests\Category\IndexCategoryRequest;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryService
{
    /**
     * @param $validated_data
     * @return Category|false
     */
    public function store($validated_data) : Category|false
    {
        return Auth::user()->categories()->create([
            'title' => $validated_data['title'],
            'parent_id' => $validated_data['parent_id'] ?? null,
            'status' => $validated_data['status'] ?? CategoryStatus::ACTIVE
        ]);
    }

    /**
     * @param Category $category
     * @param $validated_data
     * @return bool
     */
    public function update(Category $category, $validated_data) : bool
    {
        $allowed_keys = ['title', 'status', 'parent_id'];
        $data = array_intersect_key($validated_data, array_flip($allowed_keys));


        return $category->update($data);
    }

    /**
     * @param Category $category
     * @return bool
     */
    public function destroy(Category $category) : bool
    {
        return $category->delete();
    }

    /**
     * @param IndexCategoryRequest $request
     * @return mixed
     */
    public function paginate(IndexCategoryRequest $request): mixed
    {
        $query = $this->_searchQuery($request);
        return $query->userId()->paginate($request->get('per_page', null));
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function homePaginate(Request $request): mixed
    {
        $query = $this->_searchQuery($request);
        return $query->active()->whereHas('articles')->paginate($request->get('per_page', null));
    }

    /**
     * @param IndexCategoryRequest|Request $request
     * @return Builder
     */
    private function _searchQuery(IndexCategoryRequest|Request $request): Builder
    {
        $query = Category::query();
        $title = $request->get('title');
        if(! empty($title)){
            $query->where(function ($sub_query) use ($title){
                foreach (explode(' ', $title) as $txt) {
                    $sub_query->orWhere('title', 'like', "%$txt%");
                }
            });
        }

        return $query;
    }
}
