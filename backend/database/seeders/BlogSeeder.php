<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogComment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Sample blog data
        $blogs = [
            [
                'user_id' => 1, // Admin
                'title' => 'Welcome to TounsiVert Community Blog!',
                'content' => "We're thrilled to launch our new community blog platform! This space is dedicated to sharing stories, experiences, and ideas about environmental initiatives across Tunisia. Whether you're organizing a beach cleanup, planting trees, or simply want to share your thoughts on sustainability, this is your platform. Let's build a greener Tunisia together! 🌱",
                'ai_assisted' => false,
                'views_count' => 145,
                'likes_count' => 23,
                'comments_count' => 5,
            ],
            [
                'user_id' => 2, // Organizer
                'title' => 'How We Organized a Successful Tree Planting Event',
                'content' => "Last month, our organization planted over 500 trees in the Ariana region! Here are the key lessons we learned: 1) Start planning early - at least 3 months ahead. 2) Partner with local authorities and schools. 3) Provide proper tools and training. 4) Follow up with maintenance plans. The community response was overwhelming, and we're already planning our next event. If you want to organize something similar, feel free to reach out!",
                'ai_assisted' => false,
                'views_count' => 89,
                'likes_count' => 34,
                'comments_count' => 8,
            ],
            [
                'user_id' => 3, // Member Demo
                'title' => 'My First Volunteer Experience',
                'content' => "I just participated in my first community event through TounsiVert, and it was amazing! We spent the day cleaning up a local park in Ben Arous. Not only did we make a real difference, but I also met so many passionate people. It feels great to contribute to our community. If you're thinking about volunteering, just do it - you won't regret it!",
                'ai_assisted' => false,
                'views_count' => 56,
                'likes_count' => 18,
                'comments_count' => 3,
            ],
            [
                'user_id' => 4, // Member Test1
                'title' => 'The Impact of Small Actions on Our Environment',
                'content' => "We often think that individual actions don't matter, but they do! This week, I started using reusable bags and bottles, and encouraged my family to do the same. Small changes like these, when multiplied across our community, create significant impact. What small changes have you made in your daily life? Share your tips below!",
                'ai_assisted' => true,
                'views_count' => 72,
                'likes_count' => 15,
                'comments_count' => 6,
            ],
            [
                'user_id' => 5, // Member Test2
                'title' => 'Urban Gardening: Growing Green in the City',
                'content' => "Living in Tunis doesn't mean we can't connect with nature! I've started a small balcony garden with herbs and vegetables. It's not only therapeutic but also reduces my carbon footprint. Urban gardening is becoming popular worldwide, and Tunisia should be no exception. Who else is growing their own food? Let's exchange tips and seeds!",
                'ai_assisted' => false,
                'views_count' => 94,
                'likes_count' => 28,
                'comments_count' => 12,
            ],
            [
                'user_id' => 6, // Member Test3
                'title' => 'Why Youth Participation Matters in Environmental Causes',
                'content' => "As a young Tunisian, I believe our generation has the power to change the future. Climate change isn't just a future problem - it's happening now. We need more youth involvement in environmental initiatives. Schools should integrate sustainability into curricula, and organizations should create youth programs. The time to act is now!",
                'ai_assisted' => true,
                'views_count' => 67,
                'likes_count' => 21,
                'comments_count' => 7,
            ],
            [
                'user_id' => 7, // Member Test4
                'title' => 'Beach Cleanup Success: 200kg of Waste Collected!',
                'content' => "Yesterday's beach cleanup in La Marsa was incredible! Over 50 volunteers showed up, and together we collected 200kg of plastic waste. It's heartbreaking to see how much pollution our beaches face, but also inspiring to see so many people care. Big thanks to everyone who participated. Next cleanup is scheduled for next month - mark your calendars!",
                'ai_assisted' => false,
                'views_count' => 112,
                'likes_count' => 45,
                'comments_count' => 9,
            ],
            [
                'user_id' => 8, // Member Test5
                'title' => 'Traditional Tunisian Wisdom for Sustainable Living',
                'content' => "My grandmother taught me that Tunisians have always practiced sustainability - we just didn't call it that. Preserving food, reusing materials, respecting water resources... these weren't trends, they were necessities and values. Maybe the solution to modern environmental challenges lies in rediscovering these traditional practices. What sustainable traditions do you remember from your family?",
                'ai_assisted' => false,
                'views_count' => 81,
                'likes_count' => 32,
                'comments_count' => 11,
            ],
            [
                'user_id' => 9, // nihed
                'title' => 'Tech for Good: How Digital Platforms Help Environmental Causes',
                'content' => "As someone passionate about both technology and the environment, I'm excited about platforms like TounsiVert that use tech to facilitate real-world impact. Digital tools can help us organize events, track progress, build communities, and scale our impact. The future of environmental activism is digital, and Tunisia is ready to lead the way in the MENA region!",
                'ai_assisted' => false,
                'views_count' => 103,
                'likes_count' => 37,
                'comments_count' => 14,
            ],
            [
                'user_id' => 5, // Member Test2 (second blog)
                'title' => 'Composting 101: Turn Your Kitchen Waste into Garden Gold',
                'content' => "Following up on my urban gardening post, let me share how I started composting at home. It's easier than you think! You don't need a big yard - a small balcony works fine. I use a simple bin system for food scraps, and within weeks, I had rich compost for my plants. This reduces waste going to landfills AND improves soil quality. Win-win! Want to start? Here's what you need...",
                'ai_assisted' => true,
                'views_count' => 58,
                'likes_count' => 19,
                'comments_count' => 4,
            ],
        ];

        // Create blogs
        foreach ($blogs as $blogData) {
            $blog = Blog::create($blogData);

            // Add random comments to some blogs
            if ($blogData['comments_count'] > 0) {
                $this->addSampleComments($blog, $blogData['comments_count']);
            }

            // Add random likes to blogs
            if ($blogData['likes_count'] > 0) {
                $this->addSampleLikes($blog, $blogData['likes_count']);
            }
        }

        $this->command->info('Created ' . count($blogs) . ' sample blogs with comments and likes!');
    }

    private function addSampleComments(Blog $blog, int $count): void
    {
        $commentTexts = [
            "Great post! Thanks for sharing.",
            "This is very inspiring. Keep up the good work!",
            "I totally agree with your points.",
            "Where can I sign up for the next event?",
            "We need more people like you in our community.",
            "Excellent initiative! Count me in.",
            "This is exactly what Tunisia needs right now.",
            "Thanks for organizing this. It was amazing!",
            "Can you share more details about this?",
            "I'd love to collaborate on similar projects.",
            "Your passion is contagious! Well done.",
            "This gives me hope for our future.",
        ];

        // Get random users (excluding the blog author)
        $users = User::where('id', '!=', $blog->user_id)->inRandomOrder()->take($count)->get();

        foreach ($users as $index => $user) {
            if ($index < $count) {
                BlogComment::create([
                    'blog_id' => $blog->id,
                    'user_id' => $user->id,
                    'comment' => $commentTexts[array_rand($commentTexts)],
                    'parent_id' => null,
                ]);
            }
        }
    }

    private function addSampleLikes(Blog $blog, int $count): void
    {
        // Get random users (excluding the blog author)
        $users = User::where('id', '!=', $blog->user_id)
            ->inRandomOrder()
            ->take($count)
            ->get();

        foreach ($users as $user) {
            DB::table('blog_likes')->insert([
                'blog_id' => $blog->id,
                'user_id' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
