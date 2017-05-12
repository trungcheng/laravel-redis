<?php

namespace App\Http\Controllers\TraitController;

use App\Cache\CacheRedis;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\MetaArticle;
use App\Models\Article;
use Illuminate\Support\Facades\Input;
use App\Repositories\Post\PostRepository;

trait ArticleDetail
{

    public function __construct(Request $request, PostRepository $article_interface , CacheRedis $redis )
    {
        $this->_request = $request;
        $this->_post = $article_interface;

    }

    public function getCreate()
    {
//
        $this->authorize('CreateArticle');
        foreach (config('admincp.type_category') as $k => $v) {
            if ($k == 'food_1') {
                $data['food_1'] = Category::where('type', 'food')->where('type_article', 'review')->get();
            } else {
                $data[$k] = Category::where('type', $k)->get();
            }
        }
        return view('childs.article.create')->with($data);
    }

    public function postCreate()
    {
        $this->authorize('CreateArticle');
        try {
            $data_article = \Input::except('_token', 'category', 'guess', 'seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'publish_time', 'publish_date', 'address', 'time_action', 'phone', 'price', 'open_time', 'close_time', 'ward');
            $meta_data = \Input::only(['address', 'phone', 'price', 'seo_title', 'seo_meta', 'seo_description', 'category', 'tags', 'related', 'gallery', 'ward']);

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


            $article = new Article();
//            $data = array();
            foreach ($data_article as $k => $v) {
                $article->$k = $v;
            }

            $gallery_path = ScanGallery();
            $article = TimeStatusPublish($article);

            $article->slug = str_slug(\Input::get('title'));
            $article->gallery_path = $gallery_path;
            $article->creator = \Auth::user()->email;
            $article->save();




            $data_category = MetaDataProgress($meta_data, $article);

            $meta_insert = new MetaArticle ();
            $meta_insert->meta_key = 'review_time_action';
            $meta_insert->meta_value = json_encode(['open_time' => \Input::get('open_time'), 'close_time' => \Input::get('close_time')]);
            $meta_insert->article_id = $article->id;
            $meta_insert->save();
            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article->id, 'review', $data_category);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheReview();
            $this->dispatch($job2);
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

            //nap redis chi tiet
            $this->cache->CreateArticle($article);
            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function getEdit($article_id)
    {
        $this->authorize('EditArticle');
        foreach (config('admincp.type_category') as $k => $v) {
            if ($k == 'food_1') {
                $data['food_1'] = Category::where('type', 'food')->where('type_article', 'review')->get();
            } else {
                $data[$k] = Category::where('type', $k)->get();
            }
        }
        $article = Article::find($article_id);
        foreach ($article->articleOtherInfoReview as $v) {
            $data[$v->meta_key] = $v->meta_value;
        }

        $this->authorize('PostOfUser', $article);
        $data['article'] = $article;
        return view('childs.article.edit')->with($data);
    }

    public function postEdit($article_id)
    {
        $this->authorize('EditArticle');
        try {
            $data_article = \Input::except('_token', 'category', 'location', 'guess', 'seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'publish_time', 'publish_date', 'address', 'time_action', 'phone', 'price', 'open_time', 'close_time', 'ward');
            $meta_data = \Input::only(['address', 'phone', 'price', 'seo_title', 'seo_meta', 'seo_description', 'category', 'tags', 'related', 'gallery', 'ward']);
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

            $article = Article::find($article_id);

            $data = array();
            foreach ($data_article as $k => $v) {
                $article->$k = $v;
            }
            if (\Input::get('gallery') != '') {
                $folder_gallery = \Input::get('gallery');
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
                if (isset($article->articlegallery->meta_value)) {
                    $meta_data['gallery'] = $article->articlegallery->meta_value;
                } else {
                    $meta_data['gallery'] = '';
                }
                $gallery_path = '';
            }



            $this->cache->ResetCategoryTag($article);
            $article->articleCategory()->detach();

            $article->title = \Input::get('title');
            if (Input::get('status') == '') {
                $article->status = 'schedule';
            } else {
                $article->status = Input::get('status');
            }
//            $article->slug = str_slug(\Input::get('title'));
            $article->gallery_path = $gallery_path;
            TimeStatusPublish($article);




            $article->save();

//meta insert
            $data_category = MetaDataProgress($meta_data, $article);


            $meta_insert = new MetaArticle ();
            $meta_insert->meta_key = 'review_time_action';
            $meta_insert->meta_value = json_encode(['open_time' => \Input::get('open_time'), 'close_time' => \Input::get('close_time')]);
            $meta_insert->article_id = $article->id;
            $meta_insert->save();

//Job publish Article
            $this->_post->getById($article->id);
            $job = new \App\Jobs\JobCacheArray($article->id, 'review', $data_category);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheReview();
            $this->dispatch($job2);

            //nap redis chi tiet
            $this->cache->CreateArticle(Article::find($article_id));
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
            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function postPublish()
    {
        $this->authorize('EditArticle');
        try {
            $email = auth()->user()->email;
            $article_id = isset($_POST['id']) ? $_POST['id'] : 0;
            $article = Article::find((int)$article_id);
            $article->status = 'publish';
            $article->published_at = date('Y-m-d H:i:s');
            $article->approve_by = $email;
            $article->save();

            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            //NAP LAI KEY
            $this->cache->CreateArticle(Article::find($article_id));
            //KET THUC

            $this->_post->getById($article->id);
            foreach ($article->articleCategory as $items) {
                $data_category[] = $items->id;
            }
            $job = new \App\Jobs\JobCacheArray($article->id, 'review', $data_category);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheReview();
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
            $article->status = 'draft';
            $article->save();




            $article = Article::find((int)$article_id);
            $article->delete();

            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            $this->cache->cacheDetailArticle($article_id) ;
            //KET THUC


            foreach ($article->articleCategory as $items) {
                $data_category[] = $items->id;
            }

            $job = new \App\Jobs\JobCacheArray($article->id, 'review', $data_category);
            $this->dispatch($job);
            $job2 = new \App\Jobs\CacheReview();
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

            $article = Article::with('articleCategory' , 'tags')->find($article_id);

            $this->cache->ResetCategoryTag($article);
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id) ;
            //KET THUC

            $this->_post->getById($article->id);
            foreach ($article->articleCategory as $items) {
                $data_category[] = $items->id;
            }
            $job = new \App\Jobs\JobCacheArray($article->id, 'review', $data_category);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheReview();
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
            $this->cache->ResetCategoryTag(Article::find((int)$article_id));
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id) ;
            //KET THUC

            $this->_post->getById($article->id);
            $data_category[] = [];
            foreach ($article->articleCategory as $items) {
                $data_category[] = $items->id;
            }
            $job = new \App\Jobs\JobCacheArray($article->id, 'review', $data_category);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheReview();
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
            $this->cache->ResetCategoryTag(Article::find((int)$article_id));
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id) ;
            //KET THUC


            $this->_post->getById($article->id);
            $data_category[] = [];
            foreach ($article->articleCategory as $items) {
                $data_category[] = $items->id;
            }
            $job = new \App\Jobs\JobCacheArray($article->id, 'review', $data_category);
            $this->dispatch($job);

            $job2 = new \App\Jobs\CacheReview();
            $this->dispatch($job2);

            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

    public function anyEditPost(Request $request)
    {
        $this->authorize('EditArticle');
        try {
            return json_encode(['status' => 'success', 'msg' => 'Post Successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

}

?>
