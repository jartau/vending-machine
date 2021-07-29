<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('products')->insert(
            [
                'code' => 'WATER',
                'price' => 0.65,
                'stock' => 10,
            ],
            [
                'code' => 'JUICE',
                'price' => 1.00,
                'stock' => 5,
            ],
            [
                'code' => 'SODA',
                'price' => 1.5,
                'stock' => 3,
            ]
        );
    }
}
