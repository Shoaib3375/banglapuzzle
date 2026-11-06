<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use App\Models\User;
use Database\Factories\CategoryFactory;
use Database\Factories\SubcategoryFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Structure:
     *   1 admin user
     *   5 categories
     *   3 subcategories per category  (15 total)
     *   5 products per subcategory    (75 total)
     */
    public function run(): void
    {
        // ── 1. Admin user ────────────────────────────────────────────────────
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@banglapuzzle.com',
            'password'          => Hash::make('password'),
            'is_admin'          => true,
            'email_verified_at' => now(),
        ]);

        // ── 2. Categories ────────────────────────────────────────────────────
        $categories = Category::factory(5)->create();

        // ── 3. Subcategories & Products ──────────────────────────────────────
        foreach ($categories as $category) {
            $subcategories = Subcategory::factory(3)
                ->forCategory($category->id)
                ->create();

            foreach ($subcategories as $subcategory) {
                Product::factory(5)
                    ->forSubcategory($category->id, $subcategory->id)
                    ->create();
            }
        }
    }
}
