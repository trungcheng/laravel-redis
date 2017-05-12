<?php

namespace App\Providers;

use App\Cache\Article\ArticleCache;
use App\Models\Article;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('\App\Repositories\Post\PostRepository', '\App\Repositories\Post\EloquentPost');
        $this->app->bind('\App\Cache\Article\ArticleInterface', '\App\Cache\Article\ArticleCache');
    }
}
