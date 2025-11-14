<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use Illuminate\Support\Str;

class PostsSeeder extends Seeder
{
    public function run(): void {
        $posts = [
            ['The Future of AI', 'Artificial Intelligence is reshaping industries with automation and smart solutions.', 1, [1, 4, 6]],
            ['Top 5 Programming Languages in 2025', 'Discover which programming languages are leading the tech world today.', 2, [1, 4, 6]],
            ['Cybersecurity Basics', 'Learn how to protect your online data from common cyber threats.', 3, [1, 4, 6]],
            ['The Rise of Cloud Computing', 'Cloud services like AWS and Azure are dominating IT infrastructures.', 1, [1, 4, 6]],
            ['Is Blockchain Still Relevant?', 'Beyond crypto, blockchain is transforming business transparency.', 2, [1, 4, 6]],
            ['Online Learning Trends', 'E-learning platforms make education accessible to everyone.', 3, [1, 4, 6]],
            ['How to Stay Focused While Studying', 'Practical tips to boost your concentration during study sessions.', 1, [1, 4, 6]],
            ['The Power of Lifelong Learning', 'Continuous learning helps professionals stay competitive.', 2, [1, 4, 6]],
            ['AI Tutors: The Future of Education', 'Machine learning tools are improving personalized learning.', 3, [1, 4, 6]],
            ['Best Educational Apps in 2025', 'These mobile apps are revolutionizing how students learn.', 1, [1, 4, 6]],
            ['Healthy Habits for Remote Workers', 'Simple daily routines to improve posture and mental health.', 2, [1, 4, 6]],
            ['Benefits of a Balanced Diet', 'Discover how good nutrition impacts your energy and focus.', 3, [1, 4, 6]],
            ['Meditation for Beginners', 'Learn how to start a simple meditation practice to reduce stress.', 1, [1, 4, 6]],
            ['The Truth About Sleep', 'Understanding sleep cycles can improve your productivity.', 2, [1, 4, 6]],
            ['10-Minute Home Workouts', 'Stay fit with quick and effective exercises at home.', 3, [1, 4, 6]],
            ['How to Start a Small Business', 'From planning to execution, here’s what you need to know.', 1, [1, 4, 6]],
            ['The Power of Digital Marketing', 'Learn how SEO and social media grow your business.', 2, [1, 4, 6]],
            ['Why Networking Still Matters', 'Building relationships is key to career success.', 3, [1, 4, 6]],
            ['Financial Planning for Entrepreneurs', 'Tips to manage cash flow and investments wisely.', 1, [1, 4, 6]],
            ['The Future of Remote Work', 'Companies are adopting hybrid models to stay flexible.', 2, [1, 4, 6]],
            ['Minimalism: Living with Less', 'Learn how minimalism can simplify your life and reduce stress.', 3, [1, 4, 6]],
            ['How to Build a Morning Routine', 'Consistency in small habits leads to long-term success.', 1, [1, 4, 6]],
            ['Traveling on a Budget', 'Explore the world without breaking the bank.', 2, [1, 4, 6]],
            ['Home Décor Inspiration', 'Creative ways to refresh your living space.', 3, [1, 4, 6]],
            ['The Art of Slow Living', 'Embrace mindfulness and intentional daily choices.', 1, [1, 4, 6]],
        ];

        foreach ($posts as [$title, $content, $author, $categories]) {
            $post = Post::create([
                'title'   => $title,
                'post_content' => $content,
                'user_id' => $author, 
                'slug'    => Str::slug($title),
            ]);

            $post->categories()->attach($categories);
        }

    }
}
