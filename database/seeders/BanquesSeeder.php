<?php

namespace Database\Seeders;

use App\Models\Banque;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BanquesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banques = [
            [
                'nom'=>'ARAB BANK MAROC',
                'image'=>'images/banques/arab-bank.png',
            ],
            [
                'nom'=>'ATTIJARIWAFA BANK',
                'image'=>'images/banques/atw.png',
            ],
            [
                'nom'=>'AL BARID BANK',
                'image'=>'images/banques/albarid-bank.png',
            ],
            [
                'nom'=>'BANQUE CENTRALE POPULAIRE',
                'image'=>'images/banques/bcp_logo.png',
            ],
            [
                'nom'=>'BANK OF AFRICA',
                'image'=>'images/banques/boa_nouveau_logo.png',
            ],
            [
                'nom'=>'BANQUE MAROCAINE POUR LE COMMERCE ET L’INDUSTRIE',
                'image'=>'images/banques/bmci.png',
            ],
            [
                'nom'=>'CAIXA BANK S. A',
                'image'=>'images/banques/caixabank.png',
            ],
            [
                'nom'=>'CREDIT AGRICOLE DU MAROC',
                'image'=>'images/banques/ca.png',
            ],
            [
                'nom'=>'CFG BANK',
                'image'=>'images/banques/cfg.png',
            ],
            [
                'nom'=>'CDG CAPITAL',
                'image'=>'images/banques/cdg_capital_logo.png',
            ],
            [
                'nom'=>'CREDIT IMMOBILIER ET HOTELIER',
                'image'=>'images/banques/cih.png',
            ],
            [
                'nom'=>'CITIBANK MAGHREB',
                'image'=>'images/banques/citi-bank.png',
            ],
            [
                'nom'=>'CREDIT DU MAROC',
                'image'=>'images/banques/cdm.png',
            ],
            [
                'nom'=>'SABADEL',
                'image'=>'images/banques/sabadell.png',
            ],
            [
                'nom'=>'SOCIETE GENERALE MAROCAINE DE BANQUES',
                'image'=>'images/banques/sgma_nouveau_logo.png',
            ],
            [
                'nom'=>'UNION MAROCAINE DES BANQUES',
                'image'=>'images/banques/umb.png',
            ],
            [
                'nom'=>'BANK ASSAFA',
                'image'=>'images/banques/bank_assafa.png',
            ],
            [
                'nom'=>'AL AKHDAR BANK',
                'image'=>'images/banques/akhdar_bank.png',
            ],
            [
                'nom'=>'BANK AL KARAM',
                'image'=>'images/banques/bank al karam_n.jpg',
            ],
            [
                'nom'=>'BANK AL YOUSR',
                'image'=>'images/banques/Bank_Al_Yousr.png',
            ],
            [
                'nom'=>'UMNIA BANK',
                'image'=>'images/banques/Umnia_Bank.png',
            ],
        ];

        foreach ($banques as $banque){
            Banque::where('nom',$banque['nom'])->firstOr(function () use($banque){
               Banque::create($banque);
            });
        }
    }
}
