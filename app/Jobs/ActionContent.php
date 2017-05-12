<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Notification ; 
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class ActionContent extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    //EXAMPLE 
    // CLASS = "\APP\MODELS\POST"  ;
    private $_id ;
    private $model ;
    private $type_action ;
    private $route ; 
    
    public function __construct( $id , $model , $type_action , $route  )
    {
        $this->_id = $id;
        $this->model =$model  ;
        $this->type_action = $type_action ;
        $this->route = $route ; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Exec with Mongo Database
        $model = $this->model ;
        $delete = $model::find($this->_id);
        $delete->delete()  ;
        $new = new Notification()  ;
            $new->reload = 'true' ;
            $new->route = $this->route ;
            $new->type_noti = 'success' ;
        $new->save() ;
    }
}
