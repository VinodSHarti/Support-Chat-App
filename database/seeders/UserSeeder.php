<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Vinod (Customer)',
                'is_agent' => false,
                'api_token' => Str::random(32),
            ],
            [
                'name' => 'Pramod (Customer)',
                'is_agent' => false,
                'api_token' => Str::random(32),
            ],
            [
                'name' => 'Nagaraj (Agent)',
                'is_agent' => true,
                'api_token' => Str::random(32),
            ],
            [
                'name' => 'Girish (Agent)',
                'is_agent' => true,
                'api_token' => Str::random(32),
            ],
            [
                'name' => 'Pavan (Customer)',
                'is_agent' => false,
                'api_token' => Str::random(32),
            ],
        ]);
    }
}
