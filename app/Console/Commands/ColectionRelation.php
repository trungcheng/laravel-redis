<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use App\Cache\CacheRedis;

class ColectionRelation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user_collection {take} {skip}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $cache = new CacheRedis();
        $take = $this->argument('take');
        $skip = $this->argument('skip');
        $data = \App\Models\Collection::take($take)->skip($skip)->get();
        set_time_limit(100);
        try {
            foreach ($data as $item) {
                $cache->UserCollection($item,'create');
            }
        }catch (Exception $e){
            echo  $e->getMessage();
        }

    }
}
