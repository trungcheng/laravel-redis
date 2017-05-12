<?php

namespace App\Cache\Traits;

use Hoa\Exception\Exception;
use Redis;
use DB;

trait CacheShopList
{
    function CacheShopList($user = null)
    {
        if($user != null){
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));

            $user->shopList;

            $list_data = [];
            $data = [];
            if(isset($user->shopList) && count($user->shopList)>0) {
                $list = $user->shopList;
                $global = [];
                foreach ($list as $item) {
                    if(!in_array($item->article_id,$global)){
                        $global[] = $item->article_id;
                    };

                    $list_data[$item->article_id][] = $item->getIngreDient;
                }

                foreach ($global as $item){
                    $data[] = json_encode(['article_id'=>$item,'ingredient'=>$list_data[$item]]);
                }
                $key = env('REDIS_PREFIX') . '_user_shoplist_' . $user->id;
                $data = json_encode($data);
                $redis->set($key,(string)$data);
                $redis->close();
            }
        }
    }
}