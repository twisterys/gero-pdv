@extends('layouts.main')
@section('document-title', 'Rapport journalier')

@section('page')
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="mb-0">Rapport Journalier</h4>
                </div>
                <div class="col-auto no-print">
                    <a href="{{ route('rapports.journalier.plain', ['date' => $date ?? now()->format('Y-m-d'), 'magasin_id' => $magasinId ?? ($magasins[0]->id ?? null)]) }}" class="btn btn-primary ms-2">
                        <i class="mdi mdi-printer"></i> Imprimer
                    </a>
                </div>
            </div>

            <div class="row mt-3 justify-content-start no-print">
                <div class="col-12 col-xl-8 col-lg-9">
                    <form method="post" action="{{ route('rapports.journalier.filtrer') }}" class="row g-3 align-items-end">
                        @csrf
                        <div class="col-sm-6 col-md-4 col-lg-4">
                            <label class="form-label">Date</label>
                            <div class="input-group border-1 border border-light rounded">
                                <input type="date" class="form-control" name="date" id="i_date"
                                       value="{{ $date ?? now()->format('Y-m-d') }}">
                                <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-5 col-lg-5">
                            <label class="form-label">Magasin</label>
                            <select name="magasin_id" class="form-select" required>
                                @foreach($magasins as $m)
                                    <option value="{{ $m->id }}" {{ (isset($magasinId) && (int)$magasinId === (int)$m->id) ? 'selected' : '' }}>
                                        {{ $m->nom }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                            <button type="submit" class="btn btn-soft-secondary">Appliquer</button>
                        </div>
                    </form>
                </div>
            </div>
            @if ($errors->any())
                <div class="alert alert-warning mt-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Vente Client / Article --}}
            <div class="card mt-4 mb-3">
                <div class="card-header"><strong>Vente Client / Article</strong></div>
                <div class="card-body">
                    @if(!empty($ac))
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead>
                                <tr>
                                    <th>Client \ Article</th>
                                    @foreach($ac['articles'] ?? [] as $art)
                                        <th class="text-center">{{ $art }}</th>
                                    @endforeach
                                    <th class="text-end">Total TTC</th>
                                    <th class="text-end">Total Payé</th>
                                    <th class="text-end">Total de Créance</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach(($ac['clients'] ?? []) as $cl)
                                    @php
                                        $totClient = $ac['client_totals'][$cl] ?? ['total_ttc' => 0, 'total_paye' => 0];
                                        $creance = ($totClient['total_ttc'] ?? 0) - ($totClient['total_paye'] ?? 0);
                                    @endphp
                                    <tr>
                                        <td class="text-capitalize">{{ $cl }}</td>
                                        @foreach(($ac['articles'] ?? []) as $art)
                                            @php
                                                $cell = $ac['data'][$cl][$art] ?? ['quantite'=>0,'total_ttc'=>0];
                                                $q = (float)($cell['quantite'] ?? 0);
                                                $ttc = (float)($cell['total_ttc'] ?? 0);
                                            @endphp
                                            <td class="text-center">
                                                @if($q != 0 || $ttc != 0)
                                                    {{ (floor($q)===$q ? (int)$q : $q) }}
                                                @endif
                                            </td>
                                        @endforeach
                                        <td class="text-end">{{ number_format($totClient['total_ttc'] ?? 0, 2, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($totClient['total_paye'] ?? 0, 2, ',', ' ') }}</td>
                                        <td class="text-end">{{ number_format($creance, 2, ',', ' ') }}</td>
                                    </tr>
                                @endforeach
                                {{-- Ligne des totaux --}}
                                <tr class="fw-bold bg-soft-light">
                                    <td>Total</td>
                                    @foreach(($ac['articles'] ?? []) as $art)
                                        <td></td>
                                    @endforeach
                                    <td class="text-end">{{ number_format($ac['totals']['total_ttc'] ?? 0, 2, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($ac['totals']['total_paye'] ?? 0, 2, ',', ' ') }}</td>
                                    <td class="text-end">{{ number_format($ac['totals']['total_creance'] ?? 0, 2, ',', ' ') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune donnée</p>
                    @endif
                </div>
            </div>

            {{-- Article / Fournisseur (Achats) --}}
            <div class="card mb-3">
                <div class="card-header"><strong>Article / Fournisseur</strong></div>
                <div class="card-body">
                    @if(!empty($af))
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered table-sm">
                                <thead>
                                <tr>
                                    <th>Fournisseur \ Article</th>
                                    @foreach($af['articles'] ?? [] as $art)
                                        <th>{{ $art }}</th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($af['fournisseurs'] ?? [] as $fz)
                                    <tr>
                                        <td class="text-capitalize">{{ $fz }}</td>
                                        @foreach($af['articles'] ?? [] as $art)
                                            @php $cell = $af['data'][$fz][$art] ?? ['quantite'=>0,'total_ttc'=>0]; @endphp
                                            <td>{{ ($cell['quantite'] != 0) ? $cell['quantite'] : '' }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune donnée</p>
                    @endif
                </div>
            </div>

            {{-- Paiements et Créances (détaillé) --}}
            <div class="card mb-3">
                <div class="card-header"><strong>Paiements et Créances</strong></div>
                <div class="card-body">
                    @if(!empty($cr) && !empty($cr['rows']))
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
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
                                    <th>Montant Total	</th>
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
                                        <td>{!! ($row['is_controled'] ?? false) ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-danger">Non</span>' !!}</td>
                                        <td>{{ $row['total_paiement_today'] }}</td>
                                        <td>{{ $row['total_ttc'] }}</td>
                                        <td >{{ number_format($row['creance_amount'] ?? 0, 2, ',', ' ') }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold bg-light">
                                    <td colspan="9" >Total des paiements</td>
                                    <td colspan="2" class="text-end">{{ number_format($cr['total_paiements'] ?? 0, 2, ',', ' ') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune donnée</p>
                    @endif
                </div>
            </div>
            {{-- Dépenses par catégorie --}}
            <div class="card mb-3">
                <div class="card-header"><strong>Dépenses par catégorie</strong></div>
                <div class="card-body">
                    @if(!empty($depenses))
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm align-middle">
                                <thead>
                                <tr>
                                    <th>Catégorie</th>
                                    <th class="text-end">Montant</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse(($depenses['items'] ?? []) as $row)
                                    <tr>
                                        <td>{{ $row['categorie'] }}</td>
                                        <td class="text-end">{{ number_format($row['montant'] ?? 0, 2, ',', ' ') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-muted text-center">Aucune dépense</td>
                                    </tr>
                                @endforelse
                                <tr class="fw-bold bg-soft-light">
                                    <td>Total</td>
                                    <td class="text-end">{{ number_format($depenses['total'] ?? 0, 2, ',', ' ') }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center mb-0">Aucune donnée</p>
                    @endif
                </div>
            </div>

            {{-- Trésorerie (alignée POS Parfum) --}}

            <div class="card mb-3">
                <div class="card-header"><strong>Trésorerie</strong></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>Description</th>
                                <th class="text-end">Jour</th>
                                <th class="text-end">Créance</th>
                                <th class="text-end">Total</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Total ventes</td>
                                <td colspan="2"></td>
                                <td class="text-end">{{ number_format($tr['total_vente_jour'] ?? 0, 2, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td>Espèces</td>
                                <td class="text-end">{{ number_format($tr['total_espece_jour'] ?? 0, 2, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format($tr['total_espece_creance'] ?? 0, 2, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format($tr['total_espece'] ?? 0, 2, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td>Chèques</td>
                                <td class="text-end">{{ number_format($tr['total_cheque_jour'] ?? 0, 2, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format($tr['total_cheque_creance'] ?? 0, 2, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format($tr['total_cheque'] ?? 0, 2, ',', ' ') }}</td>
                            </tr>

                            <tr>
                                <td>LCN</td>
                                <td class="text-end">{{ number_format($tr['total_lcn_jour'] ?? 0, 2, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format($tr['total_lcn_creance'] ?? 0, 2, ',', ' ') }}</td>
                                <td class="text-end">{{ number_format($tr['total_lcn'] ?? 0, 2, ',', ' ') }}</td>
                            </tr>
                            <tr>
                                <td>Dépenses</td>
                                <td colspan="2"></td>
                                <td class="text-end text-danger">{{ number_format($tr['total_depenses'] ?? 0, 2, ',', ' ') }}</td>
                            </tr>
                            <tr class="fw-bold table-info">
                                <td>Reste en caisse</td>
                                <td colspan="2"></td>
                                <td class="text-end {{ ($tr['reste_en_caisse'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($tr['reste_en_caisse'] ?? 0, 2, ',', ' ') }}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>


            @endsection

@push('styles')
<style>
    /* Hide elements marked as no-print when printing */
    @media print {
        .no-print { display: none !important; }
        /* Avoid breaking cards and tables across pages */
        .card { break-inside: avoid; page-break-inside: avoid; }
        table { break-inside: avoid; page-break-inside: avoid; }
        thead { display: table-header-group; }
        tfoot { display: table-footer-group; }
        body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush
