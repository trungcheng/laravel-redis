<?php

namespace App\Http\Controllers;

use App\Cache\CacheRedis;
use App\Models\Category;
use App\Models\MetaArticle;
use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Models\Ingredients;
use App\Models\IngredientRelation;
use App\Repositories\Post\PostRepository;
use App\Models\Step;
use App\Models\Event;

class RecipeController extends Controller
{

    protected $cache;

    public function __construct(PostRepository $article_interface)
    {
        $this->_post = $article_interface;
        $this->cache = new CacheRedis();
    }

    public function getCreate()
    {
        $this->authorize('CreateArticle');
        foreach (config('admincp.type_category') as $k => $v) {

            if ($k === 'food_2') {
                $data['food_2'] = Category::where('type', 'food')->where('type_article', 'recipe')->get();
            } else {
                $data[$k] = Category::where('type', $k)->get();
            }
        }
        $data[$k] = Category::where('type', $k)->get();
        $data['level'] = Category::where('type', 'level')->where('type_article', 'recipe')->get();
        return view('childs.recipe.create')->with($data);
    }

    function postCreate()
    {
        $this->authorize('CreateArticle');
        try {
            $data_article = \Input::except('_token', 'category', 'seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'publish_time', 'publish_date', 'prep_time', 'cook_time', 'ingredients', 'directions', 'quanlity', 'quanlity_type', 'steps', 'files_steps', 'youtube');
            $meta_data = \Input::only(['prep_time', 'cook_time', 'directions', 'seo_title', 'seo_meta', 'seo_description', 'category', 'tags', 'related', 'gallery']);
            $ingredients = \Input::get('ingredients');
            $quanlity = \Input::get('quanlity');
            $quanlity_type = \Input::get('quanlity_type');
            $steps = \Input::get('steps');
            $files_image = \Input::get('files_steps');
            $files_image_array = json_decode($files_image);
            $content = '';
            $youtube = \Input::get('youtube');


            $parent = $data_article['parent_category'];
            $category = Category::find($parent);
            $cat = json_decode($meta_data['category']);
            $cat[] = $category->id;
            $check = false;
            $parent_id = $category->parent_id;
            while (true) {
                if ($parent_id != 0) {
                    $child = Category::find($parent_id);
                    $cat[] = $child->id;
                    $parent_id = $child->parent_id;
                } else {
                    break;
                }
            }
            $cat = array_unique($cat);
            $meta_data['category'] = json_encode($cat);


            foreach ($steps as $k => $step) {
                $name_step = $k + 1;
                $content .= '<p><b>Bước ' . $name_step . '</b> : ' . $step . '</p>';
                foreach ($files_image_array[$k] as $image) {
                    $content .= '<center><img  src="' . $image . '" width="475" ></center>';
                }
            }
            $content = $content . '<p><center>' . $youtube . '</center></p>';
            $data_article['content'] = $content;
            $article = new Article();
            foreach ($data_article as $k => $v) {
                $article->$k = $v;
            }
            $data = array();
            if (\Input::get('gallery') != '') {
                $folder_gallery = public_path() . \Input::get('gallery');
                if (is_dir($folder_gallery)) {
                    foreach (scandir($folder_gallery) as $k => $items) {
                        if ($k >= 3) {
                            $data[] = str_replace('//', '/', $folder_gallery . '/' . $items);
                        }
                    }
                    $gallery_path = json_encode($data);
                } else {
                    $gallery_path = '';
                }
            } else {
                $gallery_path = '';
            }
            if (Input::get('status') == '') {
                $article->status = 'schedule';
            }
            if (Input::get('status') == '') {
                $datetime_publish = date('Y-m-d H:i', strtotime(\Input::get('publish_date') . ' ' . \Input::get('publish_time')));
                $article->published_at = $datetime_publish;
            } elseif (Input::get('status') == 'publish') {
                $article->published_at = date('Y-m-d H:i:s');
            }
            $article->slug = str_slug(\Input::get('title'));
            $article->gallery_path = $gallery_path;
            $article->creator = \Auth::user()->email;
            $article->save();

            //Insert Ingredients

            foreach ($ingredients as $k => $items) {
                $tags_ingredient = explode(',', $items);
                $ingredient_item = $tags_ingredient[0];
                if (Ingredients::whereName($ingredient_item)->count() == 0) {
                    $new_ingredient = new Ingredients();
                    $new_ingredient->name = $ingredient_item;
                    $new_ingredient->slug = str_slug($items);
                    $new_ingredient->save();
                } else {
                    $new_ingredient = Ingredients::whereName($ingredient_item)->first();
                }
                $relation_ingredients = new IngredientRelation();
                $relation_ingredients->article_id = $article->id;
                $relation_ingredients->ingredient_id = $new_ingredient->id;
                $relation_ingredients->quanlity = $quanlity[$k];
                $relation_ingredients->quanlity_type = $quanlity_type[$k];
                $relation_ingredients->save();
            }
            //meta insert
            foreach ($meta_data as $k => $v) {
                if ($k == 'related' || $k == 'tags') {
                    if ($k == 'tags') {
                        $tags = explode(',', $v);
                        $meta_final = [];
                        foreach ($tags as $items) {
                            if (Category::where('title', $items)->where('type', 'tag')->count() > 0) {
                                $tag = Category::where('title', $items)->where('type', 'tag')->first();
                                $meta_final[] = [$tag->id => $tag->title];
                            } else {
                                $tag = new Category();
                                $tag->title = $items;
                                $tag->slug = str_slug($items);
                                $tag->type = 'tag';
                                $tag->parent_id = 0;
                                $tag->status = 1;
                                $tag->save();

                                $meta_final[] = [$tag->id => $items];
                            }
                        }
                    } else {
                        $meta_final = [];
                        $v = str_replace('\,', ':abc', $v);
                        $relates = explode(',', $v);
                        foreach ($relates as $items) {
                            $items = str_replace(':abc', ',', $items);
                            $check = Article::where('title', $items)->count();
                            if ($check > 0) {
                                $relate = Article::where('title', $items)->first();
                                $meta_final[] = [$relate->id => $relate->title];
                            }
                        }
                    }
                    $meta_insert = new MetaArticle ();
                    $meta_insert->meta_key = $k;
                    $meta_insert->meta_value = json_encode($meta_final);
                    $meta_insert->article_id = $article->id;
                    $meta_insert->save();
                } elseif ($k == 'category') {
                    foreach (json_decode($v) as $cate) {
                        $meta_insert = new MetaArticle ();
                        $meta_insert->meta_key = 'relation_category';
                        $meta_insert->meta_value = $cate;
                        $meta_insert->article_id = $article->id;
                        $meta_insert->save();
                    }
                } else {
                    $meta_insert = new MetaArticle ();
                    $meta_insert->meta_key = 'recipe_' . $k;
                    if ($k == 'ingredients' || $k == 'directions') {
                        $meta_insert->meta_value = str_replace(PHP_EOL, '<br>', $v);
                    } else {
                        $meta_insert->meta_value = $v;
                    }
                    $meta_insert->article_id = $article->id;
                    $meta_insert->save();
                }
            }
            \Cache::forever('youtube_' . $article->id, $youtube);
            $content = '';
            foreach ($steps as $k => $step) {
                $name_step = $k + 1;
                $content = $step;
                $new_step = new Step();
                $new_step->number_step = $name_step;
                $new_step->content = $content;
                $new_step->article_id = $article->id;
                $new_step->gallery = json_encode($files_image_array[$k]);
                $new_step->save();
            }

            //Job publish Article
            $this->_post->getById($article->id);
            //resize Image
            $thumbnail = !empty($article->thumbnail) ? $article->thumbnail : '';
            if (!empty($thumbnail) && (str_contains($thumbnail, 'http://') == false)) {
                $this->resizeImage($thumbnail, 300, 180);
                $this->resizeImage($thumbnail, 96, 72);
            }

            $thumbnail_extra = !empty($article->thumbnail_extra) ? $article->thumbnail_extra : '';
            if (!empty($thumbnail_extra) && (str_contains($thumbnail_extra, 'http://') == false)) {
                $this->resizeImage($thumbnail_extra, 300, 180);
                $this->resizeImage($thumbnail_extra, 96, 72);
            }

            //Insert Meta Seo
            $job = new \App\Jobs\JobCacheArray($article->id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);

            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getEdit($article_id)
    {
        $this->authorize('EditArticle');

        try {
            foreach (config('admincp.type_category') as $k => $v) {

                if ($k === 'food_2') {
                    $data['food_2'] = Category::where('type', 'food')->where('type_article', 'recipe')->get();
                } else {
                    $data[$k] = Category::where('type', $k)->get();
                }
            }
            $article = Article::find($article_id);

            foreach ($article->articleOtherInfoRecipe as $v) {
                $data[$v->meta_key] = $v->meta_value;
            }
            $this->authorize('PostOfUser', $article);
            $data['article'] = $article;
            $data['events'] = Event::where('status', 1)->get();
            $data['level'] = Category::where('type', 'level')->where('type_article', 'recipe')->get();
            return view('childs.recipe.edit')->with($data);
        } catch (Exception $ex) {
            return json_encode(['status' => 'error', 'msg' => $ex->getMessage()]);
        }
    }

    public function postEdit($article_id)
    {
        dd(1);
        $this->authorize('EditArticle');
        try {
            $cache = new CacheRedis();
            $data_article = \Input::except('_token', 'category', 'seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'publish_time', 'publish_date', 'prep_time', 'cook_time', 'ingredients', 'directions', 'quanlity', 'quanlity_type', 'steps', 'files_steps', 'youtube');
            $meta_data = \Input::only(['prep_time', 'cook_time', 'directions', 'seo_title', 'seo_meta', 'seo_description', 'category', 'tags', 'related', 'gallery']);
            $ingredients = \Input::get('ingredients');
            $quanlity = \Input::get('quanlity');
            $steps = \Input::get('steps');
            $files_image = \Input::get('files_steps');
            $files_image_array = json_decode($files_image);
            $quanlity_type = \Input::get('quanlity_type');
            $content = '';
            $youtube = \Input::get('youtube');

            $parent = $data_article['parent_category'];
            $category = Category::find($parent);
            $cat = json_decode($meta_data['category']);
            $cat[] = $category->id;
            $check = false;
            $parent_id = $category->parent_id;
            while (true) {
                if ($parent_id != 0) {
                    $child = Category::find($parent_id);
                    $cat[] = $child->id;
                    $parent_id = $child->parent_id;
                } else {
                    break;
                }
            }
            $cat = array_unique($cat);
            $meta_data['category'] = json_encode($cat);


            if (!empty($steps)) {
                foreach ($steps as $k => $step) {
                    $name_step = $k + 1;
                    $content .= '<p><b>Bước ' . $name_step . '</b> : ' . $step . '</p>';
                    foreach ($files_image_array[$k] as $image) {
                        $content .= '<center><img  src="' . $image . '" width="475" ></center>';
                    }
                }
                $content = $content . '<p><center>' . $youtube . '</center></p>';
                $data_article['content'] = $content;
            } else {
                $content = \Input::get('content');
                $content = $content . '<p style="display:none";><center style="display:none";>' . $youtube . '</center></p>';
                $data_article['content'] = $content;
            }


            $article = Article::find($article_id);

            $data = array();
            foreach ($data_article as $k => $v) {
                if ($k == 'event_id' && $v == 0) {
                    $article->event_id = null;
                } else {
                    $article->$k = $v;
                }
            }
            if (\Input::get('gallery') != '') {
                $folder_gallery = public_path() . \Input::get('gallery');
                if (is_dir($folder_gallery)) {
                    foreach (scandir($folder_gallery) as $k => $items) {
                        if ($k >= 3) {
                            $data[] = str_replace('//', '/', $folder_gallery . '/' . $items);
                        }
                    }
                    $gallery_path = json_encode($data);
                } else {
                    $gallery_path = '';
                }
            } else {
                $meta_data['gallery'] = isset($article->articlegallery->meta_value) ? $article->articlegallery->meta_value : '';
                $gallery_path = '';
            }
            $cache->ResetCategoryTag($article);
            $article->articleCategory()->detach();

            $article->title = \Input::get('title');
            if (Input::get('status') == '') {
                $article->status = 'schedule';
            } else {
                $article->status = Input::get('status');
            }
            if (Input::get('status') == 'schedule') {
                $datetime_publish = date('Y-m-d H:i', strtotime(\Input::get('publish_date') . ' ' . \Input::get('publish_time')));
                $article->published_at = $datetime_publish;
            } elseif (Input::get('status') == 'publish') {
                $datetime_publish = date('Y-m-d H:i', strtotime(\Input::get('publish_date') . ' ' . \Input::get('publish_time')));
                $article->published_at = $datetime_publish;
            } else {
                $article->published_at = null;
            }
            // $article->slug = str_slug(\Input::get('title'));
            $article->gallery_path = $gallery_path;

            $article->save();

            //Insert Ingredients
            IngredientRelation::where('article_id', $article->id)->delete();
            foreach ($ingredients as $k => $items) {
                $tags_ingredient = explode(',', $items);
                $ingredient_item = $tags_ingredient[0];
                if (Ingredients::whereName($ingredient_item)->count() == 0) {
                    $new_ingredient = new Ingredients();
                    $new_ingredient->name = $ingredient_item;
                    $new_ingredient->slug = str_slug($items);
                    $new_ingredient->save();
                } else {
                    $new_ingredient = Ingredients::whereName($ingredient_item)->first();
                }
                $relation_ingredients = new IngredientRelation();
                $relation_ingredients->article_id = $article->id;
                $relation_ingredients->ingredient_id = $new_ingredient->id;
                $relation_ingredients->quanlity = $quanlity[$k];
                $relation_ingredients->quanlity_type = $quanlity_type[$k];
                $relation_ingredients->save();
            }

            //meta insert
            foreach ($meta_data as $k => $v) {
                if ($k == 'related' || $k == 'tags') {
                    if ($k == 'tags') {
                        $tags = explode(',', $v);
                        $meta_final = [];
                        foreach ($tags as $items) {
                            if (Category::where('title', $items)->where('type', 'tag')->count() > 0) {
                                $tag = Category::where('title', $items)->where('type', 'tag')->first();
                                $meta_final[] = [$tag->id => $tag->title];
                            } else {
                                $tag = new Category();
                                $tag->title = $items;
                                $tag->slug = str_slug($items);
                                $tag->type = 'tag';
                                $tag->parent_id = 0;
                                $tag->status = 1;
                                $tag->save();

                                $meta_final[] = [$tag->id => $items];
                            }
                        }
                    } else {
                        $meta_final = [];
                        $v = str_replace('\,', ':abc', $v);
                        $relates = explode(',', $v);
                        foreach ($relates as $items) {
                            $items = str_replace(':abc', ',', $items);
                            $check = Article::where('title', $items)->count();
                            if ($check > 0) {
                                $relate = Article::where('title', $items)->first();
                                $meta_final[] = [$relate->id => $relate->title];
                            }
                        }
                    }
                    $meta_insert = new MetaArticle ();
                    $meta_insert->meta_key = $k;
                    $meta_insert->meta_value = json_encode($meta_final);
                    $meta_insert->article_id = $article->id;
                    $meta_insert->save();
                } elseif ($k == 'category') {
                    foreach (json_decode($v) as $cate) {
                        $meta_insert = new MetaArticle ();
                        $meta_insert->meta_key = 'relation_category';
                        $meta_insert->meta_value = (int)$cate;
                        $meta_insert->article_id = $article->id;
                        $meta_insert->save();
                    }
                } else {
                    $meta_insert = new MetaArticle ();
                    $meta_insert->meta_key = 'recipe_' . $k;
                    if ($k == 'ingredients' || $k == 'directions') {
                        $meta_insert->meta_value = str_replace(PHP_EOL, '<br>', $v);
                    } else {
                        $meta_insert->meta_value = $v;
                    }
                    $meta_insert->article_id = $article->id;
                    $meta_insert->save();
                }
            }

            if (!empty($steps)) {
                Step::where('article_id', $article->id)->delete();
                \Cache::forever('youtube_' . $article->id, $youtube);
                foreach ($steps as $k => $step) {
                    $name_step = $k + 1;
                    $content = $step;
                    $new_step = new Step();
                    $new_step->number_step = $name_step;
                    $new_step->content = $content;
                    $new_step->article_id = $article->id;
                    $new_step->gallery = json_encode($files_image_array[$k]);
                    $new_step->save();
                }
            } else if (!empty($content)) {
                \Cache::forever('youtube_' . $article->id, $youtube);
            }

            $cache->CreateArticle($article);
            //Job publish Article
            $this->_post->getById($article->id);

            //resize Image
            $thumbnail = !empty($article->thumbnail) ? $article->thumbnail : '';
            if (!empty($thumbnail) && (str_contains($thumbnail, 'http://') == false)) {
                $this->resizeImage($thumbnail, 300, 180);
                $this->resizeImage($thumbnail, 96, 72);
            }

            $thumbnail_extra = !empty($article->thumbnail_extra) ? $article->thumbnail_extra : '';
            if (!empty($thumbnail_extra) && (str_contains($thumbnail_extra, 'http://') == false)) {
                $this->resizeImage($thumbnail_extra, 300, 180);
                $this->resizeImage($thumbnail_extra, 96, 72);
            }

            //Insert Meta Seo
            $job = new \App\Jobs\JobCacheArray($article->id, 'recipe', []);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);

            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postPublish()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'publish';
            $article->published_at = date('Y-m-d H:i:s');
            $article->save();

            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            //NAP LAI KEY
            $this->cache->CreateArticle(Article::find($article_id));
            //KET THUC

            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Publish successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postTrash()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->delete();

            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id);
            //KET THUC

            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUntrash()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            Article::withTrashed()->where('id', (int)$article_id)->restore();
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postVerify()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'pending';
            $article->published_at = null;
            $article->save();
            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id);
            //KET THUC


            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postUnverify()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'draft';
            $article->published_at = null;
            $article->save();

            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id);
            //KET THUC

            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postDraft()
    {
        $this->authorize('EditArticle');
        try {
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'draft';
            $article->published_at = null;
            $article->save();

            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id);
            //KET THUC
            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article_id, 'recipe', []);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheRecipes();
            $this->dispatch($job2);
            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

}
