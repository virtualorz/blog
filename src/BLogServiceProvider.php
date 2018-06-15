<?php

namespace Virtualorz\Blog;

use Illuminate\Support\ServiceProvider;

class BlogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->publishes([
            __DIR__.'/config/blog.php' => config_path('blog.php'),
        ]);
        $this->mergeConfigFrom(
            __DIR__.'/config/blog.php', 'blog'
        );
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('blog', function () {
            return new Blog();
        });
    }
}
