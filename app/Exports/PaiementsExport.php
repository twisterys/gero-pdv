<?php

namespace App\Exports;
use App\Models\Achat;
use Carbon\Carbon;

use App\Models\Paiement;
use App\Models\Vente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class PaiementsExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $type;
    protected $magasin_id;

    public function __construct($type, $magasin_id)
    {
        $this->type = $type;
        $this->magasin_id = $magasin_id;
    }
    public function headings(): array {
        return [
            'Payable Référence',
            'Methode de paiement',
            'Montant Payé',
            'Date Paiement',
            'Compte',
            'Chèque/LCN Référence',
            'Chèque/LCN Date',

        ];
    }
    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function(BeforeSheet $event) {
                $columns = ['A', 'B', 'D', 'E', 'F', 'G'];
                foreach ($columns as $column) {
                    $event->sheet->getStyle($column)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_TEXT);
                }
            },

            AfterSheet::class => function(AfterSheet $event) {
                $columns = ['A', 'B', 'C', 'D','E', 'F','G'];

                foreach ($columns as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
                $event->sheet->getStyle('D')->applyFromArray([
                    'numberFormat' => [
                        'formatCode' => NumberFormat::FORMAT_TEXT,
                    ],
                ]);$event->sheet->getStyle('G')->applyFromArray([
                    'numberFormat' => [
                        'formatCode' => NumberFormat::FORMAT_TEXT,
                    ],
                ]);
            },
        ];
    }
    public function collection()
    {
        if($this->type === "achats"){
            $class = Achat::class;
            $montant= 'decaisser';
        }elseif($this->type === "ventes"){
            $montant= 'encaisser';
            $class=Vente::class;
        }
        $o_paiements = Paiement::with('methodePaiement','payable', 'compte')
            ->where('payable_type',$class)
            ->where('magasin_id',$this->magasin_id)
            ->get();
        $data = [];
        foreach ($o_paiements as $paiement) {
                $data[] = [
                    $paiement->payable->reference,
                    $paiement->methodePaiement->nom,
                    $paiement->$montant,
                    $paiement->date_paiement,
                    $paiement->compte->nom,
                    $paiement->cheque_lcn_reference,
                    $paiement->cheque_lcn_date ? Carbon::parse($paiement->cheque_lcn_date)->format('d/m/Y') : null
                ];
        }
        return collect($data);
    }
}
