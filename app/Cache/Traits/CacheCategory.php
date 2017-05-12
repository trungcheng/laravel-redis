<?php

namespace App\Cache\Traits;
use App\Models\Category;
trait CacheCategory
{
    function CacheCateTags($cate)
    {
        $redis = new \Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
        $connection = $redis;

        $id = $cate->id;

        $childs = Category::where('parent_id', $id)->get();
        if(count($childs)>0) {
            foreach ($childs as $child) {
                $ids_key = 'category_' . $child->id;
                if (isset($child->id) && $this->connection->get($ids_key) != null)
                    $childs_array [] = $child->id;
            }
        }
        $childs_json = json_encode([]);

        if (!empty($childs_array)) {
            $childs_json = json_encode($childs_array);
        }

        $cate->childs_json = $childs_json;

        $data_json = (string)$cate;
        $redis->set(env('REDIS_PREFIX') . '_category_' . $id, $data_json);
        $redis->close();
    }
}