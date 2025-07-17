@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Achats de produit')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col">
            <div  class="card-title justify-content-between align-items-center">
                <h2 >Rapport des achats de produit </h2>
            </div>
        </div>
        <div class="page-title-right col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
            <div class="input-group  border-1 border border-light rounded" id="datepicker1">
                <input type="text" class="form-control datepicker text-primary ps-2 "
                       id="i_date"
                       placeholder="mm/dd/yyyy"
                       name="i_date" readonly style="z-index: 1000 !important;">
                <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
            </div>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body ">
                    <div class="card-title justify-content-between align-items-center">
                        <h4>Achat de produit</h4>
                        <hr>
                    </div>

                    <div class="row align-items-end mb-4">
                        <div class=" col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                            <label for="i_search" class="form-label">Recherche de produit</label>
                            <div class="input-group">
                                <span class="input-group-text text-primary"><i class="fas fa-search"></i></span>
                                <input type="text" id="i_search" name="i_search" class="form-control" placeholder="Chercher par référence ou désignation">
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                            <label for="i_type" class="form-label">Documents inclus</label>
                            <select name="i_type" class="form-control" id="i_type" multiple>
                                @foreach($types as $type)
                                    <option @if(in_array($type,$types_inclues)) selected @endif value="{{$type}}">@lang('achats.'.$type)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class=" col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                            <button id="i_search_button" class="btn btn-soft-secondary">Appliquer</button>
                        </div>
                    </div>

                    <hr>
                    <div class="col-12 ">
                        <div>
                            <table  id="datatable" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>SKU</th>
                                    <th>Désignation</th>
                                    <th>Type de Document</th>
                                    <th>Document</th>
                                    <th>Date</th>
                                    <th>Quantité</th>
                                    <th>Prix Total</th>
                                </tr>
                                </thead>
                            </table>
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
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script>
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
            'Trimestre 1':['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}','{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2':['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3':['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4':['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{$date_picker_start}}';
        const __datepicker_end_date = '{{$date_picker_end}}';
        const __datepicker_min_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_max_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';
        $('.datepicker').daterangepicker({
            ranges: __datepicker_dates,
            locale: {
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
            },
            startDate: __datepicker_start_date,
            endDate: __datepicker_end_date,
            minDate: __datepicker_min_date,
            maxDate: __datepicker_max_date
        })
        $('#i_date').change(function () {
            $(this).closest('form').submit()
        })
    </script>
    <script>
        $('#i_type').select2({
            minimumResultsForSearch : -1,
            multiple:!0,
        })
    </script>
    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            { data: 'sku' },
            { data: 'designation' },
            { data: 'type_document' },
            { data: 'reference_vente' },
            { data: 'date_document' },
            { data: 'quantite' },
            { data: 'total' },
        ];
        const __dataTable_ajax_link = "{{ route('rapports.achat-produit') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            i_date:'#i_date',
            i_search: '#i_search',
            i_types : '#i_type',
        }
        const __dataTable_filter_trigger_button_id = '#i_search_button';
        $('#i_date').change(function () {
            table.ajax.reload();
        })
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
@endpush

