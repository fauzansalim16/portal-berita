<?php

namespace App\Observers;

use App\Events\CategoryChanged;
use App\Models\Category;

class CategoryObserver
{
    public function created(Category $category): void
    {
        event(new CategoryChanged($category, 'created'));
    }

    public function updated(Category $category): void
    {
        event(new CategoryChanged($category, 'updated'));
    }

    public function deleted(Category $category): void
    {
        event(new CategoryChanged($category, 'deleted'));
    }
}