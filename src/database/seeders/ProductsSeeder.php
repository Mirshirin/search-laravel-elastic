<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('en_US');

        for ($i = 0; $i < 200; $i++) {
            $product = Product::create([
                'name' => $faker->word,
                'description' => $faker->paragraph,
                'price' => $faker->randomFloat(2, 10, 999),
                'image' => $this->generateRandomImageName( $faker),
            ]);
        }

        $this->command->info('Generated 20000 products.');
    }

    private function generateRandomImageName( $faker)
    {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $imageName = $faker->word . '.' . $faker->randomElement($imageTypes);
        return $imageName;
    }
}
