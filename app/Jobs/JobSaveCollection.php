<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Collection ; 

class JobSaveCollection extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    protected $collection_id;
    protected $user_id;

    public function __construct($collection_id, $user_id)
    {
        $this->collection_id = $collection_id;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
			$collection = Collection::find($this->collection_id) ; 
			$obj = 'co-own' ; 
			$collection->$obj = $collection->$obj.','.$this->user_id   ; 
			$collection->save(); 
    }
}
