<?php

use App\Models\Post;
use App\Models\Profile;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('allows a profile tp publish a post', function () {
    $profile = Profile::factory()->create();

    $post = Post::publish($profile, 'the post content');

    expect($post->exists)->toBeTrue()
        ->and($post->profile->is($profile))->toBeTrue()
        ->and($post->parent_id)->toBeNull()
        ->and($post->repost_of_id)->toBeNull();
});

test('can reply to post', function () {
    $original = Post::factory()->create();

    $replier = Profile::factory()->create();
    $reply = Post::reply($replier, $original, 'the reply content');

    expect($reply->parent->is($original))->toBeTrue()
        ->and($original->replies)->toHaveCount(1)
        ->and($reply->profile->is($replier))->toBeTrue();
});

test('can have many replies', function () {
    $original = Post::factory()->create();
    $replies = Post::factory()->count(3)->reply($original)->create();


    expect($original->replies)->toHaveCount(3)
        ->and($replies->first()->parent->is($original))->toBeTrue()
        ->and($original->replies->contains($replies->first()))->toBeTrue();
});

test('can create plain repost', function () {
    $original = Post::factory()->create();

    $repostProfile = Profile::factory()->create();
    $repost = Post::repost($repostProfile, $original);

    expect($repost->repostOf->is($original))->toBeTrue()
        ->and($original->reposts)->toHaveCount(1)
        ->and($repost->content)->toBeNull();
});

test('can have many reposts', function () {
    $original = Post::factory()->create();
    $reposts = Post::factory()->count(3)->repost($original)->create();


    expect($original->reposts)->toHaveCount(3)
        ->and($reposts->first()->repostOf->is($original))->toBeTrue()
        ->and($original->reposts->contains($reposts->first()))->toBeTrue();
});

test('can create quote repost', function () {
    $content = 'quote content';
    $original = Post::factory()->create();

    $repostProfile = Profile::factory()->create();
    $repost = Post::repost($repostProfile, $original, $content);

    expect($repost->repostOf->is($original))->toBeTrue()
        ->and($original->reposts)->toHaveCount(1)
        ->and($repost->content)->toBe($content);
});

test('prevent duplicate reposts', function () {
    $original = Post::factory()->create();
    $profile = Profile::factory()->create();

    $r1 = Post::repost($profile, $original);
    $r2 = Post::repost($profile, $original);

    expect($r1->id)->toBe($r2->id);
});

test('remove a repost', function () {
    $original = Post::factory()->create();
    $profile = Post::factory()->repost($original)->create()->profile;

    $success = Post::removeRepost($profile, $original);

    expect($success)->toBeTrue()
        ->and($original->reposts)->toHaveCount(0);
});
