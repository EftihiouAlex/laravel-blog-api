<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagsSeeder extends Seeder
{
    public function run(): void {
        $tags = [
            'Technology',
            'Health',
            'Education',
            'Business',
            'Lifestyle',
            'AI',
            'Programming',
            'Finance',
            'Nutrition',
            'Travel',
        ];

        foreach ($tags as $name) {
            Tag::firstOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
