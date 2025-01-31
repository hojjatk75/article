<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Providers;

use App\Services\ArticleService;
use Illuminate\Support\ServiceProvider;

class ArticleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ArticleService::class, function (){
            return new ArticleService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
