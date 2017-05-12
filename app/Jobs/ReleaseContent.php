<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Events\ProcQueue  ;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Article ;
class ReleaseContent extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected  $id ;
    function __construct($id)
    {
        $this->id = $id ;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $article = Article::find($this->id) ;
        $article->status = 'publish' ;
        $article->save() ;
    }
}
