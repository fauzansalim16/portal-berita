<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Interfaces\PostRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class PostRepository implements PostRepositoryInterface
{
    protected $post;

    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    public function all()
    {
        return Cache::remember('posts.all', now()->addHours(24), function () {
            return $this->post->with('category')->get();
        });
    }

    public function published()
    {
        return Cache::remember('posts.published', now()->addHours(24), function () {
            return $this->post->where('status', 'published')->with('category')->get();
        });
    }

    public function find(int $id)
    {
        return Cache::remember("posts.find.{$id}", now()->addHours(24), function () use ($id) {
            return $this->post->findOrFail($id);
        });
    }

    public function findBySlug(string $slug)
    {
        return Cache::remember("posts.slug.{$slug}", now()->addHours(24), function () use ($slug) {
            return $this->post->where('slug', $slug)->firstOrFail();
        });
    }

    public function findByCategory(int $categoryId)
    {
        return Cache::remember("posts.category.{$categoryId}", now()->addHours(24), function () use ($categoryId) {
            return $this->post->where('category_id', $categoryId)->get();
        });
    }

    public function create(array $data)
    {
        $post = $this->post->create($data);
        $this->clearCache();
        return $post;
    }

    public function update(int $id, array $data)
    {
        $post = $this->find($id);
        $post->update($data);
        $this->clearCache();
        return $post;
    }

    public function delete(int $id)
    {
        $result = $this->find($id)->delete();
        $this->clearCache();
        return $result;
    }

    public function uploadFeaturedImage(int $id, $file)
    {
        $post = $this->find($id);

        // Gunakan Spatie Media Library untuk menyimpan gambar unggulan
        $post->addMedia($file)->toMediaCollection('featured_images');

        return $post->getFirstMediaUrl('featured_images');
    }

    public function uploadGalleryImages(int $id, array $files)
    {
        $post = $this->find($id);
        foreach ($files as $file) {
            $post->addMedia($file)->toMediaCollection('gallery');
        }

        return $post->getMedia('gallery')->map(function ($media) {
            return $media->getUrl();
        });
    }

    private function clearCache()
    {
        Cache::forget('posts.all');
        Cache::forget('posts.published');
    }
}
