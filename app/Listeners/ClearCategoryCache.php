<?php

namespace App\Listeners;

use App\Events\CategoryChanged;
use Illuminate\Support\Facades\Cache;

class ClearCategoryCache
{
    public function handle(CategoryChanged $event): void
    {
        // Clear general cache
        Cache::forget('categories.all');

        // Clear specific cache
        Cache::forget('categories.find.' . $event->category->id);
        Cache::forget('categories.slug.' . $event->category->slug);

        // Jika kategori diupdate, hapus juga cache dengan slug lama
        if ($event->action === 'updated' && $event->category->getOriginal('slug') !== $event->category->slug) {
            Cache::forget('categories.slug.' . $event->category->getOriginal('slug'));
        }
    }
}