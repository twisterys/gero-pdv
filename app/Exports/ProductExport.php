<?php

namespace App\Exports;

use App\Models\Article;
use App\Models\TransactionStock;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Events\AfterSheet;
class ProductExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize, WithStrictNullComparison
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $magasin_id;

    public function __construct($magasin_id)
    {
        $this->magasin_id = $magasin_id;
    }

    public function headings(): array {
        return [
            'Référence article *',
            'Designation *',
            'Quantité actuelle *',
            'Nouvelle quantité'
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getColumnDimension('A')->setWidth(25);
                $event->sheet->getColumnDimension('B')->setWidth(30);
                $event->sheet->getColumnDimension('C')->setWidth(35);
                $event->sheet->getColumnDimension('D')->setWidth(35);
            },
        ];
    }


    public function collection()
    {
        $data = [];
        $magasin_id=$this->magasin_id;
        $o_articles = Article::with('stock')->where('stockable', '=', '1')->get();

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
                ''
            ];
        }
        return collect($data);
    }

}
