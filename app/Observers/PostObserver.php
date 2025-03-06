<?php

namespace App\Observers;

use App\Events\PostChanged;
use App\Models\Post;

class PostObserver
{
    public function created(Post $post): void
    {
        event(new PostChanged($post, 'created'));
    }

    public function updated(Post $post): void
    {
        event(new PostChanged($post, 'updated'));
    }

    public function deleted(Post $post): void
    {
        event(new PostChanged($post, 'deleted'));
    }
}