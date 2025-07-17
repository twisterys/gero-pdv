<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::where('email','admin@gero.ma')->firstOr(function (){
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gero.ma',
                'password' => Hash::make('password1'),
                'email_verified_at' => now(),
            ]);
        });
    }
}
