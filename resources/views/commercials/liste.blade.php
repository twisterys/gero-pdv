@extends('layouts.main')
@section('document-title', 'Commerciaux')
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
                        <div class="d-flex  justify-content-between align-items-center">
                            <h5 class="m-0"><i class="mdi mdi-contacts  me-2 text-success"></i>Commerciaux</h5>
                            <div class="page-title-right">
                                <a href="{{ route('commercials.ajouter') }}">
                                    <button class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter</button>
                                </a>
                                <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer</button>
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
                            <label class="form-label" for="reference-input">Référence</label>
                            <input type="text" class="form-control" id="reference-input">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="ice-input">ICE</label>
                            <input type="text" class="form-control" id="ice-input">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="nom-input">Dénomination</label>
                            <input type="text" class="form-control" id="nom-input">
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                            </button>
                        </div>
                    </div>
                    <!-- #####--DataTable--##### -->
                    <div class="row">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Listes des commerciaux</h5>
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

                                            {{-- <th>ID</th> --}}
                                            <th>Référence</th>
                                            <th>Type</th>
                                            <th>Dénomination</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
                                            <th>Secteur</th>
                                            <th>Actions</th>
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

@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script>
        const __dataTable_columns =[
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {data: 'type_commercial', name: 'type_commercial'},
            {data: 'nom', name: 'Dénomination'},
            {data: 'email', name: 'email'},
            {data: 'telephone', name: 'telephone'},
            {data: 'secteur', name: 'secteur'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('commercials.liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            nom: '#nom-input',
            reference: '#reference-input',
            ice: '#ice-input',
            created_at:'#datepicker2'
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
        const __datepicker_dates = {
            "Aujourd'hui": ['{{\Carbon\Carbon::today()->format('d/m/Y')}}','{{\Carbon\Carbon::today()->format('d/m/Y')}}' ],
            'Hier': ['{{\Carbon\Carbon::yesterday()->format('d/m/Y')}}', '{{\Carbon\Carbon::yesterday()->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{\Carbon\Carbon::today()->subDays(6)->format('d/m/Y')}}', '{{\Carbon\Carbon::today()->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{\Carbon\Carbon::today()->subDays(29)->format('d/m/Y')}}','{{\Carbon\Carbon::today()->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{\Carbon\Carbon::today()->firstOfMonth()->format('d/m/Y')}}', '{{\Carbon\Carbon::today()->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{\Carbon\Carbon::today()->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{\Carbon\Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{\Carbon\Carbon::today()->firstOfYear()->format('d/m/Y')}}', '{{\Carbon\Carbon::today()->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{\Carbon\Carbon::today()->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_end_date= '{{\Carbon\Carbon::today()->lastOfYear()->format('d/m/Y')}}';
        const __datepicker_min_date = '{{\Carbon\Carbon::today()->setYear('1930')->format('d/m/Y')}}';
        const __datepicker_max_date = '{{\Carbon\Carbon::today()->format('d/m/Y')}}';
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
    @vite('resources/js/clients_liste.js')
@endpush
