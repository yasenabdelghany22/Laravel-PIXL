<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/feed', function () {
    $feedItems = json_decode(json_encode([
        [
            'profile' => [
                'avatar' => '/images/michael.png',
                'displayName' => 'Michael',
                'handle' => '@mmich_jj'
            ],
            'postedDateTime' => '3h',
            'content' => <<<str
                <p>
                    I made this! <a href="#">#myartwork</a> <a href="#">#pixl</a>
                </p>
                <img src="/images/simon-chilling.png" alt="" />
            str,
            'likeCount' => 23,
            'replyCount' => 45,
            'repostCount' => 10,
        ],
    ]));

    return view('feed', compact(['feedItems']));
});

Route::get('/profile', function () {
    $feedItems = json_decode(json_encode([
        [
            'profile' => [
                'avatar' => '/images/michael.png',
                'displayName' => 'Michael',
                'handle' => '@mmich_jj'
            ],
            'postedDateTime' => '3h',
            'content' => <<<str
                <p>
                    I made this! <a href="#">#myartwork</a> <a href="#">#pixl</a>
                </p>
                <img src="/images/simon-chilling.png" alt="" />
            str,
            'likeCount' => 23,
            'replyCount' => 45,
            'repostCount' => 10,
        ],
    ]));

    return view('profile', compact(['feedItems']));
});
