<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <style>
        *{
            font-family: Tahoma !important;
        }
    </style>
</head>
<body>

@php
    function lignes_table($o_vente){
        $table = '<table style="width: 100%;font-size="18pt">';
        $table.='<thead>
<tr>
    <th style="text-align:left" >Art</th>
    <th style="text-align:left">Qte</th>
    <th style="text-align:left">Prix</th>
    <th style="text-align:right">S.Total</th>
</tr>
</thead>';
        foreach ($o_vente->lignes as $ligne){
            $table.= '<tr>
            <td>'.$ligne->article->reference.' | '.$ligne->nom_article.'</td>
            <td>'.$ligne->quantite.'</td>
            <td>'.number_format($ligne->ht,2,'.',' ').'</td>
            <td style="text-align: right">'.number_format($ligne->quantite * $ligne->ht,2,'.',' ').'</td>
            </tr>';
        }

        $table.= '</table>';
        return $table;
    }

        $functions =[
                '[Date_et_heure]'=>now()->toDateTimeString(),
                '[Tableau]'=> lignes_table($o_vente),
                '[Reference]'=> $o_vente->reference,
                '[Magasin_adresse]'=>$o_vente->magasin->adresse,
                '[Magasin]'=>$o_vente->magasin->nom,
                '[Total_HT]'=>  number_format($o_vente->total_ht + $o_vente->total_reduction,2,'.',' ').' MAD',
                '[Total_TVA]'=>  number_format($o_vente->total_tva,2,'.',' ').' MAD',
                '[Total_TTC]'=>  number_format($o_vente->total_ttc,2,'.',' ').' MAD',
            ];
@endphp
{!! str_replace(array_keys($functions),$functions,$template) !!}
<br>
<br>
</body>
</html>


