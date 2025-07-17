@php use Carbon\Carbon;  @endphp
@extends('layouts.main')
@section('document-title',"Transformations")
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
                                <i
                                    class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i> Transformations
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('transformations.ajouter') }}">
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
                            <label class="form-label" for="sku-input">Référence</label>
                            <input type="text" class="form-control" id="reference-input"
                                   name="sku">
                        </div>
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label"
                                       for="date_expiration">Date</label>
                                <div class="input-group" id="datepicker1">
                                    <input type="text" class="form-control datepicker " id="date_expiration"
                                           placeholder="mm/dd/yyyy"
                                           name="date" readonly>
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                            </button>
                        </div>
                    </div>                    <!-- #####--DataTable--##### -->
                    <div class="row px-3">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Liste des transfomrations</h5>
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
                                        <th style="max-width: 150px">Date</th>
                                        <th>Objet</th>
                                        <th style="max-width: 250px">Magasin</th>
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
            {data: 'date', name: 'date'},
            {data: 'object', name: 'object'},
            {data: 'magasin_id', name: 'magasin_id'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('transformations.liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            reference: '#reference-input',
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
            'Trimestre 1': ['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2': ['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3': ['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4': ['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_end_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';
        const __datepicker_min_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_max_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';
        const __datepicker_end_extend_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->addYear()->format('d/m/Y')}}';;
        const datepicker_locale = {
            format: "DD/MM/YYYY",
            separator: " - ",
            applyLabel: "Appliquer",
            cancelLabel: "Annuler",
            fromLabel: "De",
            toLabel: "à",
            customRangeLabel: "Plage personnalisée",
            weekLabel: "S",
            daysOfWeek: [
                "Di",
                "Lu",
                "Ma",
                "Me",
                "Je",
                "Ve",
                "Sa"
            ],
            monthNames: [
                "Janvier",
                "Février",
                "Mars",
                "Avril",
                "Mai",
                "Juin",
                "Juillet",
                "Août",
                "Septembre",
                "Octobre",
                "Novembre",
                "Décembre"
            ],
            firstDay: 1
        }

        $('.datepicker').daterangepicker({
            ranges: __datepicker_dates,
            locale: datepicker_locale,
            startDate: __datepicker_start_date,
            endDate: __datepicker_end_date,
            minDate: __datepicker_min_date,
            maxDate: __datepicker_max_date
        })

    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>

@endpush
