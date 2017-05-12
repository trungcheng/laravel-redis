<?php
/**
 * Created by PhpStorm.
 * User: phucnt
 * Date: 18/12/2015
 * Time: 13:16
 */
namespace App\Repositories\Post;

use App\Models\Post;
use App\Models\Article;
use App\Cache\Article\ArticleInterface;

class EloquentPost implements PostRepository
{
    function __construct(ArticleInterface $cache)
    {
        $this->_cache_article = $cache;
    }

    function getAllPostPage($page, $user)
    {
        if (Post::paginate((int)$page)) {
            if ($user == 'all') {
                return Post::orderBy('created_at', 'DESC')->paginate((int)$page);
            } else {
                return Post::orderBy('created_at', 'DESC')->where('user_id', '=', $user)->paginate((int)$page);
            }
        }
        return false;
    }

    function getAllPost()
    {
        $list_post = $this->post->all();
        if ($list_post != null) {
            return $list_post;
        }
        return false;
    }

    function findPostbyId($id)
    {
        $post = $this->post->find($id);
        if ($post) {
            return $post;
        }
        return false;
    }

    function UpdatePost($id, $array)
    {
        $post = $this->post->find($id);
        if ($post != null) {
            foreach ($array as $k => $v) {
                $post->$k = $v;
            }
            if ($post->save()) {
                return true;
            }
        }
        return false;
    }

    public function DeletePost($id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
        }
        return false;
    }

    function getPostWhere($where, $take, $order_by)
    {
        $post = $this->post->whereRaw($where_array)->take($take)->orderByRaw($order_by)->get();
        return $post;
    }

    function getPostPaginate($array, $paginate)
    {
        return $post = $this->post->whereRaw($where_array)->orderByRaw($order_by)->paginate($paginate);
    }

    function InsertPost($array)
    {
        $post = $this->post;
        foreach ($array as $k => $v) {
            $post->$k = $v;
        }
        if ($post->save()) {
            return true;
        } else {
            return false;
        }
    }

    function getPostbyCategory()
    {
        return $this->post->with('category')->get();
    }

    public function getSearch($keyword, $page, $user)
    {
        $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
        if ($user != 'all') {
            $post = Post::where('post_name', 'regexp', '/' . $keyword . '/i')
                ->where('user_id', '=', $user)
                ->orderBy('post_name', 'ASC')
                ->paginate((int)$page);
        } else {
            $post = Post::where('post_name', 'regexp', '/' . $keyword . '/i')
                ->orderBy('post_name', 'ASC')
                ->paginate((int)$page);
        }
        return $post;
    }


    public function ActivePost($id, $status)
    {
        $post = Post::find($id);
        if ($post) {
            $post->post_status = $status;
            $post->save();
        } else {
            return false;
        }

    }

    public function getListPost($page, $user)
    {
        if (Post::paginate((int)$page)) {
            if ($user == 'all') {
                return Post::orderBy('created_at', 'DESC')->paginate((int)$page);
            } else {
                return Post::orderBy('created_at', 'DESC')->where('user_id', '=', $user)->paginate((int)$page);
            }
        }
        return false;
    }

    function getById($id)
    {
        $article = Article::with(
            'articleCategory',
            'articleSteps',
            'articleLocation',
            'articleGuess',
            'articlegallery',
            'articleOtherInfoReview',
            'articleOtherInfoRecipe',
            'articleFollow',
            'articleLike',
            'tags', 'articleRating', 'articleView', 'articleComment', 'getUser', 'articleAdress', 'related', 'BuiltTopArticle', 'getWardName', 'getWard', 'getIngredients'
        )->whereId($id)->first();
        \Cache::put('article_' . $id, $article, 43200);
        return $article;
    }

    function getWhereInArticle($data_id, $take = null)
    {
        $i = 1;
        foreach ($data_id as $items) {
            $article = $this->getById($items);
            $articles [] = $article;
            if ($take != null && $i == 5) {
                return $articles;
            }
            $i++;
        }
        return $articles;
    }


}