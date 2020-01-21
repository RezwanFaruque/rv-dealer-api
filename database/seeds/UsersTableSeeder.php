<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => env('ADMIN_NAME', 'YELLOW7'),
            'email' => env('ADMIN_EMAIL', 'dev@yellow7.com'),
            'password' => bcrypt(env('ADMIN_PASSWORD', 'secret')),
            'api_token' => '1GdiukCmBaxXVnZj2Sc0wmLBwZgTWXYrfgl4vQddg84W2k7BdjJaIjcKyLTR'
            //'api_token' => Str::random(60),
        ]);
    }
}
