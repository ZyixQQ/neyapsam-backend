<?php

namespace Database\Seeders;

use App\Enums\SuggestionStatus;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\Suggestion;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@neyapsam.app'],
            [
                'name' => 'Admin User',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $users = User::factory(20)->create();
        $users->prepend($admin);

        $categories = [
            [
                'name' => 'Ne Izlesem',
                'slug' => 'ne-izlesem',
                'icon' => '🎬',
                'color' => '#F97316',
                'subcategories' => [
                    ['name' => 'Dramatik', 'slug' => 'dramatik', 'icon' => '😢'],
                    ['name' => 'Romantik', 'slug' => 'romantik', 'icon' => '❤️'],
                    ['name' => 'Komedi', 'slug' => 'komedi', 'icon' => '😂'],
                    ['name' => 'Aksiyon', 'slug' => 'aksiyon', 'icon' => '💥'],
                    ['name' => 'Korku', 'slug' => 'korku', 'icon' => '👻'],
                    ['name' => 'Gerilim', 'slug' => 'gerilim', 'icon' => '🧠'],
                    ['name' => 'Belgesel', 'slug' => 'belgesel', 'icon' => '🎥'],
                    ['name' => 'Animasyon', 'slug' => 'animasyon', 'icon' => '✨'],
                ],
            ],
            [
                'name' => 'Ne Yesem',
                'slug' => 'ne-yesem',
                'icon' => '🍽',
                'color' => '#DC2626',
                'subcategories' => [
                    ['name' => 'Pratik', 'slug' => 'pratik', 'icon' => '⚡'],
                    ['name' => 'Saglikli', 'slug' => 'saglikli', 'icon' => '🥗'],
                    ['name' => 'Kolay', 'slug' => 'kolay', 'icon' => '👌'],
                    ['name' => 'Tatli', 'slug' => 'tatli', 'icon' => '🍰'],
                ],
            ],
            [
                'name' => 'Ne Oynasam',
                'slug' => 'ne-oynasam',
                'icon' => '🎮',
                'color' => '#2563EB',
                'subcategories' => [
                    ['name' => 'Hikaye', 'slug' => 'hikaye', 'icon' => '📖'],
                    ['name' => 'Rekabetci', 'slug' => 'rekabetci', 'icon' => '🏆'],
                    ['name' => 'Rahat', 'slug' => 'rahat', 'icon' => '🌿'],
                    ['name' => 'Mobil', 'slug' => 'mobil', 'icon' => '📱'],
                ],
            ],
            [
                'name' => 'Ne Desem',
                'slug' => 'ne-desem',
                'icon' => '💬',
                'color' => '#7C3AED',
                'subcategories' => [
                    ['name' => 'Buz Kirici', 'slug' => 'buz-kirici', 'icon' => '🧊'],
                    ['name' => 'Flort', 'slug' => 'flort', 'icon' => '😉'],
                    ['name' => 'Arkadasca', 'slug' => 'arkadasca', 'icon' => '🤝'],
                    ['name' => 'Komik', 'slug' => 'komik-sozler', 'icon' => '🤣'],
                ],
            ],
        ];

        foreach ($categories as $categoryIndex => $categoryData) {
            $category = Category::updateOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    'name' => $categoryData['name'],
                    'icon' => $categoryData['icon'],
                    'color' => $categoryData['color'],
                    'sort_order' => $categoryIndex,
                    'is_active' => true,
                ]
            );

            foreach ($categoryData['subcategories'] as $subcategoryIndex => $subcategoryData) {
                $subcategory = Subcategory::updateOrCreate(
                    ['slug' => $subcategoryData['slug']],
                    [
                        'category_id' => $category->id,
                        'name' => $subcategoryData['name'],
                        'icon' => $subcategoryData['icon'],
                        'sort_order' => $subcategoryIndex,
                        'is_active' => true,
                    ]
                );

                if ($subcategory->suggestions()->exists()) {
                    continue;
                }

                $suggestionCount = fake()->numberBetween(5, 10);

                for ($index = 0; $index < $suggestionCount; $index++) {
                    $user = $users->random();
                    $upvotes = fake()->numberBetween(3, 80);
                    $downvotes = fake()->numberBetween(0, 30);

                    Suggestion::create([
                        'subcategory_id' => $subcategory->id,
                        'user_id' => $user->id,
                        'title' => Str::limit(fake()->sentence(4), 120, ''),
                        'description' => fake()->optional(0.7)->sentence(12),
                        'upvote_count' => $upvotes,
                        'downvote_count' => $downvotes,
                        'net_score' => $upvotes - $downvotes,
                        'status' => SuggestionStatus::Approved,
                        'is_featured' => $index === 0,
                        'created_at' => now()->subDays(fake()->numberBetween(0, 20)),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }
}
