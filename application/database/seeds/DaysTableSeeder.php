<?php

use Illuminate\Database\Seeder;

class DaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('days')->insert([
        [
            'count' => 0,
        ],
        [
            'count' => 0,
        ],
        [
            'count' => 0,
        ],
        [
            'count' => 0,
        ],
        [
            'count' => 0,
        ],
        [
            'count' => 0,
        ],
        [
            'count' => 0,
        ],
        ]);
    }
}