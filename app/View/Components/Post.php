<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Post extends Component
{
    private \App\Models\Post $original;
    /**
     * Create a new component instance.
     */
    public function __construct(
        public \App\Models\Post $post,
        public bool $showEngagement = true,
        public bool $showReplies = false,
    )
    {
        $this->original = $post;
        $this->post = $post->isRepost() && $post->content === null ? $post->repostOf : $post;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.post');
    }
}
