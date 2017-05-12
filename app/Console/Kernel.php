<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\PushNotify::class,
        \App\Console\Commands\Inspire::class,
        \App\Console\Commands\AutoPublish::class,
        \App\Console\Commands\RequestUserArticle::class,
        \App\Console\Commands\UserDetail::class,
        \App\Console\Commands\ColectionDetail::class,
        \App\Console\Commands\CategoryTags::class,
        \App\Console\Commands\UserFavorite::class,
        \App\Console\Commands\CollectionArticle::class,
        \App\Console\Commands\ArticleDetail::class,
        \App\Console\Commands\ShopList::class,
        \App\Console\Commands\ColectionRelation::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//        $schedule->command('inspire')
//            ->hourly();
    }
}
