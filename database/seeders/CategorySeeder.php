<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::create(
            [
                'id' => 1,
                'title' => 'Unknown',
                'description' => 'Sorry but we have no idea where to place this joke',
            ]
        );

        $seedCategories = [
            [
                'title' => 'Dad',
                'description' => 'Dad jokes are always fantastic',
            ],

            [
                'title' => 'Pun',
                'description' => 'Simply so punny youll have to laugh',
            ],
            [
                'title' => 'Pirate',
                'description' => 'Aaaaaarrr me harties',
            ],

        ];

        shuffle($seedCategories);

        foreach ($seedCategories as $seedCategory) {
            Category::create($seedCategory);
        }
    }
}
