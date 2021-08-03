<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('coins')->insert([
                [
                    'value' => 5,
                    'stock' => 100
                ],
                [
                    'value' => 10,
                    'stock' => 50
                ],
                [
                    'value' => 25,
                    'stock' => 15
                ],
                [
                    'value' => 100,
                    'stock' => 25
                ]
            ]
        );
    }
}
