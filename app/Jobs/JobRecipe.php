<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaArticle;
use App\Models\Article;
class JobRecipe extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected  $data ;
    protected  $meta_data  ;
	protected  $email ;
    public function __construct($data , $meta_data , $email  )
    {
        $this->data = $data ;
        $this->meta_data = $meta_data ;
		$this->email = $email ;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
			$article = new Article();
            foreach ($this->data as $k => $v) {
                $article->$k = $v;
            }
            $article->status = 'draft' ;
            $article->slug = str_slug(\Request::get('title'));
            $article->creator =  $this->email;
            $article->save();
			
			
			
			foreach ($this->meta_data as $k => $v) {
                if ($k == 'category') {
                    foreach ( $v as $cate) {
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
    }
}
