<?php

namespace App\Console\Commands;


use App\Cache\Article\ArticleCache;
use App\Models\Article;
use Illuminate\Console\Command;

class AutoPublish extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autopub';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto Publish Article';

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
        $articles = \App\Models\Article::select('id')->where('status', '=', 'schedule')->where('published_at', '<=', date('Y-m-d H:i'))->get();
        foreach ($articles as $items) {
            $update = \App\Models\Article::find($items->id);
            $update->status = 'publish';
            $update->save();
            $cache = new ArticleCache();
            $cache->getById($items->id);
        }
    }
}
