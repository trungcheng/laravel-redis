<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Driver
    |--------------------------------------------------------------------------
    |
    | The Laravel queue API supports a variety of back-ends via an unified
    | API, giving you convenient access to each back-end using the same
    | syntax for each one. Here you may set the default queue driver.
    |
    | Supported: "null", "sync", "database", "beanstalkd",
    |            "sqs", "iron", "redis"
    |
    */

    'default' => env('QUEUE_DRIVER', 'sync'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Here you may configure the connection information for each server that
    | is used by your application. A default configuration has been added
    | for each back-end shipped with Laravel. You are free to add more.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'expire' => 60,
        ],
        'database2' => [
            'driver' => 'database',
            'table' => 'jobs',
            'queue' => 'default',
            'expire' => 60,
        ],

        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host'   => 'localhost',
            'queue'  => 'default',
            'ttr'    => 60,
        ],

        'sqs' => [
            'driver' => 'sqs',
            'key'    => 'your-public-key',
            'secret' => 'your-secret-key',
            'queue'  => 'your-queue-url',
            'region' => 'us-east-1',
        ],

        'iron' => [
            'driver'  => 'iron',
            'host'    => 'mq-aws-us-east-1.iron.io',
            'token'   => 'your-token',
            'project' => 'your-project-id',
            'queue'   => 'your-queue-name',
            'encrypt' => true,
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue'  => 'default',
            'expire' => 60,
        ],
        'redis' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default',
            'expire' => 60,
        ],
        'Rate' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-rate',
            'expire' => 60,
        ],
        'Like' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-like',
            'expire' => 60,
        ],
        'UpdateCollection' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-update-collection',
            'expire' => 60,
        ],
        'InsertCollection' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-insert-collection',
            'expire' => 60,
        ],
        'DeleteCollection' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-delete-collection',
            'expire' => 60,
        ],
        'Recipe' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-recipe',
            'expire' => 60,
        ],
        'InsertUser' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-insert-user',
            'expire' => 60,
        ],
        'UpdateUser' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-update-user',
            'expire' => 60,
        ],
        'View' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-view',
            'expire' => 60,
        ],
        'InsertComment' => [
            'driver' => 'redis',
            'connection' => 'default',
            'queue' => 'default-insert-comment',
            'expire' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | These options configure the behavior of failed queue job logging so you
    | can control which database and table are used to store the jobs that
    | have failed. You may change them to any database / table you wish.
    |
    */

    'failed' => [
        'database' => 'mysql', 'table' => 'failed_jobs',
    ],

];
