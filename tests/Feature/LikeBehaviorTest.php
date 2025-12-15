<?php

use App\Models\Like;
use App\Models\Post;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('profile can like a post', function () {
   $post = Post::factory()->create();
   $profile = Profile::factory()->create();

   $like = Like::createLike($post, $profile);

   expect($post->likes)->toHaveCount(1)
        ->and($post->likes->contains($like))->toBeTrue()
        ->and($profile->likes)->toHaveCount(1)
        ->and($profile->likes->contains($like))->toBeTrue()
        ->and($like->post->is($post))->toBeTrue()
       ->and($like->profile->is($profile))->toBeTrue();
});

test('cannot create duplicate likes', function () {
    $post = Post::factory()->create();
    $profile = Profile::factory()->create();

    $l1 = Like::createLike($post, $profile);
    $l2 = Like::createLike($post, $profile);

    expect($l1->id)->toBe($l2->id)
        ->and($post->likes)->toHaveCount(1);
});

test('can remove a like', function () {
    $post = Post::factory()->create();
    $profile = Profile::factory()->create();

    $like = Like::createLike($post, $profile);

    $success = Like::removeLike($post, $profile);

    expect($post->likes)->toHaveCount(0)
        ->and($post->likes->contains($like))->toBeFalse()
        ->and($profile->likes)->toHaveCount(0)
        ->and($profile->likes->contains($like))->toBeFalse()
        ->and($success)->toBeTrue();
});
