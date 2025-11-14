<?php

namespace Database\Seeders;

use Database\Seeders\CategoriesSeeder;
use Database\Seeders\TestUsersSeeder;
use Database\Seeders\PostsSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TestUsersSeeder::class,
            CategoriesSeeder::class,
            PostsSeeder::class,
            TagsSeeder::class,
            PostsTagsSeeder::class,
            CategoriesPostsSeeder::class,
            CommentSeeder::class
        ]);
    }
}
