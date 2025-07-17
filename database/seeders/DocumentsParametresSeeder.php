<?php

namespace Database\Seeders;

use App\Models\DocumentsParametre;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DocumentsParametresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DocumentsParametre::firstOr(function (){
            DocumentsParametre::create([]);
        });
    }
}
