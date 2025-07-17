<?php

namespace App\Exports;
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

class VentesExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
            'Référence Vente',
            'Référence Client',
            'Date Document',
            'Date Echéance',
            'Référence Article',
            'Nom Article',
            'Prix Vente HT',
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
        $o_ventes = Vente::with('client', 'lignes', 'lignes.unite', 'lignes.article')
            ->where('statut', 'validé')
            ->where('type_document', $this->type)
            ->where('magasin_id', $this->magasin_id)
            ->get();
        $data = [];
        foreach ($o_ventes as $vente) {
            foreach ($vente->lignes as $ligne){
                $data[] = [
                    $vente->reference,
                    $vente->client->reference,
                    $vente->date_document? Carbon::parse($vente->date_document)->format('d/m/Y') : null,
                    $vente->date_expiration,
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
