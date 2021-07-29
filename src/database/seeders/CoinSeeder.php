<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coins')->insert(
            [
                'value' => 0.05,
                'stock' => 100
            ],
            [
                'value' => 0.1,
                'stock' => 50
            ],
            [
                'value' => 0.25,
                'stock' => 15
            ],
            [
                'value' => 1,
                'stock' => 25
            ]
        );
    }
}
