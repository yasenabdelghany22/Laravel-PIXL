<?php

namespace App\Queries;

use App\Models\Post;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostThreadQuery
{
    public function __construct(
        private Post $post,
        private ?Profile $viewer
    )
    {

    }
    public static function for(Post $post, ?Profile $viewer): self
    {
        return new self($post, $viewer);
    }

    public function load(): Post
    {
        $viewerId = $this->viewer->id ?? 0;
        $this->post->load([
            'replies' => function ($query) use ($viewerId) {
                $query
                    ->withCount(['replies', 'likes', 'reposts'])
                    ->withExists([
                        'likes as has_liked' => fn($q) => $q->where('profile_id', $viewerId),
                        'reposts as has_reposted' => fn($q) => $q->where('profile_id', $viewerId)
                    ])
                    ->with([
                        'profile',
                        'parent.profile',
                        'replies' => function($query)  {
                            $query
                                ->withCount(['replies', 'likes', 'reposts'])
                                ->withExists([
                                    'likes as has_liked' => fn($q) => $q->where('profile_id', $viewerId),
                                    'reposts as has_reposted' => fn($q) => $q->where('profile_id', $viewerId)
                                ])
                                ->with(['profile', 'parent.profile'])
                                ->oldest();
                        }
                    ])
                    ->oldest();
            }
        ])->loadCount(['likes', 'reposts', 'replies'])
        ->loadExists([
            'likes as has_liked' => fn($q) => $q->where('profile_id', $viewerId),
            'reposts as has_reposted' => fn($q) => $q->where('profile_id', $viewerId)
        ]);

        return $this->post;
    }
}
