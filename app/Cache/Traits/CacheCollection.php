<?php

namespace App\Cache\Traits;

use App\Models\User;
use App\Models\Collection;

trait CacheCollection
{
    function UserCollection($collection = null, $action = 'remove')
    {
        if ($collection != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $id_collection = $collection->id;
            $user_id = $collection->getUser->id;
            $key = env('REDIS_PREFIX') . '_user_collection_' . $user_id;

            if ($action == 'remove') {
                $connection->zDelete($key, (string)$id_collection);
            } else {
                $connection->zAdd($key, strtotime('2100-01-01') - strtotime($collection->published_at), $id_collection);
            }
            $connection->close();
        }
    }

    function DetailCollection($collection = null)
    {
        if ($collection != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;

            $collection->collectionRelate;
            $collection->getArticle;
            $collection->getUser;

            //$data = \App\Models\Collection::with('collectionRelate', 'getArticle', 'getUser')->findOrfail($id_collection);
            $key = env('REDIS_PREFIX') . '_collection_detail_' . $collection->id;
            if (isset($collection)) {
                $connection->set($key, (string)$collection);
                $connection->expire($key, 3600 * 24 * 30);
            }
            $connection->close();
        }
    }

    function CollectionArticle($collection = null, $article = null, $action = "remove")
    {
        if ($collection != null && $article != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $id_collection = $collection->id;
            $article_id = $article->id;

            $key = env('REDIS_PREFIX') . '_collection_article_' . $id_collection;

            if ($action == 'remove') {
                $connection->zDelete($key, (string)$article_id);
            } else {
                $connection->zDelete($key, (string)$article_id);
                $connection->zAdd($key, strtotime('2100-01-01') - strtotime($article->published_at), $article_id);
            }
            $connection->close();
        }
    }
}