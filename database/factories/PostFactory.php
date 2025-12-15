<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'parent_id' => null,
            'repost_of_id' => null,
            'content' => $this->faker->realText(200),
        ];
    }

    public function reply(Post $post)
    {
        return $this->state([
            'parent_id' => $post->id,
            'content' => $this->faker->realText(200),
        ]);
    }

    public function repost(Post $post)
    {
        return $this->state([
            'repost_of_id' => $post->id,
            'content' => null,
        ]);
    }

    public function quotePost(Post $post)
    {
        return $this->state([
            'repost_of_id' => $post->id,
            'content' => $this->faker->realText(200),
        ]);
    }
}
