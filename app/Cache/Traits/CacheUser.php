<?php

namespace App\Cache\Traits;

use App\Models\User;
use Redis;

trait CacheUser
{
    function CacheUser($user = null)
    {
        if ($user != null) {
            $redis = new \Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $id = $user->id;
            $user->getDevice;
            if (isset($user)) {
                $data_json = (string)$user;
                $redis->set(env('REDIS_PREFIX') . '_user_' . $id, $data_json);
                $redis->close();
            }
        }
    }

    function CacheUserFavorite($favorite = null, $action = 'create')
    {
        if ($favorite != null) {
            $redis = new Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
            $connection = $redis;
            $user_id = $favorite->meta_value;
            $article_id = $favorite->article_id;
            $article = $favorite->getArticle;
            $key = env('REDIS_PREFIX') . '_user_favorite_' . $user_id;
            $key_type = env('REDIS_PREFIX') . '_user_favorite_' . $article->type . '_' . $user_id;
            if ($action == 'remove') {
                $connection->zDelete($key, (string)$article_id);
            } else {

                $connection->zAdd($key, strtotime('2100-01-01') - strtotime($favorite->getArticle->published_at), (string)$article_id);
                $connection->zAdd($key_type, strtotime('2100-01-01') - strtotime($favorite->getArticle->published_at), (string)$article_id);
            }
        }
    }

    function CacheComment($comment = null, $action = 'create')
    {
        if ($comment != null) {
            $redis = new Redis();
            $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));

            $key = env('REDIS_PREFIX') . '_article_comment_' . $comment->article_id;

            $parent_id  = $comment->parent_id;
            if($parent_id == null){
                $children = \App\Models\Comment::where('parent_id',$comment->id)->get();
                if ($action == 'remove') {
                    $redis->zDelete($key, (string)$comment);
                } else{
                    $redis->zAdd($key, strtotime('2100-01-01') - strtotime($comment->created_at), (string)$comment);
                }
            }

            $redis->close();
        }
    }
}