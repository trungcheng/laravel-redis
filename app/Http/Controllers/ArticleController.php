<?php

namespace App\Http\Controllers;

use App\Cache\CacheRedis;
use App\Models\Ingredients;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitController\ArticleDetail;
use App\Http\Controllers\TraitController\Tag;
use App\Repositories\Post\PostRepository;

class ArticleController extends Controller
{

    use ArticleDetail,
        Tag;

    public $article_on_page = 15;
    public $cache ;


    public function __construct(Request $request, PostRepository $article_interface)
    {
        $this->_request = $request;
        $this->_post = $article_interface;
        $this->cache = new CacheRedis()  ;
    }


    function getArticleViewGA()
    {
        $url_request = '';
        $data_article = [];
        $i = 1;
        $data_request = [];
        $request = request();
        $this->authorize('ViewArticle');
        $user_role = auth()->user()->user_type;
        $user_array = [
            'phucnt@traithivang.vn',
            'tuanpa@eyeplus.vn',
            'ducnt@eyeplus.vn'
        ];
        if ($user_role != 'Admin' && !in_array(auth()->user()->email, $user_array)) {
            abort(403);
        }
        GARequest();
        try {
            $request->flash();
            $category = Category::where('type_article', 'review')->orWhere('type_article', 'recipe')->get();
            if ($request->has('start_date') && $request->has('end_date') && $request->has('user_search')) {
                $start_date = date('Y-m-t 00:00:00', time());
                $start_end = date('Y-m-t 23:59:59', time());

                $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
                $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

                if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
                    $articles = Article::join('users', 'article.creator', '=', 'users.email')
                        ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.published_at', 'article.approve_by', 'article.type', 'article.status', 'article.parent_category', 'article.slug');
                } else {
                    $articles = Article::join('users', 'article.creator', '=', 'users.email')
                        ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.published_at', 'article.approve_by', 'article.type', 'article.status')->where('creator', auth()->user()->email);
                }
//
                if (request()->has('user_search')) {
                    $articles = $articles->where('creator', request()->get('user_search'));
                }
                $articles = $articles->where(function ($articles) {
                    $articles->where('users.user_type', 'Admin');
                    $articles->Orwhere('users.user_type', 'Editor');
                    $articles->Orwhere('users.user_type', 'Normal');
                });
//
                if ($request->has('key')) {
                    $keyword = $request->get('key');
                    $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                    $articles = $articles->whereRaw("article.title LIKE '%$keyword%'");
                }
//
                if ($request->has('type')) {
                    $type = $request->get('type');

                    if ($type === '0') {
                        $articles = $articles->whereRaw("(users.user_type = 'Admin' OR users.user_type = 'Editor' OR users.user_type = 'Normal')");
                    } else {
                        if ($type === 'RecipeOfMem') {
                            $articles = $articles->whereRaw("users.user_type = 'User_Vip' OR users.user_type = 'User_Normal'");
                        } else {
                            $articles = $articles->whereRaw("(users.user_type = 'Admin' OR users.user_type = 'Editor' OR users.user_type = 'Normal') AND article.type='$type'");
                        }
                    }
                }
//
                if ($request->old('start_date') && $request->old('end_date')) {
                    $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
                    $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

                    $start_date = date('Y-m-d H:i:s', strtotime($start_date));
                    $end_date = date('Y-m-d H:i:s', strtotime($end_date));
                    $articles = $articles->whereRaw("(article.created_at BETWEEN '" . $start_date . "' AND  '" . $end_date . "')");
                }
////
                if ($request->has('status')) {
                    if ($request->get('status') === 'trash') {
                        $articles = $articles->onlyTrashed();
                    } else {
                        $articles = $articles->where('article.status', $request->get('status'));
                    }
                }
//
                if ($request->has('category')) {
                    $cateid = $request->get('category');
                    $articles = $articles->join('meta_article', 'article.id', '=', 'meta_article.article_id')
                        ->where('meta_article.meta_value', $cateid)
                        ->where('meta_key', 'relation_category');
                }

                $articles = $articles->orderBy('article.published_at', 'desc')->orderBy('article.created_at', 'desc');
                $articles = $articles->take(300)->get();

                $url_request = '';
                $data_article = [];
                $i = 1;
                $data_request = [];
                foreach ($articles as $article) {
                    $blog = $article;
                    $blog_link = null;
                    $id = isset($blog->id) ? $blog->id : 1;
                    $title = isset($blog->title) ? $blog->title : '';
                    if ($blog->type == 'Review') {
                        $route_name = 'detail-review';
                    } elseif ($blog->type == 'Recipe') {
                        $route_name = 'detail-recipe';
                    } elseif ($blog->type == 'Blogs') {
                        $route_name = 'detail-blog';
                    }
                    $blog_link = route($route_name) . '/' . str_slug($blog->slug) . '_' . $id . '.html';
                    $blog_link = str_replace(url()->to('/'), '', genLink($blog, $route_name, $blog->slug, $id, $blog_link));
                    if ($i % 20 == 0) {
                        $json_code = GARequest(substr($url_request, 0, -1));
                        $url_request = '';
                        $res = isset($json_code->rows) ? $json_code->rows : [];
                        foreach ($res as $re) {
                            $key = !empty($re[0]) ? $re[0] : '';
                            $value = !empty($re[1]) ? $re[1] : '';
                            if (!empty($data_request[$key])) {
                                $data_request[$key] = $value + $data_request[$key];
                            } else {
                                $data_request[$key] = $value;
                            }

                        }
                    }
                    arsort($data_request);
                    $url_request .= 'ga:pagePath==' . $blog_link . ',';
                    $data_article[$blog_link] = $article;
                    $i++;
                }
            } else {
                $articles = Article::where('id', 0)->paginate(1);
            }
            $request_all = $request->all();
            if (!empty($articles)) {
                return view('childs.article.ga-view')
                    ->with('data_request', $data_request)
                    ->with('data_article', $data_article)
                    ->with('articles', $articles)
                    ->with('request_all', $request_all)
                    ->with('category', $category)
                    ->with('role', $user_role);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function getIndex(Request $request)
    {
        $this->authorize('ViewArticle');
        $user_role = auth()->user()->user_type;
        try {
            $request->flash();
            $category = Category::where('type_article', 'review')->orWhere('type_article', 'recipe')->get();

            $start_date = date('Y-m-t 00:00:00', time());
            $start_end = date('Y-m-t 23:59:59', time());

            $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
            $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

            if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
                $articles = Article::join('users', 'article.creator', '=', 'users.email')
                    ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.published_at', 'article.approve_by', 'article.type', 'article.status');
            } else {
                $articles = Article::join('users', 'article.creator', '=', 'users.email')
                    ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.published_at', 'article.approve_by', 'article.type', 'article.status')->where('creator', auth()->user()->email);
            }
//
            if (request()->has('user_search')) {
                $articles = $articles->where('creator', request()->get('user_search'));
            }
            $articles = $articles->where(function ($articles) {
                $articles->where('users.user_type', 'Admin');
                $articles->Orwhere('users.user_type', 'Editor');
                $articles->Orwhere('users.user_type', 'Normal');
            });
//
            if ($request->has('key')) {
                $keyword = $request->get('key');
                $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                $articles = $articles->whereRaw("article.title LIKE '%$keyword%'");
            }
//
            if ($request->has('type')) {
                $type = $request->get('type');

                if ($type === '0') {
                    $articles = $articles->whereRaw("(users.user_type = 'Admin' OR users.user_type = 'Editor' OR users.user_type = 'Normal')");
                } else {
                    if ($type === 'RecipeOfMem') {
                        $articles = $articles->whereRaw("users.user_type = 'User_Vip' OR users.user_type = 'User_Normal'");
                    } else {
                        $articles = $articles->whereRaw("(users.user_type = 'Admin' OR users.user_type = 'Editor' OR users.user_type = 'Normal') AND article.type='$type'");
                    }
                }
            }
//            
            if ($request->old('start_date') && $request->old('end_date')) {
                $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
                $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

                $start_date = date('Y-m-d H:i:s', strtotime($start_date));
                $end_date = date('Y-m-d H:i:s', strtotime($end_date));

                $articles = $articles->whereRaw("(article.created_at BETWEEN '" . $start_date . "' AND  '" . $end_date . "')");
            }
////
            if ($request->has('status')) {
                if ($request->get('status') === 'trash') {
                    $articles = $articles->onlyTrashed();
                } else {
                    $articles = $articles->where('article.status', $request->get('status'));
                }
            }
//
            if ($request->has('category')) {
                $cateid = $request->get('category');
                $articles = $articles->join('meta_article', 'article.id', '=', 'meta_article.article_id')
                    ->where('meta_article.meta_value', $cateid)
                    ->where('meta_key', 'relation_category');
            }

            $articles = $articles->orderBy('article.published_at', 'desc')->orderBy('article.created_at', 'desc');

            $request_all = $request->all();

            $articles = $articles->paginate(10);

            if (!empty($articles)) {
                return view('childs.article.index')->with('articles', $articles)->with('request_all', $request_all)->with('category', $category)->with('role', $user_role);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function getArticleByMember(Request $request)
    {
        $this->authorize('ViewArticle');
        $user_role = auth()->user()->user_type;
        try {
            $request->flash();
            $category = Category::where('type_article', 'review')->orWhere('type_article', 'recipe')->get();
            $event = Event::where('status', 1)->get();

            $start_date = date('Y-m-t 00:00:00', time());
            $start_end = date('Y-m-t 23:59:59', time());

            $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
            $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

            if (auth()->user()->user_type == 'Admin' || auth()->user()->user_type == 'Editor') {
                $articles = Article::join('users', 'article.creator', '=', 'users.email')
                    ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.approve_by', 'article.type', 'article.status', 'article.event_id');
            } else {
                $articles = Article::join('users', 'article.creator', '=', 'users.email')
                    ->select('article.id', 'article.title', 'article.creator', 'article.created_at', 'article.approve_by', 'article.type', 'article.event_id', 'article.status')->where('creator', auth()->user()->email);
            }
//
            $articles = $articles->where(function ($articles) {
                $articles->where('users.user_type', 'User_Normal');
//                $articles->Orwhere('users.user_type', 'Editor');
//                $articles->Orwhere('users.user_type', 'User-Normal');
            });
//
            if ($request->has('key')) {
                $keyword = $request->get('key');
                $keyword = preg_replace('/\s\s+/', ' ', trim($keyword));
                $articles = $articles->whereRaw("article.title LIKE '%$keyword%'");
            }
//
            if ($request->has('type')) {
                $type = $request->get('type');
                if ($type === '0') {
                    $articles = $articles->whereRaw("(users.user_type = 'User_Vip' OR users.user_type = 'User_Normal')");
                } else {
                    $articles = $articles->whereRaw("(users.user_type = 'User_Vip' OR users.user_type = 'User_Normal') AND article.type='$type'");
                }
            }
//            
            if ($request->old('start_date') && $request->old('end_date')) {
                $start_date = $request->has('start_date') ? $request->old('start_date') : $start_date;
                $end_date = $request->has('end_date') ? $request->old('end_date') : $start_end;

                $start_date = date('Y-m-d H:i:s', strtotime($start_date));
                $end_date = date('Y-m-d H:i:s', strtotime($end_date));

                $articles = $articles->whereRaw("(article.created_at BETWEEN '" . $start_date . "' AND  '" . $end_date . "')");
            }
////
            if ($request->has('status')) {
                if ($request->get('status') === 'trash') {
                    $articles = $articles->onlyTrashed();
                } else {
                    $articles = $articles->where('article.status', $request->get('status'));
                }
            }
//
            if ($request->has('category')) {
                $cateid = $request->get('category');
                $articles = $articles->join('meta_article', 'article.id', '=', 'meta_article.article_id')
                    ->where('meta_article.meta_value', $cateid)
                    ->where('meta_key', 'relation_category');
            }

            if ($request->has('event')) {
                $event_id = $request->get('event');
                $articles = $articles->where('event_id', $event_id);
            }


            $articles = $articles->orderBy('article.created_at', 'desc');

            $request_all = $request->all();

            $articles = $articles->paginate(10);

            if (!empty($articles)) {
                return view('childs.article.member_article')->with('articles', $articles)->with('request_all', $request_all)->with('category', $category)->with('event', $event)->with('role', $user_role);
            } else {
                echo 'no data';
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function getReview($id)
    {
        $this->authorize('ViewArticle');
        $article = Article::findOrFail($id);
        return view('childs.article.review')->with(['article' => $article]);
    }

    public function postUpdateStatus(Request $request)
    {
        $this->authorize('ActiveArticle');
        $article_id = Article::findOrFail($request->get('id'));

        $status = 'error';
        $msg = trans('article.update_status_fail');
        try {
            $article_id->status = $request->get('st');
            if ($article_id->save()) {
                $status = 'success';
                $msg = trans('article.update_status_success');
            }
            \Cache::flush();
            return json_encode(['status' => $status, 'msg' => $msg]);
        } catch (\Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    protected function getNameCategory($id)
    {
        try {
            $title = '';
            $cateTitle = Category::select('title')
                ->where('id', '=', $id)
                ->get();
            if (!empty($cateTitle)) {
                foreach ($cateTitle as $value) {
                    $title .= $value->title;
                }
                return $title;
            } else {
                return FALSE;
            }
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function getCheck()
    {
        //use App\Helpers\SimpleImage;
    }

    function getSearchIngredients()
    {
        $array = Ingredients::whereName(\Input::get('term'))->get();
        foreach ($array as $items) {
            $data [] = $items->name;
        }
        return $data;
    }

}
