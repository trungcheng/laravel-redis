<?php

namespace App\Cache;

use App\Cache\Traits\CacheArticle;
use App\Cache\Traits\CacheCategory;
use App\Cache\Traits\CacheCollection;
use App\Cache\Traits\CacheShopList;
use App\Cache\Traits\CacheUser;
use App\Cache\Traits\CacheProduct;
use Redis;
use App\Models\Article;
use App\Models\Category;
use App\User;

class CacheRedis
{
    use CacheCategory, CacheArticle, CacheUser, CacheCollection, CacheShopList, CacheProduct;
    protected $connection = '';
    private $cache;

    function __construct()
    {
        $redis = new Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        $this->connection = $redis;
        $this->cache = $redis;
    }

    function getConnection()
    {
        return $this->connection;
    }

    function getKey($key)
    {
        return $this->connection->get($key);
    }


    function RemoveKey($key)
    {
        $this->connection->delete($key);
    }

    function DeleteZKeyIndex($key, $index)
    {
        $this->connection->zDelete($key, (string)$index);
    }

    function cacheDetailCategoryTag($id)
    {
        $cate = Category::find($id);

        $childs = Category::where('parent_id', $id)->get();
        foreach ($childs as $child) {
            $ids_key = 'category_' . $child->id;
            if (isset($child->id) && $this->connection->get($ids_key) != null)
                $childs_array [] = $child->id;
        }
        $childs_json = json_encode([]);
        if (!empty($childs_array)) {
            $childs_json = json_encode($childs_array);
        }
        $cate->childs_json = $childs_json;

        $data_json = (string)$cate;
        $this->connection->set(env('REDIS_PREFIX') . '_category_' . $id, $data_json);
        $this->connection->close();
    }

    function cacheDetailUser($id)
    {
        $user = User::find($id);
        $this->connection->set(env('REDIS_PREFIX') . '_user_' . $id, (string)$user);
        $this->connection->close();
    }


}