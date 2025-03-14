<?php

namespace Database\Seeders;

use App\Models\Photo;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Yuri',
            'is_active' => 1,
            'email' => 'yuri.nethsienanda@gmail.com',
            'email_verified_at' => now(),
            'photo_id' => Photo::inRandomOrder()->first()->id,
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        DB::table('users')->insert([
            'name' => 'Tom',
            'is_active' => 1,
            'email' => 'syntraprogrammeurs@gmail.com',
            'email_verified_at' => now(),
            'photo_id' => 2,
            'password' => Hash::make('12345678'),
            'created_at' => now(),
            'updated_at' => now(),
            ]);
        User::factory(50)->create();
    }
}
