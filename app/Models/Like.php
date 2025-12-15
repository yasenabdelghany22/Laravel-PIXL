<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Like extends Model
{
    /** @use HasFactory<\Database\Factories\LikeFactory> */
    use HasFactory;

    protected $fillable = ['profile_id', 'post_id'];

    public static function createLike(Post $post, Profile $profile)
    {
        return static::firstOrCreate([
            'profile_id' => $profile->id,
            'post_id' => $post->id
        ]);
    }

    public static function removeLike(Post $post, Profile $profile)
    {
        return static::where('profile_id', $profile->id)
            ->where('post_id', $post->id)
            ->delete() > 0;
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }
}
