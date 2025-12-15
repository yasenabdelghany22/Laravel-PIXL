<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth'])->group(function () {
    Route::get('/home', [PostController::class, 'index'])->name('posts.index');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');

    Route::scopeBindings()->group(function () {
        Route::post('/{profile:handle}/status/{post}/reply', [PostController::class, 'reply'])->name('posts.reply');
        Route::post('/{profile:handle}/status/{post}/repost', [PostController::class, 'repost'])->name('posts.repost');
        Route::post('/{profile:handle}/status/{post}/quote', [PostController::class, 'quote'])->name('posts.quote');
        Route::post('/{profile:handle}/status/{post}/destroy', [PostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/{profile:handle}/status/{post}/like', [PostController::class, 'like'])->name('posts.like');
        Route::post('/{profile:handle}/status/{post}/unlike', [PostController::class, 'like'])->name('posts.unlike');
    });

    Route::post('/{profile:handle}/follow', [ProfileController::class, 'follow'])->name('profile.follow');
    Route::post('/{profile:handle}/unfollow', [ProfileController::class, 'follow'])->name('profile.unfollow');
});

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dev/login', function (Request $request) {
//    $user = User::query()->inRandomOrder()->first();
    $user = User::query()->first('id', 5);

    Auth::login($user);
    $request->session()->regenerate();

    return redirect()->intended(route('profile.show', $user->profile));
})->name('dev.login');

Route::get('/dev/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerate();

    return redirect()->intended('/feed');
})->name('dev.logout');

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
            'replies' => [
                [
                    'profile' => [
                        'avatar' => '/images/adrian.png',
                        'displayName' => 'Adrian',
                        'handle' => '@adre_22'
                    ],
                    'postedDateTime' => '1h',
                    'content' => <<<str
                        <p>Heh — this looks just like me!</p>
                    str,
                    'likeCount' => 10,
                    'replyCount' => 20,
                    'repostCount' => 30,
                ]
            ]
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
            'replies' => [
                [
                    'profile' => [
                        'avatar' => '/images/adrian.png',
                        'displayName' => 'Adrian',
                        'handle' => '@adre_22'
                    ],
                    'postedDateTime' => '1h',
                    'content' => <<<str
                        <p>Heh — this looks just like me!</p>
                    str,
                    'likeCount' => 10,
                    'replyCount' => 20,
                    'repostCount' => 30,
                ]
            ]
        ],
    ]));

    return view('profile', compact(['feedItems']));
});

Route::get('/{profile:handle}', [ProfileController::class, 'show'])->name('profile.show');
Route::get('/{profile:handle}/with_replies', [ProfileController::class, 'replies'])->name('profile.replies');

Route::scopeBindings()->group(function () {
    Route::get('/{profile:handle}/status/{post}', [PostController::class, 'show'])->name('posts.show');
});


