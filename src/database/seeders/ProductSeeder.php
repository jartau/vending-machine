<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
                [
                    'code' => 'WATER',
                    'price' => 65,
                    'stock' => 10,
                ],
                [
                    'code' => 'JUICE',
                    'price' => 100,
                    'stock' => 5,
                ],
                [
                    'code' => 'SODA',
                    'price' => 150,
                    'stock' => 3,
                ]
            ]
        );
    }
}
