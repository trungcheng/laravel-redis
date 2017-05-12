<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaArticleFe as MetaArticle;
use App\Repositories\Post\PostRepository;
use App\Cache\Article\ArticleCache ;

class JobLike extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $key;
    protected $article_id;
    protected $user_id;

    public function __construct($key, $article_id, $user_id, PostRepository $post)
    {
        $this->key = $key;
        $this->article_id = $article_id;
        $this->user_id = $user_id;
        $this->_post = $post ;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $key = $this->key;
        $article_id = $this->article_id;
        $user_id = $this->user_id;
        if (MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->where('meta_value', $user_id)->count() > 0) {
            MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->where('meta_value', $user_id)->delete();
            $cache = new ArticleCache() ;
            $cache->getById($article_id) ;
            return response()->json(['status' => http_response_code()]);
        } else {
            $meta_fe = new MetaArticle();
            $meta_fe->meta_key = $key;
            $meta_fe->meta_value = $user_id;
            $meta_fe->article_id = $article_id;
            $meta_fe->save();
            $cache = new ArticleCache() ;
            $cache->getById($article_id) ;
            return response()->json(['status' => http_response_code()]);
        }
    }
}
