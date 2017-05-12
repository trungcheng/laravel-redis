<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaArticleFe as MetaArticle;
use App\Models\Collection ;

class JobDeleteCollection extends Job implements ShouldQueue
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
        $collection = Collection::find($this->data);
        if ($collection->creator == $this->email) {
            $collection->delete();
        } else {
            $own = 'co-own' ;
            $collection->$own = str_replace($this->email,'',$collection->$own) ;
            $collection->save() ;
        }
    }
}
