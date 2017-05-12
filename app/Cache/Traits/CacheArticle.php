<?php

namespace App\Cache\Traits;

use App\Models\Article;

trait CacheArticle
{

    protected $connection_article = '';

    function __construct()
    {
        $redis = new \Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        $this->connection_article = $redis;
    }

    function CreateArticle($article)
    {
        $this->cacheDetailArticle($article->id);
        $this->RelationCategory($article, 'create');
        $this->RelationTag($article, 'create');
        $this->RelationUser($article, 'create');
    }

    function ResetCategoryTag($article)
    {
        $this->RelationCategory($article);
        $this->RelationTag($article);
    }

    function GeoAdd($article = null)
    {
        if ($article != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $connection->rawCommand('GEOADD', env('REDIS_PREFIX') . '_map_restaurant', $article->longitude, $article->latitude, $article->id);
        }
    }


    function cacheDetailArticle($id = 208)
    {
        $redis = new \Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        $connection = $redis;
        $article = Article::with('getParent',
        'articleRating',
        'articleCategory',
        'articleSteps',
        'articleLocation',
        'articleGuess',
        'articlegallery',
        'articleOtherInfoReview',
        'articleFollow',
        'articleLike',
        'tags',
        'articleRating',
        'articleView',
        'articleComment',
        'getUser',
        'articleAdress',
        'related',
        'BuiltTopArticle',
        'getWardName',
        'getWard',
        'getIngredients',
        'getEvent',
        'articleOtherInfoRecipe')->find($id);
        $connection->set((string)env('REDIS_PREFIX') . '_article_' . $id, (string)$article);
        $connection->expire((string)env('REDIS_PREFIX') . '_article_' . $id, 3600 * 24 * 30);
        $connection->close();
    }

    function RelationCategory($article = null,  $action = 'remove', $key = '', $score = 'time')
    {
        if ($article != null) {
            if ($score == 'time') {
                $score = strtotime('2100-01-01') - strtotime($article->published_at);
            }
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $categories = $article->articleCategory()->get();
            if (count($categories) > 0) {
                foreach ($categories as $category) {
                    $key_category = $key == '' ? env('REDIS_PREFIX') . '_category_article_' . $category->id : env('REDIS_PREFIX') . '_category_article_' . $key . '_' . $category->id;
                    if ($action === 'remove') {
                        $connection->zDelete($key_category, (string)$article->id);
                    } else {
                        $connection->zDelete($key_category, (string)$article->id);
                        $connection->zAdd($key_category, $score, (string)$article->id);
                    }

                }
            }
            if ($action == 'remove') {
                $connection->zDelete(env('REDIS_PREFIX') . '_' . $article->type . '_article', $score, (string)$article->id);
            } else {
                $connection->zDelete(env('REDIS_PREFIX') . '_' . $article->type . '_article', $score, (string)$article->id);
                $connection->zAdd(env('REDIS_PREFIX') . '_' . $article->type . '_article', $score, (string)$article->id);
            }

        }
    }

    function RelationTag($article = null, $action = 'remove')
    {
        if ($article != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $tags = $article->tags()->get();
            if (count($tags) > 0) {
                foreach ($tags as $tag) {
                    $key_category = env('REDIS_PREFIX') . '_category_article_' . $tag->id;
                    if ($action == 'remove') {
                        $connection->zDelete($key_category, (string)$article->id);
                    } else {
                        $connection->zDelete($key_category, (string)$article->id);
                        $connection->zAdd($key_category, strtotime('2100-01-01') - strtotime($article->published_at), (string)$article->id);
                    }

                }
            }
        }
    }

    function IndexTitle($article)
    {

    }

    function RelationUser($article = null, $action = 'remove')
    {
        if ($article != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $user_id = $article->getUser->id;
            $key = env('REDIS_PREFIX') . '_user_article_' . $article->type . '_' . $user_id;
            if ($action == 'remove') {
                $connection->zDelete($key, (string)$article->id);
            } else {
                $connection->zDelete($key, (string)$article->id);
                $connection->zAdd($key, strtotime('2100-01-01') - strtotime($article->published_at), $article->id);
            }
        }
    }

}