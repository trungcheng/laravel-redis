<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use App\Models\Tags;
class ProcQueue extends Event
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    private $type  ;
    private $api_request ;
    private $data ; 
    public function __construct()
    {
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function handle()
    {
        $tag = new Tags() ;
        $tag->name = 'abc';
        $tag->slug = 'abc' ;
        $tag->save() ;
    }
}
