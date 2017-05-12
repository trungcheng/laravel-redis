<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Category;
use App\Models\Article;

class JobCacheArray extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $article_id;
    protected $query;
    protected $category;

    public function __construct($article_id, $query, $category)
    {
        $this->article_id = $article_id;
        $this->query = $query;
        $this->category = $category;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // IMPORT TO CATEGORY ARRAY CACHE
        $article = Article::find($this->article_id);
        if ($article->status == 'publish') {
            if ($this->query == 'review') {
                $this->process_Category('publish');
            } elseif ($article->type == 'Recipe') {
                $this->process_Recipe('publish');
            } elseif ($article->type == 'Blogs') {
                $this->process_Blog('publish') ;
            }

        } else {
            if ($this->query == 'review') {
                $this->process_Category('draft');
            } elseif ($article->type == 'Recipe') {
                $this->process_Recipe('draft');
            } elseif ($article->type == 'blogs') {
                $this->process_Blog('draft') ;
            }
        }

    }

    function process_Category($status)
    {
        $category = $this->category;
        if ($category != null) {
            foreach ($category as $items) {
                $cate_detail = Category::find($items);
                if (!empty($cate_detail)) {
                    if (\Cache::has(str_slug($cate_detail->title))) {
                        $array = \Cache::get(str_slug($cate_detail->title));
                        if ($status == 'publish') {
                            array_unshift($array, $this->article_id);
                        } else {
                            $array = array_diff($array, [$this->article_id]);
                        }
                        \Cache::forever(str_slug($cate_detail->title), array_unique($array));
                    }
                }
            }
        }
    }

    function process_Recipe($status)
    {
        if (\Cache::has('recipe_all')) {
            $array = \Cache::get('recipe_all');
            if ($status == 'publish') {
                array_unshift($array, $this->article_id);
            } else {
                $array = array_diff($array, [$this->article_id]);
            }
            \Cache::forever('recipe_all', array_unique($array));
        }
    }

    function process_Blog($status)
    {
        if (\Cache::has('blog_all')) {
            $array = \Cache::get('blog_all');
            if ($status == 'publish') {
                array_unshift($array, $this->article_id);
            } else {
                $array = array_diff($array, [$this->article_id]);
            }
            \Cache::forever('blog_all', array_unique($array));
        }
    }
}
