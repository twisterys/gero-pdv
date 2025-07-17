<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(ClientSeeder::class);
        $this->call(FournisseurSeeder::class);
        $this->call(CommercialSeeder::class);
        $this->call(ArticlesSeeder::class);
    }
}
