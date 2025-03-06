<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use App\Repositories\Interfaces\CategoryRepositoryInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function all()
    {
        return Cache::remember('categories.all', now()->addHours(24), function () {
            return $this->category->all();
        });
    }

    public function find(int $id)
    {
        return Cache::remember("categories.find.{$id}", now()->addHours(24), function () use ($id) {
            return $this->category->findOrFail($id);
        });
    }

    public function findBySlug(string $slug)
    {
        return Cache::remember("categories.slug.{$slug}", now()->addHours(24), function () use ($slug) {
            return $this->category->where('slug', $slug)->firstOrFail();
        });
    }

    public function create(array $data)
    {
        $category = $this->category->create($data);
        $this->clearCache();
        return $category;
    }

    public function update(int $id, array $data)
    {
        $category = $this->find($id);
        $category->update($data);
        $this->clearCache();
        return $category;
    }

    public function delete(int $id)
    {
        $result = $this->find($id)->delete();
        $this->clearCache();
        return $result;
    }

    private function clearCache()
    {
        Cache::forget('categories.all');
        // Cache kategori spesifik bisa dihapus di tempat lain, misalnya di event listener
    }
}
