<?php

namespace App\Exports;

use App\Models\Article;
use App\Models\Stock;
use App\Models\TransactionStock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;

class StocksExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize ,WithStrictNullComparison
{


    protected $magasin_id;

    public function __construct($magasin_id)
    {
        $this->magasin_id = $magasin_id;
    }
    public function headings(): array {
        return [
            'Référence Article',
            'Designation',
            'Quantité',
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $columns = ['A', 'B','C'];

                foreach ($columns as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }

    public function collection()
    {
        $data = [];
        $magasin_id=$this->magasin_id;
        $o_articles = Article::with('stock')->get();

        foreach ($o_articles as $article) {
            $qte_entree = TransactionStock::where('article_id', $article->id)
                ->whereHas('stockable', function ($query) use ($magasin_id) {
                    $query->where('magasin_id', $magasin_id);
                })
                ->sum('qte_entree');

            $qte_sortir = TransactionStock::where('article_id', $article->id)
                ->whereHas('stockable', function ($query) use ($magasin_id) {
                    $query->where('magasin_id', $magasin_id);
                })
                ->sum('qte_sortir');

            $quantite = ($qte_entree ?? 0) - ($qte_sortir ?? 0);

            $data[] = [
                $article->reference,
                $article->designation,
                $quantite ,
            ];
        }
        return collect($data);
    }
}
