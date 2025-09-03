<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            font-family: 'Tahoma', Arial, sans-serif !important;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            padding: 10px;
            font-size: 12px;
            line-height: 1.4;
        }
        .receipt-header {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px dashed #ccc;
        }
        .receipt-header h2 {
            margin: 5px 0;
            font-size: 16px;
        }
        .receipt-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            font-size: 12px;
        }
        .receipt-info div {
            margin-bottom: 5px;
        }
        .receipt-info strong {
            font-weight: bold;
        }
        .receipt-footer {
            margin-top: 15px;
            text-align: center;
            font-size: 11px;
            padding-top: 10px;
            border-top: 1px dashed #ccc;
        }
        .receipt-totals {
            margin-top: 10px;
            text-align: right;
        }
        .receipt-totals div {
            margin: 5px 0;
        }
        .receipt-totals .total-line {
            font-weight: bold;
            font-size: 14px;
        }
        .receipt-signature {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            border-top: 1px dotted #000;
            width: 45%;
            padding-top: 5px;
            text-align: center;
            font-size: 11px;
        }
    </style>
</head>
<body>

@php
    function lignes_table($o_vente){
        $table = '<table style="width: 100%; border-collapse: collapse; margin: 10px 0;">';
        $table.='<thead>
<tr style="border-bottom: 1px solid #ddd;">
    <th style="text-align:left; padding: 5px; font-size: 12px; font-weight: bold;">Art</th>
    <th style="text-align:center; padding: 5px; font-size: 12px; font-weight: bold;">Qte</th>
    <th style="text-align:right; padding: 5px; font-size: 12px; font-weight: bold;">Prix</th>
    <th style="text-align:right; padding: 5px; font-size: 12px; font-weight: bold;">S.Total</th>
</tr>
</thead>';
        foreach ($o_vente->lignes as $ligne){
            $table.= '<tr style="border-bottom: 1px dotted #eee;">
            <td style="padding: 5px; font-size: 11px;">'.$ligne->article->reference.' | '.$ligne->nom_article.'</td>
            <td style="padding: 5px; text-align:center; font-size: 11px;">'.$ligne->quantite.'</td>
            <td style="padding: 5px; text-align:right; font-size: 11px;">'.number_format($ligne->ht,2,'.',' ').'</td>
            <td style="padding: 5px; text-align:right; font-size: 11px;">'.number_format($ligne->quantite * $ligne->ht,2,'.',' ').'</td>
            </tr>';
        }

        $table.= '</table>';
        return $table;
    }

        // Calculate total paid amount from all payments
        $total_paye = 0;
        foreach ($o_vente->paiements as $paiement) {
            $total_paye += $paiement->encaisser;
        }

        $montant_restant = $o_vente->solde;

        $nom_revendeur = auth()->check() ? auth()->user()->name : 'N/A';

        $functions =[
                '[Date_et_heure]'=>now()->format('d/m/Y H:i'),
                '[Tableau]'=> lignes_table($o_vente),
                '[Reference]'=> $o_vente->reference,
                '[Magasin_adresse]'=>$o_vente->magasin->adresse,
                '[Magasin]'=>$o_vente->magasin->nom,
                '[Total_HT]'=>  number_format($o_vente->total_ht + $o_vente->total_reduction,2,'.',' ').' MAD',
                '[Total_TVA]'=>  number_format($o_vente->total_tva,2,'.',' ').' MAD',
                '[Total_TTC]'=>  number_format($o_vente->total_ttc,2,'.',' ').' MAD',
                '[Montant_Paye]'=> number_format($total_paye,2,'.',' ').' MAD',
                '[Montant_Restant]'=> number_format($montant_restant,2,'.',' ').' MAD',
                '[Nom_Revendeur]'=> $nom_revendeur,
            ];
@endphp
<div class="receipt-container">

    {!! str_replace(array_keys($functions),$functions,$template) !!}
</div>
</body>
</html>
