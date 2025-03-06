<?php

namespace App\Events;

use App\Models\Category;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CategoryChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $category;
    public $action;

    public function __construct(Category $category, string $action)
    {
        $this->category = $category;
        $this->action = $action; // 'created', 'updated', 'deleted'
    }
}