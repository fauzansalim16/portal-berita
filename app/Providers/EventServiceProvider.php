<?php

namespace App\Providers;

use App\Events\CategoryChanged;
use App\Events\PostChanged;
use App\Listeners\ClearCategoryCache;
use App\Listeners\ClearPostCache;
use App\Models\Category;
use App\Models\Post;
use App\Observers\CategoryObserver;
use App\Observers\PostObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CategoryChanged::class => [
            ClearCategoryCache::class,
        ],
        PostChanged::class => [
            ClearPostCache::class,
        ],
    ];

    public function boot(): void
    {
        Category::observe(CategoryObserver::class);
        Post::observe(PostObserver::class);
    }
}