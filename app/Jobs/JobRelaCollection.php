<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaArticleFe as MetaArticle;
use App\Models\Collection ; 
use App\Models\MetaCollection ; 

class JobRelaCollection extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $data ; 
	protected $email ; 

    public function __construct($data , $email )
    {
        $this->data = $data;
		$this->email = $email ; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
		$collections_of_user = Collection::where('creator' ,$this->email )->get() ; 
		$data  = json_decode($this->data)   ;
		foreach($collections_of_user as $items) : 
			MetaCollection::where('meta_key', 'article_of_collection')->where('meta_value', $data->article_id)->where('collection_id', $items->id)->delete();
		endforeach ; 	
		foreach (json_decode($data->collections) as $items) {
            $new = new MetaCollection();
            $new->meta_key = 'article_of_collection';
            $new->meta_value = $data->article_id;
            $new->collection_id = $items;
            $new->save();
        }
    }
}
