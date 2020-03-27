<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
        [
            'username' => 140145145,
            'type' => 0,
            'cn_name' => '陈科锦',
            'en_name' => 'TAN KEL ZIN',
            'password' => Hash::make('010801-10-0351'),
        ],
        [
            'username' => 140204204,
            'type' => 0,
            'cn_name' => '陈伟辰',
            'en_name' => 'TAN WEIU CHENG',
            'password' => Hash::make('010527-14-0579'),
        ],
        [
            'username' => 119999,
            'type' => 0,
            'cn_name' => '教务处',
            'en_name' => 'JIAO WU CHU',
            'password' => Hash::make('111111-00-2222'),
        ],
        ]);
    }
}