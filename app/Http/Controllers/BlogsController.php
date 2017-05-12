<?php

namespace App\Http\Controllers;

use App\Cache\CacheRedis;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Category;
use App\Models\MetaArticle;
use Illuminate\Support\Facades\Input;
use App\Repositories\Post\PostRepository;

class BlogsController extends Controller
{

    public function __construct(Request $request, PostRepository $article_interface)
    {
        $this->_request = $request;
        $this->_post = $article_interface;
        $this->cache = new CacheRedis();
    }

    public function getCreate()
    {
        $this->authorize('CreateArticle');
        foreach (config('admincp.type_category') as $k => $v) {

            if ($k == 'food_1') {
                $data['food_1'] = Category::where('type', 'food')
                    ->where('type_article', 'review')
                    ->get();
            } else {
                $data[$k] = Category::where('type', $k)
                    ->get();
            }

        }
        return view('childs.blogs.create')->with($data);
    }

    public function postCreate()
    {
        $this->authorize('CreateArticle');
        try {
            $data_article = \Input::except('_token', 'seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'publish_time', 'publish_date', 'category');
            $meta_data = \Input::only(['seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'category']);

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
                    $meta_insert->meta_key = 'review_' . $k;
                    if ($k == 'ingredients' || $k == 'directions') {
                        $meta_insert->meta_value = str_replace(PHP_EOL, '<br>', $v);
                    } else {
                        $meta_insert->meta_value = $v;
                    }
                    $meta_insert->article_id = $article->id;
                    $meta_insert->save();
                }
            }

            //Job publish Article
            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article->id, 'blog', []);
            $this->dispatch($job);
            $job3 = new \App\Jobs\CacheBlogs();
            $this->dispatch($job3);

            //nap cache Detail
            $this->cache->CreateArticle($article);

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

    public function getEdit($article_id)
    {
        $this->authorize('EditArticle');
        foreach (config('admincp.type_category') as $k => $v) {
            if ($k == 'food_1') {
                $data['food_1'] = Category::where('type', 'food')
                    ->where('type_article', 'review')
                    ->get();
            } else {
                $data[$k] = Category::where('type', $k)
                    ->get();
            }
        }
        $article = Article::find($article_id);
        foreach ($article->articleOtherInfoReview as $v) {
            $data[$v->meta_key] = $v->meta_value;
        }
        foreach (config('admincp.type_category') as $k => $v) {
            if ($v[1] == 'blog') {
                if ($k == 'food_1') {
                    $data['food_1'] = Category::where('type', 'food')->where('type_article', 'review')->get();
                } else {
                    $data[$k] = Category::where('type', $k)->get();
                }
            }
        }

        $this->authorize('PostOfUser', $article);
        $data['article'] = $article;
        return view('childs.blogs.edit')->with($data);
    }

    public function postEdit($article_id)
    {
        $this->authorize('EditArticle');
        try {
            $data_article = \Input::except('_token', 'seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'publish_time', 'publish_date', 'category');
            $meta_data = \Input::only(['seo_title', 'seo_meta', 'seo_description', 'tags', 'related', 'gallery', 'category']);
            $article = Article::find($article_id);


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
                $meta_data['gallery'] = $article->articlegallery->meta_value;
                $gallery_path = '';
            }


            //XOA BO QUAN HE CATEGORY TAG TRONG KEY REDIS
            $this->cache->ResetCategoryTag($article);
            //END


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
//            $article->slug = str_slug(\Input::get('title'));
            $article->gallery_path = $gallery_path;
            $article->save();

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
                    $meta_insert->meta_key = 'review_' . $k;
                    if ($k == 'ingredients' || $k == 'directions') {
                        $meta_insert->meta_value = str_replace(PHP_EOL, '<br>', $v);
                    } else {
                        $meta_insert->meta_value = $v;
                    }
                    $meta_insert->article_id = $article->id;
                    $meta_insert->save();
                }
            }

            //Job publish Article
            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article->id, 'blog', []);
            $this->dispatch($job);
            $job3 = new \App\Jobs\CacheBlogs();
            $this->dispatch($job3);

            //Nap key detail
            $this->cache->CreateArticle($article);
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
            $this->_post->getById($article->id);


            //XOA KHOI RELATION REDIS
            $this->cache->ResetCategoryTag($article);
            //NAP LAI KEY
            $this->cache->CreateArticle(Article::find($article_id));
            //KET THUC

            $job = new \App\Jobs\JobCacheArray($article->id, 'blog', []);
            $this->dispatch($job);
            $job3 = new \App\Jobs\CacheBlogs();
            $this->dispatch($job3);


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


            $job = new \App\Jobs\JobCacheArray($article->id, 'blog', []);
            $this->dispatch($job);
            $job3 = new \App\Jobs\CacheBlogs();
            $this->dispatch($job3);


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
            $this->cache->ResetCategoryTag($article);
            //Nap key detail
            $this->cache->cacheDetailArticle($article_id) ;
            //KET THUC

            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article->id, 'blog', []);
            $this->dispatch($job);
            $job3 = new \App\Jobs\CacheBlogs();
            $this->dispatch($job3);

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
            $this->cache->cacheDetailArticle($article_id) ;
            //KET THUC

            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article->id, 'blog', []);
            $this->dispatch($job);
            $job3 = new \App\Jobs\CacheBlogs();
            $this->dispatch($job3);

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
            $this->cache->cacheDetailArticle($article_id) ;
            //KET THUC

            $this->_post->getById($article->id);

            $job = new \App\Jobs\JobCacheArray($article->id, 'blog', []);
            $this->dispatch($job);
            $job3 = new \App\Jobs\CacheBlogs();
            $this->dispatch($job3);

            return json_encode(['status' => 'success', 'msg' => 'Move to trash article successfully']);
        } catch (\Exception $e) {
            return json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
        }
    }

}
