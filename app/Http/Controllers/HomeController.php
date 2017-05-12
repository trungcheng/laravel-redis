<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function getIndex()
    {
        $user_new = User::where('user_type', 'like', 'User_%')->orderBy('created_at', 'desc')->take(8)->get();
        if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
            $articles = Article::join('users', 'article.creator', '=', 'users.email')
                ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.approve_by', 'article.type', 'article.status');
        } else {
            $articles = Article::join('users', 'article.creator', '=', 'users.email')
                ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.approve_by', 'article.type', 'article.status')->where('creator', auth()->user()->email);
        }
        $articles = $articles->orderBy('created_at', 'desc')->paginate(10);

        if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
            $articles_schedule = Article::join('users', 'article.creator', '=', 'users.email')
                ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.approve_by', 'article.type', 'article.status' ,  'article.thumbnail');
        } else {
            $articles_schedule = Article::join('users', 'article.creator', '=', 'users.email')
                ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.approve_by', 'article.type', 'article.status' ,  'article.thumbnail' )->where('creator', auth()->user()->email);
        }
        $articles_schedule = $articles_schedule->where('article.status' , 'schedule')->orderBy('created_at', 'desc')->paginate(4);

        $count_restaurants = Article::where('type', 'Review')->count();
        $count_recipes = Article::where('type', 'Recipe')->count();
        $count_blogs = Article::where('type', 'Blogs')->count();
        $count_users = User::where('user_type', 'like', 'User_%')->orderBy('created_at', 'desc')->count();
        $data['user_new'] = $user_new;
        $data['articles'] = $articles;
        $data['articles_schedule'] = $articles_schedule;
        $data['count_restaurants'] = $count_restaurants;
        $data['count_recipes'] = $count_recipes;
        $data['count_blogs'] = $count_blogs;
        $data['count_users'] = $count_users;
        return view('childs.home.index')->with($data);
    }

    function destroy()
    {
        $new = new \App\Helpers\UploadHandler();
    }
}
