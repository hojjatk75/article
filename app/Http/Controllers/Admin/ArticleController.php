<?php
/**
 * controller for manage articles
 * @author Hojjat koochak zadeh
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\DeleteArticleRequest;
use App\Http\Requests\Article\IndexArticleRequest;
use App\Http\Requests\Article\ShowArticleRequest;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use App\Traits\ReturnErrorMessageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class ArticleController extends Controller
{
    use ReturnErrorMessageTrait;

    /**
     * @param ArticleService $articleService
     */
    public function __construct(
        protected ArticleService $articleService
    )
    {
    }

    /**
     * @param IndexArticleRequest $request
     * @return ArticleCollection
     */
    public function index(IndexArticleRequest $request): ArticleCollection
    {
        $articles = $this->articleService->paginate($request);
        return new ArticleCollection($articles);
    }

    /**
     * @param StoreArticleRequest $request
     * @return JsonResponse
     */
    public function store(StoreArticleRequest $request): JsonResponse
    {
        $article = $this->articleService->store($request->all());
        if($article){
            return Response::json([
                'message' => __('Article stored successfully!'),
                'article' => new ArticleResource($article)
            ]);
        }

        return $this->errorResponse();
    }

    /**
     * @param UpdateArticleRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function update(UpdateArticleRequest $request, Article $article): JsonResponse
    {
        $updated = $this->articleService->update($article, $request->all());
        if($updated){
            return Response::json([
                'message' => __('Article updated successfully!'),
                'article' => new ArticleResource($article)
            ]);
        }

        return $this->errorResponse();
    }

    /**
     * @param DeleteArticleRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function destroy(DeleteArticleRequest $request, Article $article): JsonResponse
    {
        $updated = $this->articleService->destroy($article);
        if($updated){
            return Response::json([
                'message' => __('Article deleted successfully!'),
            ]);
        }

        return $this->errorResponse();
    }

    /**
     * @param ShowArticleRequest $request
     * @param Article $article
     * @return JsonResponse
     */
    public function show(ShowArticleRequest $request, Article $article): JsonResponse
    {
        return Response::json([
            'article' => new ArticleResource($article)
        ]);
    }

}
