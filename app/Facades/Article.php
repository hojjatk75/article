<?php
/**
 * Facade for articleService
 * @author Hojjat koochak zadeh
 */

namespace App\Facades;

use App\Http\Requests\Article\IndexArticleRequest;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;


/**
 * @method store($validated_data)
 * @method update(Article $article, $validated_data)
 * @method destroy(Article $article)
 * @method paginate(IndexArticleRequest $request)
 * @method homePaginate(Request $request)
 * @method publishArticles()
 * @see ArticleService
 */

class Article extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ArticleService::class;
    }
}
