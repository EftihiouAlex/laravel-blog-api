<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Category;

class CategoriesPostsSeeder extends Seeder
{
    public function run(): void {
        $categories = Category::all();

        Post::all()->each(function ($post) use ($categories) {
            $randomCategories = $categories->random(rand(1, 2))->pluck('id');
            $post->categories()->syncWithoutDetaching($randomCategories);
        });
    }
}
