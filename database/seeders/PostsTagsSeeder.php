<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Tag;

class PostsTagsSeeder extends Seeder
{
    public function run(): void {
        $tags = Tag::all();

        Post::all()->each(function ($post) use ($tags) {
            $randomTags = $tags->random(rand(2, 4))->pluck('id');
            $post->tags()->syncWithoutDetaching($randomTags);
        });
    }
}
