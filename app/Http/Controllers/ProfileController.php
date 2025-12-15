<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show(Profile $profile): Factory|View
    {
        $profile->loadCount(['followings', 'followers']);

        $posts = Post::query()->where('profile_id', $profile->id)
            ->whereNull('parent_id')
            ->with([
                'repostOf' => fn($q) => $q->withCount(['replies', 'likes', 'reposts'])
            ])
            ->withCount(['replies', 'likes', 'reposts'])
            ->latest()
            ->get();

        return view('profiles.show', compact('profile', 'posts'));
    }

    public function replies(Profile $profile): Factory|View
    {
        $profile->loadCount(['followings', 'followers']);

        $posts = Post::query()
            ->where(fn($q) => $q
                ->whereBelongsTo($profile, 'profile')
                ->whereNull('parent_id')
            )
            ->orWhereHas('replies', fn($q) => $q
                ->whereBelongsTo($profile, 'profile')
            )
            ->with(['replies' => fn($q) => $q->withCount(['replies', 'likes', 'reposts'])])
            ->with([
                'profile',
                'repostOf' => fn($q) => $q->withCount(['likes', 'reposts', 'replies']),
                'repostOf.profile',
                'parent.profile',
                'replies' => fn($q) => $q->whereBelongsTo($profile, 'profile')->with('profile')->oldest()
            ])
            ->withCount(['replies', 'likes', 'reposts'])
            ->latest()
            ->get();

        return view('profiles.replies', compact('profile', 'posts'));
    }

    public function follow(Profile $profile): JsonResponse
    {
        $currentProfile = Auth::user()->profile;
        $follow = Follow::createFollow($currentProfile, $profile);

        return response()->json(compact('follow'));
    }

    public function unfollow(Profile $profile): JsonResponse
    {
        $currentProfile = Auth::user()->profile;
        $success = Follow::removeFollow($currentProfile, $profile);

        return response()->json(compact('success'));
    }
}
