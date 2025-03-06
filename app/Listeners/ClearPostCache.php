<?php

namespace App\Listeners;

use App\Events\PostChanged;
use Illuminate\Support\Facades\Cache;

class ClearPostCache
{
    public function handle(PostChanged $event): void
    {
        // Clear general cache
        Cache::forget('posts.all');
        Cache::forget('posts.published');

        // Clear specific cache
        Cache::forget('posts.find.' . $event->post->id);
        Cache::forget('posts.slug.' . $event->post->slug);
        Cache::forget('posts.category.' . $event->post->category_id);

        // Jika post diupdate
        if ($event->action === 'updated') {
            // Jika slug berubah, hapus cache dengan slug lama
            if ($event->post->getOriginal('slug') !== $event->post->slug) {
                Cache::forget('posts.slug.' . $event->post->getOriginal('slug'));
            }

            // Jika kategori berubah, hapus cache kategori lama
            if ($event->post->getOriginal('category_id') !== $event->post->category_id) {
                Cache::forget('posts.category.' . $event->post->getOriginal('category_id'));
            }
        }
    }
}