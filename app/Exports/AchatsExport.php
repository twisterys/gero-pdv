<?php

namespace App\Exports;
use App\Models\Achat;
use Carbon\Carbon;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

use App\Models\Article;
use App\Models\Vente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AchatsExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $type;
    protected $magasin_id;

    public function __construct($type,$magasin_id)
    {
        $this->type = $type;
        $this->magasin_id = $magasin_id;
    }
    public function headings(): array {
        return [
            'Référence Achat Interne',
            'Référence Achat Externe',
            'Référence Fournisseur',
            'Date Emission',
            'Date Echéance',
            'Référence Article',
            'Nom Article',
            'Prix Achat HT',
            'Nom Unité',
            'Quantité',
            'Type Reduction',
            'Reduction',
            'Taxe',
        ];
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $columns = ['C', 'D'];
                foreach ($columns as $column) {
                    $event->sheet->getStyle($column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                }
            },
            AfterSheet::class => function(AfterSheet $event) {
                $columns = ['A', 'B', 'C', 'D','E', 'F','G','H','I','J','K','L'];
                foreach ($columns as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
                $event->sheet->getStyle('C')->applyFromArray([
                    'numberFormat' => [
                        'formatCode' => NumberFormat::FORMAT_TEXT,
                    ],
                ]);$event->sheet->getStyle('D')->applyFromArray([
                    'numberFormat' => [
                        'formatCode' => NumberFormat::FORMAT_TEXT,
                    ],
                ]);
            },
        ];
    }

    public function collection()
    {
        $o_achats = Achat::with('fournisseur', 'lignes', 'lignes.unite', 'lignes.article')
            ->where('statut', 'validé')
            ->where('type_document', $this->type)
            ->where('magasin_id', $this->magasin_id)
            ->get();
        $data = [];
        foreach ($o_achats as $achat) {
            foreach ($achat->lignes as $ligne){
                $data[] = [
                    $achat->reference_interne,
                    $achat->reference,
                    $achat->fournisseur->reference,
                    $achat->date_emission? Carbon::parse($achat->date_document)->format('d/m/Y') : null,
                    $achat->date_expiration,
                    $ligne->article->reference ?? null,
                    $ligne->nom_article,
                    $ligne->ht,
                    $ligne->unite->nom,
                    $ligne->quantite,
                    $ligne->mode_reduction,
                    $ligne->reduction,
                    $ligne->taxe,
                ];
            }
        }
        return collect($data);
    }

}
