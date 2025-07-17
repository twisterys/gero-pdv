<?php

namespace Database\Seeders;

use App\Models\Reference;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReferencesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $references = [
            [
                "nom" => "Clients",
                "type" => "clt",
                "longueur_compteur" => 4,
                "template" => "CLT-[n]",
            ],
            [
                "nom" => "Fournisseurs",
                "type" => "fr",
                "longueur_compteur" => 4,
                "template" => "FS-[n]",
            ],
            [
                "nom" => "Commerciaux",
                "type" => "cms",
                "longueur_compteur" => 4,
                "template" => "CMS-[n]",
            ],
            [
                "nom" => "Articles",
                "type" => "art",
                "longueur_compteur" => 4,
                "template" => "ART-[n]",
            ],
            [
                "nom" => "Devis",
                "type" => "dv",
                "longueur_compteur" => 4,
                "template" => "DEV-[n]",
            ],
            [
                "nom" => "Bons de commande",
                "type" => "bc",
                "longueur_compteur" => 4,
                "template" => "BC-[n]",
            ],
            [
                "nom" => "Bon de livraison",
                "type" => "bl",
                "longueur_compteur" => 4,
                "template" => "BL-[n]",
            ],
            [
                "nom" => "Bons de retour",
                "type" => "br",
                "longueur_compteur" => 4,
                "template" => "BR-[n]",
            ],
            [
                "nom" => "Factures",
                "type" => "fa",
                "longueur_compteur" => 4,
                "template" => "FA-[n]",
            ],
            [
                "nom" => "Factures proforma",
                "type" => "fp",
                "longueur_compteur" => 4,
                "template" => "FP[n]",
            ],
            [
                "nom" => "Avoirs",
                "type" => "av",
                "longueur_compteur" => 4,
                "template" => "AV-[n]",
            ],
            [
                "nom" => "Devis d'achat",
                "type" => "dva",
                "longueur_compteur" => 4,
                "template" => "DEVA[n]",
            ],
            [
                "nom" => "Bons de commande d'achat",
                "type" => "bca",
                "longueur_compteur" => 4,
                "template" => "BCA-[n]",
            ],
            [
                "nom" => "Bon de reception d'achat",
                "type" => "bla",
                "longueur_compteur" => 4,
                "template" => "BLA-[n]",
            ],
            [
                "nom" => "Bons de retour d'achat",
                "type" => "bra",
                "longueur_compteur" => 4,
                "template" => "BRA-[n]",
            ],
            [
                "nom" => "Factures d'achat",
                "type" => "faa",
                "longueur_compteur" => 4,
                "template" => "FAA-[n]",
            ],
            [
                "nom" => "Avoirs d'achat",
                "type" => "ava",
                "longueur_compteur" => 4,
                "template" => "AVA-[n]",
            ],
            [
                "nom" => "Depenses ",
                "type" => "dpa",
                "longueur_compteur" => 4,
                "template" => "DEP-[n]",
            ]
        ];
        foreach ($references as $reference){
            Reference::where('type',$reference['type'])->firstOr(function () use ($reference){
                Reference::create($reference);
            });
        }
    }
}
