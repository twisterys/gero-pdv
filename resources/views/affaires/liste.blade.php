@php use Carbon\Carbon; use App\Models\Vente @endphp
@extends('layouts.main')
@section('document-title', 'Liste des affaires')
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
                        <div   class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i
                                    class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i> Listes des affaires
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('affaire.ajouter') }}">
                                    <button class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter</button>
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
                            <label class="form-label" for="client-select">Client</label>
                            <select class="select2 form-control mb-3 custom-select" name="client_id" id="client-select">
                            </select>
                        </div>

{{--                        <div class="col-sm-3 col-12 mb-3">--}}
{{--                            <label class="form-label" for="statut-select">Statut</label>--}}
{{--                            <select class="select2 form-control mb-3 custom-select"  id="statut-select">--}}
{{--                                <option value=""></option>--}}
{{--                                @foreach($status as $s)--}}
{{--                                    <option value="{{$s}}">{{ucfirst($s)}}</option>--}}
{{--                                @endforeach--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div class="col-sm-3 col-12 mb-3">--}}
{{--                            <label class="form-label" for="sku-input">Référence</label>--}}
{{--                            <input type="text" class="form-control" id="reference-input"--}}
{{--                                   name="sku">--}}
{{--                        </div>--}}
                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                            </button>
                        </div>
                    </div>                    <!-- #####--DataTable--##### -->
                    <div class="row px-3">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Liste des affaires</h5>
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
                                        <th style="max-width: 150px">Référence</th>
                                        <th>Titre</th>
                                        <th>Client</th>
                                        <th>Statut</th>
                                        <th>Date de début</th>
                                        <th>Date de fin</th>

                                        <th >Budget estimatif</th>
                                        <th >CA global</th>
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
    <div class="modal fade" id="conversion-modal" tabindex="-1" aria-labelledby="conversion-modal-title"
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
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {data: 'titre', name: 'titre'},
            {data: 'client_id', name: 'client_id'},
            {data: 'statut', name: 'statut'},
            {data: 'date_debut', name: 'date_debut'},
            {data: 'date_fin', name: 'date_fin'},
            {data: 'budget_estimatif', name: 'budget_estimatif'},
            {data: 'ca_global', name: 'ca_global'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('affaire.liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            client_id: '#client-select',
            // commercial_id: '#commercial-select',
            // livraison_id: '#livraison-select',
            // date_emission: '#date_emission',
            // statut: '#statut-select',
            // reference: '#reference-input',
            // balises: '#balises-select',
            // total_ttc: '#ttc-input',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
        @php
            $exercice = session()->get('exercice')
        @endphp
         const __datepicker_dates = {
            "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 1':['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}','{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2':['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3':['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4':['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_end_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';
        const __datepicker_min_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_max_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';

    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
    @vite('resources/js/ventes_liste.js')
@endpush
