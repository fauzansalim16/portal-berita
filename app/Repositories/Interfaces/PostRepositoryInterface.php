<?php

namespace App\Repositories\Interfaces;

interface PostRepositoryInterface
{
    public function all();
    public function published();
    public function find(int $id);
    public function findBySlug(string $slug);
    public function findByCategory(int $categoryId);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function uploadFeaturedImage(int $id, $file);
    public function uploadGalleryImages(int $id, array $files);
}