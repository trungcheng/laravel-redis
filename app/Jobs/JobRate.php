<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaArticleFe as MetaArticle;
use App\Repositories\Post\PostRepository;
use App\Cache\Article\ArticleCache ;
class JobRate extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $key;
    protected $article_id;
    protected $number_rate;

    public function __construct($key, $article_id, $number_rate , PostRepository $post )
    {
        $this->key = $key;
        $this->article_id = $article_id;
        $this->number_rate = $number_rate;
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
        $number_rate = $this->number_rate;
        if (MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->count() > 0) {
            $rate = MetaArticle::where('meta_key', $key)->where('article_id', $article_id)->first();
            $sessions = json_decode($rate->meta_value)->total_sessions + 1;
            $scores = json_decode($rate->meta_value)->total_scores + $number_rate;
            $rate->meta_value = json_encode(['total_scores' => $scores, 'total_sessions' => $sessions]);
            $rate->save();
            $cache = new ArticleCache() ;
            $cache->getById($article_id) ;
            return response()->json(['status' => http_response_code()]);
        } else {
            $meta_fe = new MetaArticle();
            $meta_fe->meta_key = $key;
            $meta_fe->meta_value = json_encode(['total_scores' => $number_rate, 'total_sessions' => 1]);
            $meta_fe->article_id = $article_id;
            $meta_fe->save();
            $cache = new ArticleCache() ;
            $cache->getById($article_id) ;
            return response()->json(['status' => http_response_code()]);
        }
    }
}
