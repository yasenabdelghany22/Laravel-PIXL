<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    protected $fillable = ['profile_id', 'parent_id', 'content', 'repost_of_id'];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(Post::class, 'parent_id', 'id');
    }

    public function reposts(): HasMany
    {
        return $this->hasMany(Post::class, 'repost_of_id', 'id');
    }

    public function repostOf(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'repost_of_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function isRepost(): bool
    {
        return $this->repost_of_id !== null;
    }

    public static function publish(Profile $profile, string $string): self
    {
        return static::create([
            'profile_id' => $profile->id,
            'content' => $string,
            'parent_id' => null,
            'repost_of_id' => null,
        ]);
    }

    public static function reply(Profile $replier, Post $original, string $string)
    {
        return static::create([
            'profile_id' => $replier->id,
            'content' => $string,
            'parent_id' => $original->id,
            'repost_of_id' => null,
        ]);
    }

    public static function repost(Profile $repostProfile, Post $original, string $string = null)
    {
        return static::firstOrCreate([
            'profile_id' => $repostProfile->id,
            'repost_of_id' => $original->id,
            'content' => $string,
            'parent_id' => null,
        ]);
    }

    public static function removeRepost(Profile $profile, Post $original)
    {
        return static::where('profile_id', $profile->id)->where('repost_of_id', $original->id)
                ->delete() > 0;
    }
}
