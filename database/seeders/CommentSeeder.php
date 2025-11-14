<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Comment;

class CommentSeeder extends Seeder
{
    public function run() {
        for ($i = 0; $i < 10; $i++) {
            Comment::create([
                'post_id' => rand(1, 25),
                'user_id' => rand(1, 3),
                'comment_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'
            ]);
        }
    }
}

