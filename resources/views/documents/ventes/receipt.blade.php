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
    function lignes_table($o_vente, $cols = null){
        // Define allowed columns and their headers and cell renderers
        $defaultCols = ['art','qte','prix','total'];
        if (!$cols || !is_array($cols) || count($cols) === 0) {
            $cols = $defaultCols;
        }
        // Normalize columns
        $normalized = [];
        foreach ($cols as $c) {
            $c = strtolower(trim($c));
            if ($c !== '') $normalized[] = $c;
        }
        $cols = $normalized;

        // Map for headers and alignments
        $headers = [
            'art'        => ['label' => 'Art',     'align' => 'left'],
            'ref'        => ['label' => 'Réf',     'align' => 'left'],
            'nom'        => ['label' => 'Article', 'align' => 'left'],
            'qte'        => ['label' => 'Qte',     'align' => 'center'],
            'prix'       => ['label' => 'Prix',    'align' => 'right'],
            'reduction'  => ['label' => 'Réduc.',  'align' => 'right'],
            'reduc'      => ['label' => 'Réduc.',  'align' => 'right'],
            'tva'        => ['label' => 'TVA',     'align' => 'right'],
            'total'      => ['label' => 'S.Total', 'align' => 'right'],
        ];

        // Start table
        $table = '<table style="width: 100%; border-collapse: collapse; margin: 10px 0;">';
        $table .= '<thead><tr style="border-bottom: 1px solid #ddd;">';
        foreach ($cols as $col) {
            if (!isset($headers[$col])) { continue; }
            $align = $headers[$col]['align'];
            $label = $headers[$col]['label'];
            $table .= '<th style="text-align:'.$align.'; padding: 5px; font-size: 12px; font-weight: bold;">'.$label.'</th>';
        }
        $table .= '</tr></thead>';

        // Rows
        foreach ($o_vente->lignes as $ligne){
            // Format quantity: remove trailing .000
            $quantite = number_format($ligne->quantite, 3, '.', ' ');
            if (preg_match('/\.000$/', $quantite)) {
                $quantite = preg_replace('/\.000$/', '', $quantite);
            }
            $table .= '<tr style="border-bottom: 1px dotted #eee;">';
            foreach ($cols as $col) {
                switch ($col) {
                    case 'art':
                        $cell = (($ligne->article?->reference) ?? '') . ' | ' . ($ligne->nom_article ?? '');
                        $align = 'left';
                        break;
                    case 'ref':
                        $cell = $ligne->article?->reference ?? '';
                        $align = 'left';
                        break;
                    case 'nom':
                        $cell = $ligne->nom_article ?? '';
                        $align = 'left';
                        break;
                    case 'qte':
                        $cell = $quantite;
                        $align = 'center';
                        break;
                    case 'prix':
                        $unit_ht = isset($ligne->ht) ? $ligne->ht : ($ligne->ht_unitaire ?? 0);
                        $cell = number_format($unit_ht, 3, '.', ' ');
                        $align = 'right';
                        break;
                    case 'reduction':
                    case 'reduc':
                        $unit_reduc = $ligne->reduction_unitaire ?? 0;
                        $cell = number_format($unit_reduc, 3, '.', ' ');
                        $align = 'right';
                        break;
                    case 'tva':
                        // Prefer total TVA for the line if available; else compute from rate
                        $total_tva = $ligne->total_tva ?? null;
                        if ($total_tva === null) {
                            $rate = ($ligne->taxe ?? 0) / 100;
                            $qty = $ligne->quantite ?? 0;
                            $unit_ht = isset($ligne->ht) ? $ligne->ht : ($ligne->ht_unitaire ?? 0);
                            $unit_reduc = $ligne->reduction_unitaire ?? 0;
                            $total_tva = ($qty * max($unit_ht - $unit_reduc, 0)) * $rate;
                        }
                        $cell = number_format($total_tva, 3, '.', ' ');
                        $align = 'right';
                        break;
                    case 'total':
                        $unit_ht = isset($ligne->ht) ? $ligne->ht : ($ligne->ht_unitaire ?? 0);
                        $cell = number_format(($ligne->quantite ?? 0) * $unit_ht, 3, '.', ' ');
                        $align = 'right';
                        break;
                    default:
                        continue 2; // skip unknown column
                }
                $table .= '<td style="padding: 5px; text-align:'.$align.'; font-size: 11px;">'.$cell.'</td>';
            }
            $table .= '</tr>';
        }

        $table .= '</table>';
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
                '[Client]'=> $o_vente->client->nom,
                '[Magasin_adresse]'=>$o_vente->magasin->adresse,
                '[Magasin]'=>$o_vente->magasin->nom,
                '[Total_HT]'=>  number_format($o_vente->total_ht + $o_vente->total_reduction,3,'.',' ').' MAD',
                '[Total_TVA]'=>  number_format($o_vente->total_tva,3,'.',' ').' MAD',
                '[Total_Reduction]'=>  number_format($o_vente->total_reduction,3,'.',' ').' MAD',
                '[Total_TTC]'=>  number_format($o_vente->total_ttc,3,'.',' ').' MAD',
                '[Montant_Paye]'=> number_format($total_paye,3,'.',' ').' MAD',
                '[Montant_Restant]'=> number_format($montant_restant,3,'.',' ').' MAD',
                '[Nom_Revendeur]'=> $nom_revendeur,
            ];

        // Allow custom columns in [Tableau:col1,col2,...] or [Tableau:col1|col2|...]
        $template_processed = preg_replace_callback('/\[Tableau:([^\]]+)\]/', function($m) use ($o_vente) {
            $raw = preg_split('/[,|]/', $m[1]);
            $cols = array_filter(array_map(function($c){ return strtolower(trim($c)); }, $raw));
            return lignes_table($o_vente, $cols);
        }, $template);
@endphp
<div class="receipt-container">

    {!! str_replace(array_keys($functions), $functions, $template_processed ?? $template) !!}
</div>
</body>
</html>
