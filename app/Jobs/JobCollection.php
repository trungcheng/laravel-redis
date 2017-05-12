<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\MetaArticleFe as MetaArticle;
use App\Models\Collection ; 

class JobCollection extends Job implements ShouldQueue
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
		$collection = new Collection();
		$data  = json_decode($this->data)   ;
        foreach ($data as $k => $v) {
            $collection->$k = $v;
        }
        $collection->creator = $this->email;
        $collection->save();
        $client = new \Hoa\Websocket\Client(
            new \Hoa\Socket\Client('ws://42.112.25.22:8889/')
        );
        $client->setHost('42.112.25.22') ;
        $client->on('open', function (\Hoa\Event\Bucket $bucket) {
            $bucket->getSource()->send('loading');
            return;
        });
        $client->connect();
        $client->close() ;
    }
}
