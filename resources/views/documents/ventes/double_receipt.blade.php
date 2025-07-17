<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            font-family: Tahoma !important;
        }
        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            position: relative;
        }
        .half-page {
            width: 100%;
            height: 50%;
            box-sizing: border-box;
            padding: 10px;
            position: relative;
        }
        .divider {
            border-top: 1px dashed #000;
            text-align: center;
            padding: 5px 0;
            margin: 10px 0;
        }
        .divider-text {
            background-color: white;
            padding: 0 10px;
        }
        @media print {
            .page {
                page-break-after: always;
            }
            .half-page {
                height: 140mm; /* Ajustement pour l'impression */
            }
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

<div class="page">
    <!-- Première moitié (Copie client) -->
    <div class="half-page">
        <div style="display: flex;justify-content: space-between">
            <p>
                {!! $o_vente->client && $o_vente->client->nom ? 'client : ' .$o_vente->client->nom : '' !!}
            </p>
            <p>
                {!! $o_vente->magasin && $o_vente->magasin->nom ? 'magasin : ' . $o_vente->magasin->nom : 'magasin' !!}
            </p>
        </div>
        {!! str_replace(array_keys($functions),$functions,$template) !!}
    </div>

    <!-- Séparation -->
    <div class="divider">
        <span class="divider-text"></span>
    </div>

    <!-- Deuxième moitié (Copie vendeur) -->
    <div class="half-page">
        <div style="display: flex;justify-content: space-between">
            <p>
                {!! $o_vente->client && $o_vente->client->nom ? 'client : ' .$o_vente->client->nom : '' !!}
            </p>
            <p>
                {!! $o_vente->magasin && $o_vente->magasin->nom ? 'magasin : ' . $o_vente->magasin->nom : 'magasin' !!}
            </p>
        </div>

        {!! str_replace(array_keys($functions),$functions,$template) !!}
    </div>
</div>

</body>
</html>
