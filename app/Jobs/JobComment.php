<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaArticleFe as MetaArticle;
use App\Models\Comment ; 

class JobComment extends Job implements ShouldQueue
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
        $comment = new Comment() ; 
		$data  = json_decode($this->data)   ;
		foreach($data as $k => $v ) {
			$comment->$k = $v ; 
		}
		$comment->creator = $this->email ;
		$comment->save() ; 
    }
}
