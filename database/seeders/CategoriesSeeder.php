<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoriesSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void {
        $parents = [
            'Technology',
            'Lifestyle',
            'Education',
            'Health',
            'Business',
        ];

        foreach ($parents as $parentName) {
            $parent = Category::create([
                'name' => $parentName,
                'slug' => Str::slug($parentName),
                'parent_category' => true
            ]);

            $children = match ($parentName) {
                'Technology' => ['Programming', 'AI', 'Gadgets', 'Cybersecurity'],
                'Lifestyle' => ['Travel', 'Food', 'Fashion', 'Home'],
                'Education' => ['E-learning', 'Science', 'Mathematics'],
                'Health' => ['Fitness', 'Nutrition', 'Mental Health'],
                'Business' => ['Startups', 'Marketing', 'Finance'],
                default => [],
            };

            foreach ($children as $childName) {
                Category::create([
                    'name' => $childName,
                    'slug' => Str::slug($childName),
                    'parent_category' => false,
                    'parent_id' => $parent->id,
                ]);
            }
        }
    }
}