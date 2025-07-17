@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', __('achats.' . $type . '.index.title'))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <style>
        .last-col {
            width: 1%;
            white-space: nowrap;
        }
    </style>
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i class="mdi  mdi-shopping me-2 text-success"></i> @lang('achats.' . $type . 's')
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('achats.ajouter', ['type' => $type]) }}">
                                    <button class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter
                                    </button>
                                </a>
                                <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer
                                </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <!-- #####--Filters--##### -->
                    <div class="switch-filter row px-3 d-none mt-2 mb-4">
                        <div class="card-title col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Filtres</h5>
                            </div>
                            <hr class="border">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="fournisseur-select">Fournisseur</label>
                            <select class="select2 form-control mb-3 custom-select" name="fournisseur_id"
                                    id="fournisseur-select">
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="statut-select">Statut</label>
                            <select class="select2 form-control mb-3 custom-select" id="statut-select">
                                <option value=""></option>
                                @foreach ($status as $s)
                                    <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (in_array($type, $payabale_types))
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label" for="statut-paiement-select">Statut de paiement</label>
                                <select class="select2 form-control mb-3 custom-select " id="statut-paiement-select">
                                    <option value=""></option>
                                    @foreach ($status_paiement as $s)
                                        <option value="{{ $s }}">{{ ucfirst(__('achats.' . $s)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="sku-input">Référence (Externe)</label>
                            <input type="text" class="form-control" id="reference-input" name="sku">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="sku-input">Référence Interne</label>
                            <input type="text" class="form-control" id="reference_interne" name="reference_interne">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="ttc-input">Montant TTC</label>
                            <input type="number" class="form-control" id="ttc-input" name="ttc">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label"
                                   for="date_emission">@lang('achats.' . $type . '.date_emission')</label>
                            <div class="input-group" id="datepicker1">
                                <input type="text" class="form-control datepicker " id="date_emission"
                                       placeholder="mm/dd/yyyy" name="date" readonly>
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                        @if (in_array($type, ['dva', 'faa', 'fpa', 'bca']))
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label"
                                       for="date_expiration">@lang('achats.' . $type . '.date_expiration')</label>
                                <div class="input-group" id="datepicker1">
                                    <input type="text" class="form-control datepicker-expired " id="date_expiration"
                                           placeholder="mm/dd/yyyy" name="date" readonly>
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="balises-select">Étiquette</label>
                            <select multiple class="select2 form-control mb-3 custom-select" id="balises-select">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                            </button>
                        </div>
                    </div> <!-- #####--DataTable--##### -->
                    <div class="row px-3">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">@lang('achats.' . $type . '.index.title')</h5>
                            </div>
                            <hr class="border">
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 20px">
                                            <input type="checkbox" class="form-check-input" id="select-all-row">
                                        </th>
                                        <th style="max-width: 150px">Référence Interne</th>
                                        <th>Référence</th>
                                        <th>Fournisseur</th>
                                        <th>@lang('achats.' . $type . '.date_emission')</th>
                                        <th>Montant TTC</th>
                                        <th style="max-width: 250px">Objet</th>
                                        <th>Statut</th>
                                        @if (in_array($type, ['dva', 'faa', 'fpa', 'bca']))
                                            <th>@lang('achats.' . $type . '.date_expiration')</th>
                                        @endif
                                        @if (in_array($type, $payabale_types))
                                            <th>Paiement</th>
                                        @endif

                                        <th style="max-width: 180px">Actions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="clone-modal" tabindex="-1" aria-labelledby="clone-modal-title"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script>
        const __dataTable_columns = [{
            data: 'selectable_td',
            orderable: false,
            searchable: false,
            class: 'check_sell'
        },
            {
                data: 'reference_interne',
                name: 'reference_interne'
            },
            {
                data: 'reference',
                name: 'reference'
            },
            {
                data: 'fournisseur_id',
                name: 'fournisseur_id'
            },
            {
                data: 'date_emission',
                name: 'date_emission'
            },
            {
                data: 'total_ttc',
                name: 'total_ttc'
            },
            {
                data: 'objet',
                name: 'objet'
            },
            {
                data: 'statut',
                name: 'statut'
            },
                @if (in_array($type, ['dva', 'faa', 'fpa', 'bca']))
            {
                data: 'date_expiration',
                name: 'date_expiration'
            },
                @endif
                @if (in_array($type, $payabale_types))
            {
                data: 'statut_paiement',
                name: 'statut_paiement'
            },
                @endif


                {
                data: 'actions',
                name: 'actions',
                orderable: false,
            },
        ];
        const __dataTable_ajax_link = "{{ route('achats.liste', $type) }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            fournisseur_id: '#fournisseur-select',
            date_emission: '#date_emission',
            statut: '#statut-select',
            @if (in_array($type, $payabale_types))
            statut_paiement: '#statut-paiement-select',
            @endif
                @if (in_array($type, ['dva', 'faa', 'fpa', 'bca']))
            date_expiration: '#date_expiration',
            @endif
            reference: '#reference-input',
            total_ttc: '#ttc-input',
            balises: '#balises-select',
            reference_interne: '#reference_interne'
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
        @php
            $exercice = session()->get('exercice');
        @endphp
        const __datepicker_dates = {
            "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 1': ['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2': ['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3': ['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4': ['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{ Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y') }}';
        const __datepicker_end_date = '{{ Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y') }}';
        const __datepicker_min_date = '{{ Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y') }}';
        const __datepicker_max_date = '{{ Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y') }}';

        const __datepicker_end_date_expired = '{{ Carbon::today()->setYear($exercice)->lastOfYear()->addYear()->format('d/m/Y') }}';
        const __datepicker_max_date_expired = '{{ Carbon::today()->setYear($exercice)->lastOfYear()->addYears(2)->format('d/m/Y') }}';
    </script>
    <script type="module" src="{{ asset('js/dataTable_init.js') }}"></script>
    @vite('resources/js/achats_liste.js')
@endpush
