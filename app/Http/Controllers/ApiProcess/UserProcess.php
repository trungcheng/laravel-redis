<?php

namespace App\Http\Controllers\ApiProcess;

use App\Models\User;
use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Models\MetaArticleFe as MetaArticle;
use App\Cache\Article\ArticleCache;
use App\Models\MetaUser;
use App\Models\Ingredients;
use App\Models\IngredientRelation;
use App\Repositories\Post\PostRepository;
use App\Models\Comment;
use App\Models\Rate;
use App\Models\Step;
use App\Models\ShopList;
use App\Models\MediaUser;
use App\Models\Question;
use App\Models\Device;
use App\Models\Notify;

class UserProcess extends Controller {

    public function __construct(PostRepository $article_interface) {
        $this->_post = $article_interface;
    }

    function anyCreate() {
        $input = \Input::all();
        $data = \Input::except('function');
        $data = json_decode($data['data']);
        $data = (get_object_vars(json_decode($data->data)));
        if (!in_array($data['user_type'], config('admincp.user_type_check'))) {
            $new = new User();
            foreach ($data as $k => $v) {
                if ($v == 'Admin' || $v == 'Editor' || $v == 'Normal') {
                    return response()->json(['status' => 403, 'state' => 'CA']);
                }
                if ($k != 'password') {
                    $new->$k = $v;
                } else {
                    $new->$k = $v;
                }
            }
            $new->api_token = str_random(10);
            if ($new->save()) {
                return response()->json(['status' => http_response_code(), 'id' => $new->id]);
            } else {
                return response()->json(['status' => 400]);
            }
        } else {
            return response()->json(['status' => 403]);
        }
    }

    function anyUpdate() {
        $input = \Input::all();
        $data = \Input::except('function');
        $data = json_decode($data['data']);
        $user = User::find($data->data->id);
        if (isset($data->data->name)) {
            $user->name = $data->data->name;
        } else {
            $user->password = bcrypt($data->data->password);
        }
        if (isset($data->data->thumbnail)) {
            $user->thumbnail = $data->data->thumbnail;
        }
        $user->save();
        if (isset($data->meta)) {
            $meta = $data->meta;
            $meta_user = MetaUser::where('meta_key', 'like', '%info_%')->where('user_id', $data->data->id)->delete();
            foreach ($meta as $k => $v) {
                $meta_user = new MetaUser ();
                $meta_user->meta_key = $k;
                $meta_user->meta_value = $v;
                $meta_user->user_id = $data->data->id;
                $meta_user->save();
            }
        }
        return response()->json(['status' => http_response_code()]);
    }

    function postLike() {
        $data = json_decode(\Request::get('data'));
        $key = 'like_article';
        $article_id = $data->article_id;
        $user_id = $data->user_id;
        if (MetaArticle::where('meta_key', $key)
                        ->where('article_id', $article_id)
                        ->where('meta_value', $user_id)
                        ->count() > 0
        ) {
            MetaArticle::where('meta_key', $key)
                    ->where('article_id', $article_id)
                    ->where('meta_value', $user_id)
                    ->delete();

            $cache = new ArticleCache();
            $cache->getById($article_id);
            return response()->json(['status' => http_response_code(), 'status_article' => 'dislike']);
        } else {
            $meta_fe = new MetaArticle();
            $meta_fe->meta_key = $key;
            $meta_fe->meta_value = $user_id;
            $meta_fe->article_id = $article_id;
            $meta_fe->save();
            $cache = new ArticleCache();
            $cache->getById($article_id);
            return response()->json(['status' => http_response_code(), 'status_article' => 'like']);
        }
    }

    function postVote() {
        $data = json_decode(\Request::get('data'));
        $key = 'vote_article';
        $article_id = $data->article_id;
        $user_id = $data->user_id;
        if (MetaArticle::where('meta_key', $key)
                        ->where('article_id', $article_id)
                        ->where('meta_value', $user_id)
                        ->count() > 0
        ) {
            MetaArticle::where('meta_key', $key)
                    ->where('article_id', $article_id)
                    ->where('meta_value', $user_id)
                    ->delete();

            $cache = new ArticleCache();
            $cache->getById($article_id);
            if (\Cache::has('event_count_vote_article_' . $article_id)) {
                $count = \Cache::get('event_count_vote_article_' . $article_id) - 1;
                \Cache::forever('event_count_vote_article_' . $article_id, $count);
            }

            return response()->json(['status' => http_response_code(), 'status_article' => 'unvote']);
        } else {
            $meta_fe = new MetaArticle();
            $meta_fe->meta_key = $key;
            $meta_fe->meta_value = $user_id;
            $meta_fe->article_id = $article_id;
            $meta_fe->save();
            $cache = new ArticleCache();
            $cache->getById($article_id);
            if (\Cache::has('event_count_vote_article_' . $article_id)) {
                $count = \Cache::get('event_count_vote_article_' . $article_id) + 1;
                \Cache::forever('event_count_vote_article_' . $article_id, $count);
            } else {
                $count = 1;
                \Cache::forever('event_count_vote_article_' . $article_id, $count);
            }
            return response()->json(['status' => http_response_code(), 'status_article' => 'vote']);
        }
    }

    function postRate() {
        $data = json_decode(\Request::get('data'));
        $article_id = $data->article_id;
        $creator = $data->email;
        $score = $data->rate;
        $content = isset($data->content) ? $data->content : null;
        $check = Rate::where('creator', $creator)
                ->where('article_id', $article_id)
                ->count();
        if ($check == 0) {
            $key = 'rate_article';
            $number_rate = $score;
            if (MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->count() > 0) {
                $rate = MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->first();
                $sessions = json_decode($rate->meta_value)->total_sessions + 1;
                $scores = json_decode($rate->meta_value)->total_scores + $number_rate;
                $rate->meta_value = json_encode(['total_scores' => $scores, 'total_sessions' => $sessions]);
                $rate->save();
            } else {
                $meta_fe = new MetaArticle();
                $meta_fe->meta_key = $key;
                $meta_fe->meta_value = json_encode(['total_scores' => $number_rate, 'total_sessions' => 1]);
                $meta_fe->article_id = $article_id;
                $meta_fe->save();
            }
            $rate = new Rate();
            $rate->article_id = $article_id;
            $rate->creator = $creator;
            $rate->score = $score;
            $rate->content = $content;
            $rate->save();
            $cache = new ArticleCache();
            $cache->getById($article_id);
            return response()->json(['status' => http_response_code(), 'status_rate' => 'Bạn đã đánh giá bài viết này thành công']);
        } else {
            return response()->json(['status' => 400, 'status_rate' => 'Bạn đã từng đánh giá bài viết này']);
        }
    }

    function postCreateComment() {
        $data = json_decode(\Request::get('data'), true);
        $check = 0;
        if ($check == 0) {
            $rate = new Rate();
            foreach ($data as $k => $v) {
                if ($k == 'email') {
                    $rate->creator = $v;
                } else {
                    $rate->$k = $v;
                }
            }
            if (isset($data['event_id'])) {
                $rate->article_id = null;
            } elseif (isset($data['article_id'])) {
                $rate->event_id = null;
            }

            $rate->save();
            return response()->json(['status' => http_response_code(), 'status_rate' => 'Bạn đã đánh giá bài viết này thành công']);
        } else {
            return response()->json(['status' => 400, 'status_rate' => 'Bạn đã từng đánh giá bài viết này']);
        }
    }

    function postFollow() {
        $data = json_decode(\Request::get('data'));
        $key = 'follow_user';
        $user_id_follow = $data->user_follow;
        $user_id = $data->user_id;
        if (MetaUser::where('meta_key', $key)->where('user_id', $user_id)->where('meta_value', $user_id_follow)->count() > 0) {
            MetaUser::where('meta_key', $key)->where('user_id', $user_id)->where('meta_value', $user_id_follow)->delete();
            return response()->json(['status' => http_response_code(), 'status_user' => 'unfollow']);
        } else {
            $meta_fe = new MetaUser();
            $meta_fe->meta_key = $key;
            $meta_fe->meta_value = $user_id_follow;
            $meta_fe->user_id = $user_id;
            $meta_fe->save();
            return response()->json(['status' => http_response_code(), 'status_user' => 'follow']);
        }
    }

    //Check in da den dia diem nha hang || da nau mon an nay
    function postCheckIn() {
        $data = json_decode(\Request::get('data'));
        $key = 'check_in';
        $article_id = $data->article_id;
        $user_id = $data->user_id;
        if (MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->where('meta_value', $user_id)->count() > 0) {
            MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->where('meta_value', $user_id)->delete();
            $cache = new ArticleCache();
            $cache->getById($article_id);
            return response()->json(['status' => http_response_code(), 'status_article' => 'un-check-in']);
        } else {
            $meta_fe = new MetaArticle();
            $meta_fe->meta_key = $key;
            $meta_fe->meta_value = $user_id;
            $meta_fe->article_id = $article_id;
            $meta_fe->save();
            $cache = new ArticleCache();
            $cache->getById($article_id);
            return response()->json(['status' => http_response_code(), 'status_article' => 'check-in']);
        }
    }

    function postComment() {
        $data = json_decode(\Request::get('data'));
        $comment = new Comment();
        foreach ($data as $k => $v) {
            $comment->$k = $v;
        }
        $comment->status = 0;
        $comment->save();
        $cache = new ArticleCache();
        $cache->getById($data->article_id);
        return response()->json(['status' => http_response_code()]);
    }

    function postLikeComment() {
        $data = json_decode(\Request::get('data'));
        $comment_id = $data->comment_id;
        $check = Comment::where('id', $comment_id)->where('liker', 'like', '%' . $data->user_email . '%')->count();
        if ($check > 0) {
            $comment = Comment::find($comment_id);
            $comment->liker = str_replace($data->user_email, '', $comment->liker);
            $comment->number_like = $comment->number_like - 1;
            $comment->save();
            return response()->json(['status' => 200, 'status_comment' => 'unlike']);
        } else {
            $comment = Comment::find($comment_id);
            $comment->number_like = $comment->number_like + 1;
            $comment->liker = $comment->liker . ',' . $data->user_email;
            if ($comment->save()) {
                return response()->json(['status' => http_response_code(), 'status_comment' => 'like']);
            } else {
                return response()->json(['status' => 400]);
            }
        }
    }

    function postDeleteComment() {
        $data = json_decode(\Request::get('data'));
        $comment_id = $data->comment_id;
        if (Comment::find($comment_id)) {
            $comment = Comment::find($comment_id);
            $article_id = $comment->article_id;
            if ($comment->delete()) {
                $cache = new ArticleCache();
                $cache->getById($article_id);
                return response()->json(['status' => http_response_code()]);
            } else {
                return response()->json(['status' => 400]);
            }
        } else {
            return response()->json(['status' => 400]);
        }
    }

    function postCreateRecipe() {
        $event = null;
        $data = json_decode(\Request::get('data'));
        $ingredients = $data->data_article->ingredients;
        $steps = $data->data_article->directions;

        $content = '';
        foreach ($steps as $k => $step) {
            $name_step = $k + 1;
            $content .= '<p><b>Bước ' . $name_step . '</b> : ' . $step->content . '</p>';
            foreach ($step->img_list as $image) {
                $content .= '<center><img  src="' . $image . '" width="475" ></center>';
            }
        }
        if (isset($data->data_article->yt))
            $content = $content . $data->data_article->yt;


        $article = new Article();
        foreach ($data->data_article as $k => $v) {
            if ($k != 'meta_data' && $k != 'yt' && $k != 'data_article' && $k != 'category' && $k != 'prep_time' && $k != 'cook_time' && $k != 'ingredients' && $k != 'directions' && $k != 'for_person')
                $article->$k = $v;
            if ($k == 'event_id' && $v == '') {
                $article->event_id = null;
            } elseif ($k == 'event_id' && $v != '') {
                $event = $v;
            }
            if ($k == 'creator')
                $creator = $v;
        }
        $article->content = $content;
        $article->status = 'draft';
        $article->slug = str_slug($article->title);

        if ($article->save()) {
            if (isset($data->data_article->yt))
                \Cache::forever('youtube_' . $article->id, $data->data_article->yt);
            $id = $article->id;
            foreach ($ingredients as $k => $items) {
                $ingredient_item = $items->name;
                if (Ingredients::whereName($ingredient_item)->count() == 0) {
                    $new_ingredient = new Ingredients();
                    $new_ingredient->name = $ingredient_item;
                    $new_ingredient->slug = str_slug($items->name);
                    $new_ingredient->save();
                } else {
                    $new_ingredient = Ingredients::whereName($ingredient_item)->first();
                }
                $relation_ingredients = new IngredientRelation();
                $relation_ingredients->article_id = $article->id;
                $relation_ingredients->ingredient_id = $new_ingredient->id;
                $relation_ingredients->quanlity = $items->quality;
                $relation_ingredients->quanlity_type = $items->quality_type;
                $relation_ingredients->save();
            }
            foreach ($data->meta_data as $k => $v) {
                if ($k == 'category') {
                    foreach ($v as $cate) {
                        $meta_insert = new \App\Models\MetaArticle ();
                        $meta_insert->meta_key = 'relation_category';
                        $meta_insert->meta_value = $cate;
                        $meta_insert->article_id = $article->id;
                        $meta_insert->save();
                    }
                } else {
                    $meta_insert = new \App\Models\MetaArticle();
                    $meta_insert->meta_key = 'recipe_' . $k;
                    $meta_insert->article_id = $article->id;
                    $meta_insert->meta_value = $v;
                    $meta_insert->save();
                }
            }
            foreach ($steps as $k => $step) {
                $name_step = $k + 1;
                $content = $step->content;
                $new_step = new Step();
                $new_step->number_step = $name_step;
                $new_step->content = $content;
                $new_step->article_id = $article->id;
                $new_step->gallery = json_encode($step->img_list);
                $new_step->save();
            }
        };

        $this->_post->getById($id);
        if ($event != null) {
            if (\Cache::has('event_article_' . $event)) {
                $data_article_event = \Cache::get('event_article_' . $event);
                array_unshift($data_article_event, $creator);
                $data_article_event = array_unique($data_article_event);
                \Cache::forever('event_article_' . $event, $data_article_event);
            } else {
                $data_article_event = [$creator];
                \Cache::forever('event_article_' . $event, $data_article_event);
            }
        }
        $job = new \App\Jobs\JobCacheArray($article->id, 'recipe', []);
        $this->dispatch($job);

        $job2 = new \App\Jobs\JobCache('recipe-home');
        $this->dispatch($job2);
        return response()->json(['status' => http_response_code(), 'id_article' => $article->id]);
    }

    function postEditRecipe() {
        $data = json_decode(\Request::get('data'));
        $ingredients = $data->data_article->ingredients;
        $steps = $data->data_article->directions;

        $content = '';
        if (!empty($steps)) {
            foreach ($steps as $k => $step) {
                $name_step = $k + 1;
                $content .= '<p><b>Bước ' . $name_step . '</b> : ' . $step->content . '</p>';
                foreach ($step->img_list as $image) {
                    $content .= '<center><img  src="' . $image . '" width="475" ></center>';
                }
            }
        }
        if (isset($data->data_article->yt))
            $content = $content . $data->data_article->yt;

        $article = Article::find($data->article_id);
        foreach ($data->data_article as $k => $v) {
            if ($k != 'article_id' && $k != 'creator' && $k != 'yt' && $k != 'meta_data' && $k != 'data_article' && $k != 'category' && $k != 'prep_time' && $k != 'cook_time' && $k != 'ingredients' && $k != 'directions' && $k != 'for_person'
            )
                $article->$k = $v;
            if ($k == 'event_id' && $v == '') {
                $article->event_id = null;
            }
        }
        $article->content = $content;
        $article->slug = str_slug($article->title);

        if ($article->save()) {
            if (isset($data->data_article->yt))
                \Cache::forever('youtube_' . $article->id, $data->data_article->yt);
            $id = $article->id;
            if (!empty($ingredients)) {
                IngredientRelation::where('article_id', $article->id)->delete();
                foreach ($ingredients as $k => $items) {
                    $ingredient_item = $items->name;
                    if (Ingredients::whereName($ingredient_item)->count() == 0) {
                        $new_ingredient = new Ingredients();
                        $new_ingredient->name = $ingredient_item;
                        $new_ingredient->slug = str_slug($items->name);
                        $new_ingredient->save();
                    } else {
                        $new_ingredient = Ingredients::whereName($ingredient_item)->first();
                    }
                    $relation_ingredients = new IngredientRelation();
                    $relation_ingredients->article_id = $article->id;
                    $relation_ingredients->ingredient_id = $new_ingredient->id;
                    $relation_ingredients->quanlity = $items->quality;
                    $relation_ingredients->quanlity_type = $items->quality_type;
                    $relation_ingredients->save();
                }
            }

            if (!empty($data->meta_data)) {
                $article->articleCategory()->detach();
                foreach ($data->meta_data as $k => $v) {
                    if ($k == 'category') {
                        foreach ($v as $cate) {
                            $meta_insert = new \App\Models\MetaArticle ();
                            $meta_insert->meta_key = 'relation_category';
                            $meta_insert->meta_value = $cate;
                            $meta_insert->article_id = $article->id;
                            $meta_insert->save();
                        }
                    } else {
                        $meta_insert = new \App\Models\MetaArticle();
                        $meta_insert->meta_key = 'recipe_' . $k;
                        $meta_insert->article_id = $article->id;
                        $meta_insert->meta_value = $v;
                        $meta_insert->save();
                    }
                }
            }
            if (!empty($steps)) {
                Step::where('article_id', $article->id)->delete();
                foreach ($steps as $k => $step) {
                    $name_step = $k + 1;
                    $content = $step->content;
                    $new_step = new Step();
                    $new_step->number_step = $name_step;
                    $new_step->content = $content;
                    $new_step->article_id = $article->id;
                    $new_step->gallery = json_encode($step->img_list);
                    $new_step->save();
                }
            }
        };

        $this->_post->getById($id);
        $job = new \App\Jobs\JobCacheArray($article->id, 'recipe', []);
        $this->dispatch($job);

        $job2 = new \App\Jobs\JobCache('recipe-home');
        $this->dispatch($job2);
        return response()->json(['status' => http_response_code()]);
    }

    function anyAddShopList() {
        $data = json_decode(\Request::get('data'));
        $user_id = isset($data->user_id) ? $data->user_id : null;
        $article_id = isset($data->article_id) ? $data->article_id : null;
        $ingredient_id = isset($data->ingredient_id) ? $data->ingredient_id : null;

        $ingredient_ids = explode(',', $ingredient_id);
        foreach ($ingredient_ids as $ingredient_id) {
            $check = IngredientRelation::where('article_id', $article_id)->where('ingredient_id', $ingredient_id)->count();
            if ($user_id != null && $article_id != null && $ingredient_id != null && $check > 0) {
                try {
                    $shop_list_old = ShopList::where('article_id', $article_id)
                            ->where('user_id', $user_id)
                            ->where('ingredient_id', $ingredient_id)
                            ->delete();

                    $shop_list = new ShopList();
                    $shop_list->user_id = $user_id;
                    $shop_list->article_id = $article_id;
                    $shop_list->ingredient_id = $ingredient_id;
                    $shop_list->saveOrFail();
                } catch (\Exception $e) {
                    return response()->json(['status' => 400, 'message' => $e->getMessage()]);
                }
            } else {
                return response()->json(['status' => 400]);
            }
        }
        return response()->json(['status' => 200]);
    }

    function anyStatusShopList() {
        $data = json_decode(\Request::get('data'));
        $user_id = isset($data->user_id) ? $data->user_id : null;
        $article_id = isset($data->article_id) ? $data->article_id : null;
        $ingredient_id = isset($data->ingredient_id) ? $data->ingredient_id : null;
        if ($user_id != null && $article_id != null && $ingredient_id != null) {
            try {
                $shop_list = ShopList::where('article_id', $article_id)
                        ->where('user_id', $user_id)
                        ->where('ingredient_id', $ingredient_id)
                        ->first();
                $shop_list->status = $shop_list->status == 0 ? 1 : 0;
                $shop_list->saveOrFail();
                return response()->json(['status' => 200, 'status_value' => $shop_list->status]);
            } catch (\Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
        } else {
            return response()->json(['status' => 400]);
        }
    }

    function anyDeleteShopList() {
        $data = json_decode(\Request::get('data'));
        $user_id = isset($data->user_id) ? $data->user_id : null;
        $article_id = isset($data->article_id) ? $data->article_id : null;
        if ($user_id != null && $article_id != null) {
            try {
                $shop_list = ShopList::where('article_id', $article_id)
                        ->where('user_id', $user_id)
                        ->delete();
                return response()->json(['status' => 200]);
            } catch (\Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
        } else {
            return response()->json(['status' => 400]);
        }
    }

    function postUploadFile() {
        $data = json_decode(\Request::get('data'));
        $media_path = isset($data->media_path) ? $data->media_path : null;
        $media_name = isset($data->media_name) ? $data->media_name : null;
        $creator = isset($data->email) ? $data->email : null;

        $media = new MediaUser();
        $media->media_path = $media_path;
        $media->media_name = $media_name;
        $media->creator = $creator;
        if ($media->save()) {
            return response()->json(['status' => 200]);
        } else {
            return response()->json(['status' => 400]);
        }
    }

    function postDeleteRecipe() {
        $data = json_decode(\Request::get('data'));
        $article = Article::findOrFail($data->article_id);
        if ($article->delete()) {
            return response()->json(['status' => 200]);
        } else {
            return response()->json(['status' => 400]);
        }
    }

    function postDeleteQuestions() {
        $data = json_decode(\Request::get('data'));
        $user_email = isset($data->user_email) ? $data->user_email : null;
        $questions_id = isset($data->questions_id) ? $data->questions_id : null;
        if ($user_id != null && $questions_id != null) {
            try {
                $questions = Question::where('answer_question', $questions_id)
                        ->where('creator', $user_id)
                        ->delete();
                return response()->json(['status' => 200]);
            } catch (\Exception $e) {
                return response()->json(['status' => 400, 'message' => $e->getMessage()]);
            }
        } else {
            return response()->json(['status' => 400]);
        }
    }

    function postRegisterDevice() {
        try {
            $data = json_decode(request()->get('data'));
            $device = new Device();
            foreach ($data as $k => $v) {
                $device->$k = $v;
            }
            $device->save();
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function postUpdateDevice() {
        try {
            $data = json_decode(request()->get('data'));
            $device = Device::where('deviceid', $data->deviceid)->first();
            foreach ($data as $k => $v) {
                if ($v != null)
                    $device->$k = $v;
            }
            $device->save();
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function postCreateQuestion() {
        try {
            $data = json_decode(request()->get('data'));
            $quest = new Question();
            foreach ($data as $k => $v) {
                $quest->$k = $v;
            }
            $quest->save();
            return response()->json(['status' => 200, 'id' => $quest->id]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function postUpdateQuestion() {
        try {
            $data = json_decode(request()->get('data'));
            $quest = Question::find($data->id);
            if ($quest->creator != $data->creator) {
                return response()->json(['status' => 400]);
            }
            foreach ($data as $k => $v) {
                $quest->$k = $v;
            }
            $quest->save();
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function postDeleteQuestion() {
        try {
            $data = json_decode(request()->get('data'));
            $quest = Question::find($data->id);
            if ($quest->creator == $data->creator)
                $quest->delete();
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function postCreateNotify() {
        try {
            $data = json_decode(request()->get('data'), true);
            $notify = new Notify ();
            foreach ($data as $v => $k) {
                $notify->$v = $k;
            }
            $notify->save();


            // Progress Cache
            $get_notify = \App\Models\Notify::with(['getUser', 'getArticle'])->where('id', $notify->id)->first();
            if (\Cache::has('count_notify_' . $data['user_receive'])) {
                \Cache::forever('count_notify_' . $data['user_receive'], \Cache::get('count_notify_' . $data['user_receive']) + 1);
            } else {
                \Cache::forever('count_notify_' . $data['user_receive'], 1);
            }
            \Cache::forever('notify_' . $notify->id, $get_notify);
            if (\Cache::has('notify_' . $data['user_receive'])) {
                $array_noti = \Cache::get('notify_' . $data['user_receive']);
                array_unshift($array_noti, $notify->id);
                \Cache::forever('notify_' . $data['user_receive'], $array_noti);
            } else {
                $array_noti = [$notify->id];
                \Cache::forever('notify_' . $data['user_receive'], $array_noti);
            }
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function postUpdateReadNotify() {
        try {
            $data = json_decode(request()->get('data'), true);
            if (\Cache::has('notify_') . $data['user_receive']) {
                $array_noti = \Cache::get('notify_' . $data['user_receive']);
                foreach ($array_noti as $noti) {
                    $data_noti = \Cache::get('notify_' . $noti);
                    if ($data_noti->status == 'Read')
                        break;
                    if (!empty($data_noti)) {
                        $data_noti->status = 'Read';
                        \Cache::forever('notify_' . $data_noti->id, $data_noti);
                    }
                }
            }
            if (\Cache::has('count_notify_' . $data['user_receive'])) {
                \Cache::forever('count_notify_' . $data['user_receive'], 0);
            }
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function postUpdateInfoRegisterEvent() {
        try {
            $data = json_decode(request()->get('data'), true);
            $user_id = $data['user_id'];
            $data_parser = $data['data'];
            $user = User::find($user_id);
            $new = new MetaUser();
            $new->meta_key = 'data_register';
            $new->user_id = $user->id;
            $new->meta_value = json_encode($data_parser);
            $new->save();
            return response()->json(['status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

    function getDeviceToken() {
        try {
            $data_devices = [];
            $devices = Device::select('token', 'user_id', 'devicename', 'os')
                    ->where('active', 1)
                     ->orderBy('id', 'desc')
                    ->paginate(10000);
            if (!empty($devices)) {
                foreach ($devices as $device) {
                    if (isset($device->token) && $device->token != null) {
                        $data_devices[] = array(
                            'User ID'=>$device->user_id,
                            'Device name'=>$device->devicename,
                            'OS'=>$device->os,
                            'Token'=>$device->token
                                );
                    }
                }
            }
            return response()->json($data_devices);
        } catch (\Exception $e) {
            return response()->json(['status' => 400]);
        }
    }

}
