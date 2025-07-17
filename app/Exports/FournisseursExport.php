<?php

namespace App\Exports;

use App\Models\Client;
use App\Models\Fournisseur;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class FournisseursExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array {
        return [
            'Forme Juridique',
            'Référence',
            'Dénomination',
            'Ice',
            'Email',
            'Telephone',
            'Note',
            'Adresse'

        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $columns = ['A', 'B', 'C', 'D','E', 'F', 'G', 'H'];

                foreach ($columns as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }


    public function collection()
    {
        $formeJuridiqueMapping = [
            4 => ['id' => 4, 'name' => 'S.A.R.L'],
            2 => ['id' => 2, 'name' => 'Société Anonyme'],
            3 => ['id' => 3, 'name' => 'Société Anonyme Simplifiée'],
            5 => ['id' => 5, 'name' => 'Auto Entrepreneur'],
            8 => ['id' => 8, 'name' => 'Société en Commandite par Actions'],
            9 => ['id' => 9, 'name' => 'Société en Commandite Simple'],
            7 => ['id' => 7, 'name' => 'Société en nom collectif'],
            6 => ['id' => 6, 'name' => 'groupement d’intérêt économique'],
            10 => ['id' => 10, 'name' => 'Particulier'],
            1 => ['id' => 1, 'name' => 'Personne Physique'],
        ];

        $o_fournisseurs = Fournisseur::all();
        $data = [];
        foreach ($o_fournisseurs as $fournisseur) {
            $formeJuridique = $formeJuridiqueMapping[$fournisseur->forme_juridique_id];
            $data[] = [
                '0' => $formeJuridique['name'],
                '1' => $fournisseur->reference,
                '2' => $fournisseur->nom,
                '3' => (string) $fournisseur->ice,
                '4' => $fournisseur->email,
                '5' => $fournisseur->telephone,
                '6' => $fournisseur->note,
                '7' => $fournisseur->adresse,
            ];
        }
        return collect($data);
    }
}
