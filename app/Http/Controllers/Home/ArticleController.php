<?php

namespace App\Http\Controllers\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\HomeArticleCollection;
use App\Http\Resources\HomeArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService
    )
    {
    }

    public function index(Request $request): HomeArticleCollection
    {
        $articles = $this->articleService->homePaginate($request);
        return new HomeArticleCollection($articles);
    }

    public function show(Article $article): \Illuminate\Http\JsonResponse
    {
        return Response::json([
            'article' => new HomeArticleResource($article)
        ]);
    }

}
