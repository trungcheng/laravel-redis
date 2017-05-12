<?php

/*
  |--------------------------------------------------------------------------
  | Application Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register all of the routes for an application.
  | It's a breeze. Simply tell Laravel the URIs it should respond to
  | and give it the controller to call when that URI is requested.
  |
 */

use App\Cache\CacheRedis;

Route::get('/', 'Auth\AuthController@getLogin');
Route::post('/', 'Auth\AuthController@postLogin');
Route::get('logout', 'Auth\AuthController@getLogout');


Route::get('/nha-hang/{category1?}/{category2?}/{category3?}/{category4?}/{category5?}/{category6?}/{category7?}/{category8?}/{category9?}/{category10?}/{category11?}/{category12?}', function () {

})->name('detail-review');

Route::get('/bai-viet/{category1?}/{category2?}/{category3?}/{category4?}/{category5?}/{category6?}/{category7?}/{category8?}/{category9?}/{category10?}/{category11?}/{category12?}', function () {

})->name('detail-blog');

Route::get('/tin-tuc/{category1?}/{category2?}/{category3?}/{category4?}/{category5?}/{category6?}/{category7?}/{category8?}/{category9?}/{category10?}/{category11?}/{category12?}', function () {

})->name('detail-tin-tuc');


Route::get('/view-cache', function () {
    if (request()->has('get')) {
        $new = new CacheRedis();
        $data = $new->getConnection()->get(request()->get('get'));
        dd(json_decode($data));
    } else if (request()->has('z')) {
        $new = new CacheRedis();
        $data = $new->getConnection()->zRevRange(request()->get('z'), 0, -1);
        dd($data);
    }

});

Route::get('/chi-tiet-cong-thuc/{category1?}/{category2?}/{category3?}/{category4?}/{category5?}/{category6?}/{category7?}/{category8?}/{category9?}/{category10?}/{category11?}/{category12?}', function () {

})->name('detail-recipe');

Route::group(['middleware' => ['auth']], function () {
    Route::get('home', 'HomeController@getIndex');

    Route::group(['prefix' => 'media'], function () {
        Route::controller('article', 'ArticleController', [
            'postCreate' => 'createArticle', 'postEdit' => 'editArticle',
        ]);
        Route::controller('location', 'LocationController');
        Route::controller('category', 'CategoryController');
        Route::controller('notification', 'NotificationController');
        Route::controller('food', 'FoodController');
        Route::controller('product', 'ProductController');
        Route::controller('recipe', 'RecipeController');
        Route::controller('blogs', 'BlogsController');
        Route::controller('comment', 'CommentController');
        Route::controller('collection', 'CollectionController');
        Route::controller('edit/{$id}', 'TraitController\ArticleDetail');
        Route::controller('delete/{$id}', 'TraitController\ArticleDetail');
        Route::controller('delete/{$id}', 'RecipeController');
        Route::controller('publish/{$id}', 'TraitController\ArticleDetail');
        Route::controller('publish/{$id}', 'RecipeController');
        Route::controller('built-top', 'BuilttopController');
    });
    Route::controller('article', 'ArticleController', [
        'postCreate' => 'createArticle', 'postEdit' => 'editArticle',
    ]);
    Route::controller('built-top', 'BuilttopController');
    Route::controller('collection', 'CollectionController');
    Route::controller('config', 'ConfigController');
    Route::controller('comment', 'CommentController');
    Route::controller('category', 'CategoryController');
    Route::controller('user', 'UserController');
    Route::get('quan-tri-vien', 'UserController@getQuanTriVien');
    Route::get('thanh-vien', 'UserController@getThanhVien');
    Route::get('profile', 'UserController@getProfile');
    Route::controller('questions', 'QuestionsController');
    Route::controller('gallery', 'GalleryController');
    Route::get('member/article', 'ArticleController@getArticleByMember');
    Route::get('/view-ga', 'ArticleController@getArticleViewGA');
    Route::controller('events', 'EventsController');
    Route::get('class-register', 'UserController@getClassRegister');

    Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'notify'], function () {
        Route::get('/', 'NotificationController@getIndex');
        Route::get('/create', 'NotificationController@getCreate')->name('create-notify');
        Route::post('/action-create', 'NotificationController@postCreate')->name('action-create-notify');
        Route::get('/edit/{id?}', 'NotificationController@getEdit')->name('edit-notify');
        Route::post('/edit/action-edit', 'NotificationController@postEdit')->name('action-edit-notify');
        Route::post('/delete', 'NotificationController@postDelete')->name('delete-notify');
        Route::get('/user', 'NotificationController@getPushUser')->name('user-push');
        Route::post('/push', 'NotificationController@PushNotify')->name('push-notify');
        Route::post('/push-all', 'NotificationController@PushNotifyAll')->name('push-notify-all');
    });
});

Route::get('locations', function () {
    if (\Request::has('ward')) {
        $province = \App\Models\Ward::where('name', 'like', '%' . \Request::get('q') . '%')->get();
        $json = [];
        foreach ($province as $items) {

            $json['items'][] = ['id' => $items->wardid, 'full_name' => $items->name, 'district_name' => $items->getDistrict->name, 'province_name' => $items->getDistrict->getProvince->name];
        }
    } elseif (\Request::has('district')) {
        $province = \App\Models\District::where('name', 'like', '%' . \Request::get('q') . '%')->get();
        $json = [];
        foreach ($province as $items) {
            $json['items'][] = ['id' => $items->districtid, 'full_name' => $items->name, 'province_name' => $items->getProvince->name];
        }
    } else {
        $province = \App\Models\Province::where('name', 'like', '%' . \Request::get('q') . '%')->get();
        $json = [];
        foreach ($province as $items) {
            $json['items'][] = ['id' => $items->provinceid, 'full_name' => $items->name];
        }
    }

    echo json_encode($json);
});
Route::get('cache-article', function () {
    $a = \App\Models\Article::where('id', '>=', request()->get('turn1'))->where('id', '<=', request()->get('turn2'))->get();
    $cache = new \App\Cache\Article\ArticleCache();
    foreach ($a as $article) {
        $cache->getById($article->id);
    }
});
Route::get('import-cache', function () {
    if (\Request::get('s') == 'review') {
        // NAP CACHE ID BAI VIET CUA TOAN BO CATEGORY NHA HANG
        $categories = \App\Models\Category::where('type_article', 'review')->get();
        foreach ($categories as $category) {
            $data = [];
            $article = $category->categoryArticle()->where(function ($reviews) {
                $reviews->where('status', 'publish');
                $reviews->Orwhere('published_at', '<=', date('Y-m-d H:i'));
            })->orderBy('published_at', 'desc')->orderBy('id', 'desc')->get();
            foreach ($article as $items) {
                $data[] = $items->id;
            }
            \Cache::forever(str_slug($category->title), $data);
        }
    } elseif (\Request::get('s') == 'recipe') {
        // NAP CACHE ALL RECIPE
        $recipe = App\Models\Article::select('id')->where('type', 'Recipe')->orderBy('published_at', 'desc')->orderBy('id', 'desc')->get();
        foreach ($recipe as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('recipe_all', $data);
    } elseif (\Request::get('s') == 'blog') {
        // NAP CACHE ALL RECIPE
        $blog = App\Models\Article::select('id')->where('type', 'Blogs')->orderBy('published_at', 'desc')->orderBy('id', 'desc')->get();
        foreach ($blog as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('blog_all', $data);
    } elseif (\Request::get('s') == 'review-home') {
        // NAP CACHE ALL RECIPE
        $review = App\Models\Article::select('*');
        $review = $review->where('type', 'Review')->where(function ($review) {
            $review->where('status', 'publish');
            $review->Orwhere('published_at', '<=', date('Y-m-d H:i'));
        });
        $review = $review->orderBy('published_at', 'desc')->orderBy('id', 'desc');
        $review = $review->skip(0)->take(6)->get();

        foreach ($review as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('review_home', $data);
    } elseif (\Request::get('s') == 'recipe-home') {
        $recipe = App\Models\Article::select('id', 'title', 'slug', 'thumbnail', 'description', 'type', 'creator');
        $recipe = $recipe->where('type', 'Recipe')->where(function ($recipe) {
            $recipe->where('status', 'publish');
            $recipe->Orwhere('published_at', '<=', date('Y-m-d H:i'));
        });
        $recipe = $recipe->orderBy('published_at', 'desc')->orderBy('id', 'desc');
        $recipe = $recipe->skip(0)->take(6)->get();
        foreach ($recipe as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('recipe_home', $data);
    } elseif (\Request::get('s') == 'blog-home') {
        $blogs = App\Models\Article::select('id', 'title', 'slug', 'thumbnail', 'description', 'type', 'creator');
        $blogs = $blogs->where('type', 'Blogs')->where(function ($blogs) {
            $blogs->where('status', 'publish');
            $blogs->Orwhere('published_at', '<=', date('Y-m-d H:i'));
        });
        $blogs = $blogs->orderBy('published_at', 'desc')->orderBy('id', 'desc');
        $blogs = $blogs->skip(0)->take(6)->get();
        foreach ($blogs as $item) {
            $data[] = $item->id;
        }
        \Cache::forever('blog_home', $data);
    } elseif (\Request::get('s') == 'builtop') {
        $builttop = App\Models\BuiltTop::select('name', 'link', 'link_img', 'post_id', 'type');
        $builttop = $builttop->where('status', 2);
        $builttop = $builttop->orderBy('position', 'asc');
        $builttop = $builttop->skip(0)->take(5)->get();
        foreach ($builttop as $item) {
            $data_type[] = $item->getArticle->type;
            $data_top[] = $item;
        }
        $data = [$data_type, $data_top];
        \Cache::forever('builtop', $data);
    } elseif (\Request::get('s') == 'user-top') {
        $data_user = [];
        $data_meta = [];
        $meta = App\Models\MetaArticleFe::selectRaw('* , count(id) as total ')
            ->where('meta_key', 'like_article')
            ->groupBy('meta_value')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();
        foreach ($meta as $item) {
            $data_user[] = $item->getUser;
            $data_meta[] = $item;
        }
        \Cache::forever('user_top', [$data_user, $data_meta]);
    } elseif (\Request::get('s') == 'collection_home_review') {
        $data_collection = [];
        $data_article = [];
        $data_user = [];
        $collections = App\Models\Collection::join('meta_collection', 'collection.id', '=', 'meta_collection.collection_id')
            ->select('collection.*')
            ->where('status', 1)
            ->where('type', 'Review')
            ->orderBy('id', 'desc')
            ->groupBy('collection_id')
            ->paginate(6);
        foreach ($collections as $items) {
            $data_article[] = $items->getArticle()->take(4)->orderBy('id', 'desc')->get();
            $data_user[] = $items->getUser;
            $data_collection[] = $items;
        }
        \Cache::forever('collection_Review', [$data_article, $data_collection, $data_user]);
    } elseif (\Request::get('s') == 'collection_home_recipe') {
        $data_collection = [];
        $data_article = [];
        $data_user = [];
        $collections = App\Models\Collection::join('meta_collection', 'collection.id', '=', 'meta_collection.collection_id')
            ->select('collection.*')
            ->where('status', 1)
            ->where('type', 'Recipe')
            ->orderBy('id', 'desc')
            ->groupBy('collection_id')
            ->paginate(6);
        foreach ($collections as $items) {
            $data_article[] = $items->getArticle()->take(4)->orderBy('id', 'desc')->get();
            $data_user[] = $items->getUser;
            $data_collection[] = $items;
        }
        \Cache::forever('collection_Recipe', [$data_article, $data_collection, $data_user]);
    } elseif (\Request::get('s') == 'get_all_category') {
        foreach (config('admincp.type_category') as $k => $v) {
            if ($v[1] == 'review') {
                $k == 'food_1' ? $type = 'food' : $type = $k;
                $data[$k] = \App\Models\Category::where('category.status', '1')
                    ->where('category.type', $type)
                    ->where('type_article', $v[1])
                    ->limit(12)
                    ->get();
                \Cache::forever('category_type_' . $k, $data[$k]);
            }
        }
    }
});

Route::resource('api', 'ApiController');
Route::controller('api-user', '\App\Http\Controllers\ApiProcess\UserProcess');
Route::controller('api-collection', '\App\Http\Controllers\ApiProcess\CollectionProcess');
Route::controller('api-question', '\App\Http\Controllers\ApiProcess\QuestionProcess');
Route::controller('api-product', '\App\Http\Controllers\ApiProcess\ProductProcess');
//Route::controller('api-article' , '\App\Http\Controllers\ApiProcess\ArticleProcess') ;
Route::any('upload-image', function () {
    $new = new App\Helpers\UploadHandler();
});
Route::resource('delete-image', 'HomeController');
Route::post('oauth/access_token', function () {
    return Response::json(Authorizer::issueAccessToken());
});

Route::get('test',function (){
    $redis = new Redis();
    $redis->connect(env('REDIS_HOST'), env('REDIS_PORT'));
    $allKeys = $redis->keys('*');
    print_r($allKeys);
});


