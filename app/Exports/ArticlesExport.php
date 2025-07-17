<?php

namespace App\Exports;

use App\Models\Article;
use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class ArticlesExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array {
        return [
            'Référence',
            'Designation',
            'Nom Famille',
            'Nom Unité',
            'Prix Vente',
            'Taxe',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $columns = ['A', 'B', 'C', 'D', 'E','F'];

                foreach ($columns as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }


    public function collection()
    {

        $o_products = Article::all();
        $data = [];
        foreach ($o_products as $product) {
            $data[] = [
                '0' => $product->reference,
                '1' => $product->designation,
                '2' => $product->famille->nom ?? null,
                '3' => $product->unite->nom ?? null,
                '4' => $product->prix_vente,
                '5' => $product->taxe,
            ];
        }
        return collect($data);
    }
}
