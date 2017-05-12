<?php

namespace App\Cache\Traits;

use App\Models\Product;
use App\Models\User;

trait CacheProduct
{
    function __construct()
    {
        $redis = new \Redis();
        $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
    }
    
    function CacheProduct($product)
    {
        $this->detailProduct($product);
        $this->userProduct($product, 'create');
    }

    function detailProduct($product = null)
    {
        if ($product != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;

            $key = env('REDIS_PREFIX') . '_product_detail_' . $product->id;
            if (isset($product)) {
                $connection->set($key, (string)$product);
                $connection->expire($key, 3600 * 24 * 30);
            }
            $connection->close();
        }
    }

    function userProduct($product = null, $action = 'remove')
    {
        if ($product != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $id_product = $product->id;
            $user_id = $product->getUser->id;
            $key = env('REDIS_PREFIX') . '_user_product_' . $user_id;

            if ($action == 'remove') {
                $connection->zDelete($key, (string)$id_product);
            } else {
                $connection->zAdd($key, strtotime('2100-01-01') - strtotime($product->created_at), $id_product);
            }
            $connection->close();
        }
    }
}