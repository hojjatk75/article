<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Services;

use App\Enums\ArticleStatus;
use App\Http\Requests\Article\IndexArticleRequest;
use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ArticleService
{
    /**
     * @param $validated_data
     * @return Article|false
     */
    public function store($validated_data) : Article|false
    {
        $status = $validated_data['status'] ?? ArticleStatus::DRAFT->value;
        $article = Auth::user()->articles()->create([
            'title' => $validated_data['title'],
            'content' => $validated_data['content'],
            'published_at' => ($status == ArticleStatus::SCHEDULED->value) ? $validated_data['published_at'] : null,
            'category_id' => $validated_data['category_id'],
            'status' => $status
        ]);
        $this->_clearSearchCache();
        return $article;
    }

    /**
     * @param Article $article
     * @param $validated_data
     * @return bool
     */
    public function update(Article $article, $validated_data) : bool
    {
        $allowed_keys = ['title', 'content', 'status', 'published_at', 'category_id'];
        $data = array_intersect_key($validated_data, array_flip($allowed_keys));

        $result = $article->update($data);
        $this->_clearSearchCache();
        return $result;
    }

    /**
     * @param Article $article
     * @return bool
     */
    public function destroy(Article $article) : bool
    {
        $result =  $article->delete();
        $this->_clearSearchCache();
        return $result;
    }

    /**
     * @param IndexArticleRequest $request
     * @return mixed
     */
    public function paginate(IndexArticleRequest $request): mixed
    {
        $cash_key = 'search_articles_' . md5(json_encode($request->all()));
        if(! Cache::has($cash_key)){
            $query = $this->_searchQuery($request);
            Cache::put($cash_key, $query->userId()->paginate($request->get('per_page')), now()->addMinutes(10));
        }

        return Cache::get($cash_key);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function homePaginate(Request $request): mixed
    {
        $cash_key = 'search_articles_' . md5(json_encode($request->all()));
        if(! Cache::has($cash_key)){
            $query = $this->_searchQuery($request);
            Cache::put($cash_key, $query->published()->paginate($request->get('per_page')), now()->addMinutes(10));
        }

        return Cache::get($cash_key);
    }

    /**
     * @return void
     */
    public function publishArticles(): void
    {
        Article::query()->where([['status', ArticleStatus::SCHEDULED->value], ['published_at', '<=', now()]])->update(['status' => ArticleStatus::PUBLISHED->value]);
        $this->_clearSearchCache();
    }

    /**
     * @param IndexArticleRequest|Request $request
     * @return Builder
     */
    private function _searchQuery(IndexArticleRequest|Request $request): Builder
    {
        $query = Article::query();

        $filters = [
            'category_id' => $request->get('category_id'),
            'status' => $request->get('status'),
            'user_id' => $request->get('user_id')
        ];

        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, $value);
            }
        }

        $searchFields = ['title', 'content'];

        foreach ($searchFields as $field) {
            $value = $request->get($field);
            if (!empty($value)) {
                $query->where(function ($sub_query) use ($field, $value) {
                    foreach (explode(' ', $value) as $txt) {
                        $sub_query->orWhere($field, 'like', "%$txt%");
                    }
                });
            }
        }

        return $query;
    }

    /**
     * @return void
     */
    private function _clearSearchCache(): void
    {
        Cache::flush();
    }
}
