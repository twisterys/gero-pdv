<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport journalier (simple)</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Pure minimal print-friendly styles (no frameworks) */
        body { font-family: Arial, sans-serif; font-size: 12px; color: #000; margin: 16px; }
        h1, h2 { margin: 8px 0; }
        .meta { margin: 8px 0 16px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 16px; }
        th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
        th { text-align: left; }
        .right { text-align: right; }
        .center { text-align: center; }
        .no-print { margin: 8px 0 16px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
<div class="no-print">
    <button onclick="window.print()">Imprimer</button>
    <a href="{{ route('rapports.journalier', ['date' => $date, 'magasin_id' => $magasinId]) }}" style="margin-left:8px;">Version avec interface</a>
</div>

<h1>Rapport journalier</h1>
<div class="meta">
    <div>Date: <strong>{{ $date }}</strong></div>
    <div>Magasin: <strong>{{ optional($magasins->firstWhere('id', (int)$magasinId))->nom ?? '—' }}</strong></div>
</div>

{{-- 1) Vente Client / Article --}}
<h2>Vente Client / Article</h2>
@if(!empty($ac))
    <table>
        <thead>
        <tr>
            <th>Client \\ Article</th>
            @foreach($ac['articles'] ?? [] as $art)
                <th class="center">{{ $art }}</th>
            @endforeach
            <th class="right">Total TTC</th>
            <th class="right">Total Payé</th>
            <th class="right">Total de Créance</th>
        </tr>
        </thead>
        <tbody>
        @foreach(($ac['clients'] ?? []) as $cl)
            @php
                $totClient = $ac['client_totals'][$cl] ?? ['total_ttc' => 0, 'total_paye' => 0];
                $creance = ($totClient['total_ttc'] ?? 0) - ($totClient['total_paye'] ?? 0);
            @endphp
            <tr>
                <td>{{ $cl }}</td>
                @foreach(($ac['articles'] ?? []) as $art)
                    @php
                        $cell = $ac['data'][$cl][$art] ?? ['quantite'=>0,'total_ttc'=>0];
                        $q = (float)($cell['quantite'] ?? 0);
                        $ttc = (float)($cell['total_ttc'] ?? 0);
                    @endphp
                    <td class="center">
                        @if($q != 0 || $ttc != 0)
                            {{ (floor($q)===$q ? (int)$q : $q) }}
                        @endif
                    </td>
                @endforeach
                <td class="right">{{ number_format($totClient['total_ttc'] ?? 0, 2, ',', ' ') }}</td>
                <td class="right">{{ number_format($totClient['total_paye'] ?? 0, 2, ',', ' ') }}</td>
                <td class="right">{{ number_format($creance, 2, ',', ' ') }}</td>
            </tr>
        @endforeach
        <tr>
            <td><strong>Total</strong></td>
            @foreach(($ac['articles'] ?? []) as $art)
                <td></td>
            @endforeach
            <td class="right"><strong>{{ number_format($ac['totals']['total_ttc'] ?? 0, 2, ',', ' ') }}</strong></td>
            <td class="right"><strong>{{ number_format($ac['totals']['total_paye'] ?? 0, 2, ',', ' ') }}</strong></td>
            <td class="right"><strong>{{ number_format($ac['totals']['total_creance'] ?? 0, 2, ',', ' ') }}</strong></td>
        </tr>
        </tbody>
    </table>
@else
    <div>Aucune donnée</div>
@endif

{{-- 2) Article / Fournisseur --}}
<h2>Article / Fournisseur</h2>
@if(!empty($af))
    <table>
        <thead>
        <tr>
            <th>Fournisseur \\ Article</th>
            @foreach($af['articles'] ?? [] as $art)
                <th>{{ $art }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($af['fournisseurs'] ?? [] as $fz)
            <tr>
                <td>{{ $fz }}</td>
                @foreach($af['articles'] ?? [] as $art)
                    @php $cell = $af['data'][$fz][$art] ?? ['quantite'=>0,'total_ttc'=>0]; @endphp
                    <td>{{ ($cell['quantite'] != 0) ? $cell['quantite'] : '' }}</td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <div>Aucune donnée</div>
@endif

{{-- 3) Paiements et Créances (détaillé) --}}
<h2>Paiements et Créances</h2>
@if(!empty($cr) && !empty($cr['rows']))
    <table>
        <thead>
        <tr>
            <th>Référence</th>
            <th>Magasin de vente</th>
            <th>Client</th>
            <th>Méthode de Paiement</th>
            <th>Date de Paiement</th>
            <th>N° Chèque/LCN</th>
            <th>Date vente</th>
            <th>Contrôlé</th>
            <th>Montant Payé</th>
            <th>Montant Total</th>
            <th>Montant Créance</th>
        </tr>
        </thead>
        <tbody>
        @foreach($cr['rows'] as $row)
            <tr>
                <td>{{ $row['reference'] }}</td>
                <td>{{ $row['magasin_name'] }}</td>
                <td>{{ $row['client_name'] }}</td>
                <td>{{ $row['last_payment_method'] ?? '—' }}</td>
                <td>{{ $row['last_payment_date'] ?? '—' }}</td>
                <td>{{ $row['cheque_lcn_reference'] ?? '—' }}</td>
                <td>{{ $row['sale_date'] }}</td>
                <td>{{ ($row['is_controled'] ?? false) ? 'Oui' : 'Non' }}</td>
                <td class="right">{{ number_format($row['total_paiement_today'] ?? 0, 2, ',', ' ') }}</td>
                <td class="right">{{ number_format($row['total_ttc'] ?? 0, 2, ',', ' ') }}</td>
                <td class="right">{{ number_format($row['creance_amount'] ?? 0, 2, ',', ' ') }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="9"><strong>Total des paiements</strong></td>
            <td colspan="2" class="right"><strong>{{ number_format($cr['total_paiements'] ?? 0, 2, ',', ' ') }}</strong></td>
        </tr>
        </tbody>
    </table>
@else
    <div>Aucune donnée</div>
@endif

{{-- 4) Dépenses par catégorie --}}
<h2>Dépenses par catégorie</h2>
@if(!empty($depenses))
    <table>
        <thead>
        <tr>
            <th>Catégorie</th>
            <th class="right">Montant</th>
        </tr>
        </thead>
        <tbody>
        @forelse(($depenses['items'] ?? []) as $row)
            <tr>
                <td>{{ $row['categorie'] }}</td>
                <td class="right">{{ number_format($row['montant'] ?? 0, 2, ',', ' ') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="center">Aucune dépense</td>
            </tr>
        @endforelse
        <tr>
            <td><strong>Total</strong></td>
            <td class="right"><strong>{{ number_format($depenses['total'] ?? 0, 2, ',', ' ') }}</strong></td>
        </tr>
        </tbody>
    </table>
@else
    <div>Aucune donnée</div>
@endif

{{-- 5) Trésorerie --}}
<h2>Trésorerie</h2>
<table>
    <thead>
    <tr>
        <th>Description</th>
        <th class="right">Jour</th>
        <th class="right">Créance</th>
        <th class="right">Total</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>Total ventes</td>
        <td colspan="2"></td>
        <td class="right">{{ number_format($tr['total_vente_jour'] ?? 0, 2, ',', ' ') }}</td>
    </tr>
    <tr>
        <td>Espèces</td>
        <td class="right">{{ number_format($tr['total_espece_jour'] ?? 0, 2, ',', ' ') }}</td>
        <td class="right">{{ number_format($tr['total_espece_creance'] ?? 0, 2, ',', ' ') }}</td>
        <td class="right">{{ number_format($tr['total_espece'] ?? 0, 2, ',', ' ') }}</td>
    </tr>
    <tr>
        <td>Chèques</td>
        <td class="right">{{ number_format($tr['total_cheque_jour'] ?? 0, 2, ',', ' ') }}</td>
        <td class="right">{{ number_format($tr['total_cheque_creance'] ?? 0, 2, ',', ' ') }}</td>
        <td class="right">{{ number_format($tr['total_cheque'] ?? 0, 2, ',', ' ') }}</td>
    </tr>
    <tr>
        <td>LCN</td>
        <td class="right">{{ number_format($tr['total_lcn_jour'] ?? 0, 2, ',', ' ') }}</td>
        <td class="right">{{ number_format($tr['total_lcn_creance'] ?? 0, 2, ',', ' ') }}</td>
        <td class="right">{{ number_format($tr['total_lcn'] ?? 0, 2, ',', ' ') }}</td>
    </tr>
    <tr>
        <td>Dépenses</td>
        <td colspan="2"></td>
        <td class="right">{{ number_format($tr['total_depenses'] ?? 0, 2, ',', ' ') }}</td>
    </tr>
    <tr>
        <td><strong>Reste en caisse</strong></td>
        <td colspan="2"></td>
        <td class="right"><strong>{{ number_format($tr['reste_en_caisse'] ?? 0, 2, ',', ' ') }}</strong></td>
    </tr>
    </tbody>
</table>

<script>
    (function() {
        const redirectUrl = "{{ route('rapports.journalier', ['date' => $date, 'magasin_id' => $magasinId]) }}";

        function goBack() {
            if (window.__returned) return;
            window.__returned = true;
            try {
                window.location.replace(redirectUrl);
            } catch (e) {
                window.location.href = redirectUrl;
            }
        }

        // Primary: when the print dialog closes
        window.addEventListener('afterprint', goBack);

        // Fallback: on window focus after printing (Chrome often refocuses the tab after the dialog closes)
        window.addEventListener('focus', function() {
            if (window.__printingDone) {
                goBack();
            }
        });

        // Trigger print on load and arm fallbacks
        window.addEventListener('load', function () {
            // Small delay to ensure rendering is complete before invoking print
            setTimeout(function () {
                try { window.print(); } catch (e) { /* no-op */ }

                // Mark that we've attempted printing; used by the focus fallback
                window.__printingDone = true;

                // Last-resort timer: if neither afterprint nor focus triggers, navigate back after a short delay
                setTimeout(goBack, 2000);
            }, 0);
        });
    })();
</script>
</body>
</html>
