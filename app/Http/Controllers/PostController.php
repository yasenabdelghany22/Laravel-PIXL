<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Models\Like;
use App\Models\Post;
use App\Models\Profile;
use App\Queries\PostThreadQuery;
use App\Queries\TimelineQuery;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{

    public function index(): View
    {
        $profile = Auth::user()->profile;

        $posts = TimelineQuery::forViewer($profile)->get();

        return view('posts.index', compact('posts', 'profile'));
    }

    public function show(Profile $profile, Post $post): View
    {
        $post = PostThreadQuery::for($post, Auth::user()?->profile)->load();

        return view('posts.show', ['post' => $post]);
    }

    public function store(CreatePostRequest $request): RedirectResponse
    {
        $profile = $request->user()->profile;
        Post::publish($profile, $request->get('content'));

        return redirect()->route('posts.index');
    }

    public function reply(Profile $profile, Post $post, CreatePostRequest $request): RedirectResponse
    {
        $currentProfile = $request->user()->profile;
        Post::reply($currentProfile, $post, $request->get('content'));

        return redirect()->route('posts.show', ['profile' => $profile, 'post' => $post]);
    }

    public function repost(Profile $profile, Post $post): RedirectResponse
    {
        $currentProfile = Auth::user()->profile;
        Post::repost($currentProfile, $post);

        return redirect(route('posts.index'));
    }

    public function quote(Profile $profile, Post $post, CreatePostRequest $request): RedirectResponse
    {
        $currentProfile = $request->user()->profile;
        Post::repost($currentProfile, $post, $request->get('content'));

        return redirect(route('posts.index'));
    }

    public function like(Profile $profile, Post $post): JsonResponse
    {
        $currentProfile = Auth::user()->profile;
        $like = Like::createLike($post, $currentProfile);

        return response()->json(compact('like'));
    }

    public function unlike(Profile $profile, Post $post): JsonResponse
    {
        $currentProfile = Auth::user()->profile;
        $success = Like::removeLike($post, $currentProfile);

        return response()->json(compact('success'));
    }

    public function destroy(Profile $profile, Post $post): JsonResponse
    {
        $currentProfile = Auth::user()->profile;
        $success = false;

        if ($currentProfile->id === $profile->id) {
            $success = $post->delete() > 0;
            return response()->json(compact('success'));
        }

        $repost = $post->reposts()->where('profile_id', $currentProfile->id)->first();
        if ($repost) {
            $success = $repost->delete() > 0;
            return response()->json(compact('success'));
        }

        return response()->json(compact('success'));
    }
}
