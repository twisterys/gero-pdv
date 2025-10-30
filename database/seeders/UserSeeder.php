<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Dashboard;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'admin@gero.ma')->firstOr(function () {
            return User::create([
                'name' => 'Admin',
                'email' => 'admin@gero.ma',
                'password' => Hash::make('password1'),
                'email_verified_at' => now(),
            ]);
        });

        $dashboard = Dashboard::where('function_name', 'pos')->first();

        if ($dashboard && !$user->dashboards->contains($dashboard->id)) {
            $user->dashboards()->attach($dashboard->id);
        }
    }
}
