<?php

namespace Database\Seeders;

use App\Models\Achat;
use App\Models\Module;
use App\Models\Vente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = array_merge(Vente::TYPES,Achat::TYPES);
        foreach ($types as $type){
            Module::where('type',$type)->firstOr(function () use ($type){
                Module::create([
                    'type'=>$type,
                    'action_stock' =>  null,
                    'action_paiement'=>null,
                    'active' => true
                ]);
            });
        }
        Module::whereIn('type',['fa','bra'])->update(['action_stock' => 'sortir']);
        Module::whereIn('type',['faa','br'])->update(['action_stock' => 'entrer']);
        Module::whereIn('type',['fa','ava','fp'])->update(['action_paiement' => 'encaisser']);
        Module::whereIn('type',['av','faa','fpa'])->update(['action_paiement' => 'decaisser']);
    }
}
