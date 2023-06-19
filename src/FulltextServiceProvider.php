<?php

namespace Saber13812002\Laravel\Fulltext;

use Illuminate\Support\ServiceProvider;
use Saber13812002\Laravel\Fulltext\Commands\Index;
use Saber13812002\Laravel\Fulltext\Commands\IndexOne;
use Saber13812002\Laravel\Fulltext\Commands\UnindexOne;

class FulltextServiceProvider extends ServiceProvider
{
    protected $commands = [
      Index::class,
      IndexOne::class,
      UnindexOne::class,
    ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/laravel-fulltext.php',
            'laravel-fulltext'
        );

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                __DIR__.'/../config/laravel-fulltext.php' => config_path('laravel-fulltext.php'),
                ],
                'laravel-fulltext'
            );

            $this->publishes(
                [
                __DIR__.'/../database/migrations' => database_path('migrations'),
                ],
                'laravel-fulltext'
            );

            $this->commands($this->commands);
        }

        $this->app->bind(
            SearchInterface::class,
            Search::class
        );
    }
}
