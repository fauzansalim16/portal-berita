<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $post;
    public $action;

    public function __construct(Post $post, string $action)
    {
        $this->post = $post;
        $this->action = $action; // 'created', 'updated', 'deleted'
    }
}