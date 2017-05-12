<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class JobCache extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $query;

    public function __construct($query)
    {
        $this->query = $query;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $articles = \App\Models\Article::select('id')->where('status', '=', 'schedule')->where('published_at', '<=', date('Y-m-d H:i'))->get();
        foreach ($articles as $items) {
            $update = \App\Models\Article::find($items->id);
            $update->status = 'publish';
            $update->save();
            $cache = new  \App\Cache\Article\ArticleCache();
            $cache->getById($items->id);
        }
        if ($this->query == 'review') {
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
        } elseif ($this->query == 'recipe') {
            // NAP CACHE ALL RECIPE
            $recipe = \App\Models\Article::select('id')
                ->where('type', 'Recipe')
                ->where(function ($reviews) {
                    $reviews->where('status', 'publish');
                    $reviews->Orwhere('published_at', '<=', date('Y-m-d H:i'));
                })
                ->orderBy('published_at', 'desc')
                ->orderBy('id', 'desc')->get();
            foreach ($recipe as $item) {
                $data[] = $item->id;
            }
            \Cache::forever('recipe_all', $data);
        } elseif ($this->query == 'blog') {
            // NAP CACHE ALL RECIPE
            $blog = \App\Models\Article::select('id')
                ->where('type', 'Blogs')
                ->where(function ($reviews) {
                    $reviews->where('status', 'publish');
                    $reviews->Orwhere('published_at', '<=', date('Y-m-d H:i'));
                })
                ->orderBy('published_at', 'desc')
                ->orderBy('id', 'desc')->get();
            foreach ($blog as $item) {
                $data[] = $item->id;
            }
            \Cache::forget('blog_all');
            \Cache::forever('blog_all', $data);
        } elseif ($this->query == 'review-home') {
            // NAP CACHE ALL RECIPE
            $review = \App\Models\Article::select('*');
            $review = $review->where('type', 'Review')->where(function ($review) {
                $review->where('status', 'publish');
                $review->Orwhere('published_at', '<=', date('Y-m-d H:i'));
            });
            $review = $review->orderBy('published_at', 'desc')->orderBy('id', 'desc');
            $review = $review->skip(0)->take(8)->get();

            foreach ($review as $item) {
                $data[] = $item->id;
            }
            \Cache::forever('review_home', $data);
        }elseif ($this->query == 'blog-home') {
            $blogs = \App\Models\Article::select('id', 'title', 'slug', 'thumbnail', 'description', 'type', 'creator');
            $blogs = $blogs->where('type', 'Blogs')->where(function ($blogs) {
                $blogs->where('status', 'publish');
                $blogs->Orwhere('published_at', '<=', date('Y-m-d H:i'));
            });
            $blogs = $blogs->orderBy('published_at', 'desc')->orderBy('id', 'desc');
            $blogs = $blogs->skip(0)->take(8)->get();
            foreach ($blogs as $item) {
                $data[] = $item->id;
            }
            \Cache::forever('blog_home', $data);

        } elseif ($this->query == 'builtop') {
            $builttop = \App\Models\BuiltTop::select('name', 'link', 'link_img', 'post_id', 'type');
            $builttop = $builttop->where('status', 2);
            $builttop = $builttop->orderBy('position', 'asc');
            $builttop = $builttop->skip(0)->take(5)->get();
            foreach ($builttop as $item) {
                $data_type[] = $item->getArticle->type;
                $data_top[] = $item;
            }
            $data = [$data_type, $data_top];
            \Cache::forever('builtop', $data);
        } elseif ($this->query == 'user-top') {
            $meta;
            $data_user = [];
            $data_meta = [];
            $meta = \App\Models\MetaArticleFe::selectRaw('* , count(id) as total ')
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
        } elseif ($this->query == 'collection_home_review') {
            $data_collection = [];
            $data_article = [];
            $data_user = [];
            $collections = \App\Models\Collection::join('meta_collection', 'collection.id', '=', 'meta_collection.collection_id')
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
        } elseif ($this->query == 'collection_home_recipe') {
            $data_collection = [];
            $data_article = [];
            $data_user = [];
            $collections = \App\Models\Collection::join('meta_collection', 'collection.id', '=', 'meta_collection.collection_id')
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
        } elseif ($this->query == 'get_all_category') {
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
    }
}
