@php
    use Carbon\Carbon;
@endphp
@extends('layouts.main')
@section('document-title', ucwords($o_commercial->nom))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('commercials.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                    {{ ucwords($o_commercial->nom) }} <span
                                        class="text-muted font-size-10">({{ $o_commercial->reference }})</span>
                                </h5>
                            </div>
                            <div class="pull-right">
                                <a href="{{ route('commercials.modifier', $o_commercial->id) }}"
                                    class="btn btn-soft-warning"><i class="fa fa-edit"></i> Modifier</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card overflow-hidden">
                <div class="card-body overflow-hidden p-0">
                    <div class="row mx-0">
                        <div
                            class="col-xxl-2 col-lg-3 col-md-4 col-12 p-5 py-3 bg-primary text-center d-flex flex-column align-items-center ">
                            <div class="rounded-circle overflow-hidden border border-white border-5  bg-white"
                                style="max-width: 150px">
                                <img src="{{ $o_commercial->image ? route('commercials.image.load', $o_commercial->image) : 'https://placehold.co/150x150?text=' . $o_commercial->reference }}"
                                    class="border-0 w-100" alt="">
                            </div>
                            <h5 class="mb-0 mt-2 text-white text-center">{{ $o_commercial->nom }} </h5>
                            <p class="text-center text-white-50  mb-0">{{ $o_commercial->reference }} -
                                {{ strtoupper($o_commercial->type_commercial) }}</p>
                        </div>
                        <div class=" p-3 row col-xxl-10 col-lg-9 col-md-8 col-12 align-items-start">
                            <div class="col-12 row">
                                <div class="col-xxl-3  col-sm-6 col-12  my-1 my-xxl-0  d-flex align-items-center">
                                    <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-file-invoice fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">@lang('ventes.bcs')</span>
                                        <p class="mb-0 h5 text-black">{{ number_format($commandes, 2, '.', '') }} MAD</p>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-sm-6 col-12 my-1 my-xxl-0 d-flex align-items-center">
                                    <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-dollar-sign text-success fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">Chiffre d'affaires</span>
                                        <p class="mb-0 h5 text-black">{{ number_format($ca, 2, '.', '') }} MAD</p>
                                    </div>
                                </div>
                                <div class="col-xxl-3  col-sm-6 col-12  my-1 my-xxl-0  d-flex align-items-center">
                                    <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-wallet fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">Total encaissement</span>
                                        <p class="mb-0 h5 text-black">{{ number_format($encaissement, 2, '.', '') }} MAD
                                        </p>
                                    </div>
                                </div>
                                <div class="col-xxl-3  col-sm-6 col-12  my-1 my-xxl-0  d-flex align-items-center">
                                    <div class="rounded bg-soft-danger  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-credit-card fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">Commissions</span>
                                        <p class="mb-0 h5 text-black">{{ number_format($commissions, 2, '.', '') }} MAD</p>
                                    </div>
                                </div>
                                <div class="col-12 ">
                                    <hr class="border">
                                </div>
                            </div>
                            <div class="col-lg-6 row m-0">
                                <p class=" col-xxl-12 col-lg-3 col-sm-6"> <i class="fa fa-envelope me-2"></i>
                                    {{ $o_commercial->email ?? '-' }}</p>
                                <p class="text-capitalize  col-xxl-12 col-lg-3 col-sm-6"> <i
                                        class="fa fa-phone fa-flip-horizontal me-2"></i>
                                    {{ $o_commercial->telephone ?? '-' }} </p>
                                <p class="text-capitalize col-xxl-12 col-lg-3 col-sm-6"> <i class="fa fa-building me-2"></i>
                                    {{ $o_commercial->secteur ?? '-' }}</p>
                                <p class="text-capitalize  col-xxl-12 col-lg-3 col-sm-6"> <i
                                        class="fa fa-bullseye me-2"></i> {{ $o_commercial->objectif ?? '-' }} MAD</p>
                            </div>
                            <div class="col-lg-6  m-0">
                                <p class=" "> <b>Commission par défaut :</b>
                                    {{ $o_commercial->commission_par_defaut ?? '-' }} %</p>
                                <p class="text-capitalize "> <b>Note:</b> <br> {{ $o_commercial->note ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Ventes</h5>
                        <hr class="border">
                    </div>
                    <div class="row align-items-end mb-4 ">
                        <div class=" col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                            <label for="i_date">Plage de dates</label>
                            <div class="input-group  border-1 border border-light rounded" id="datepicker1">
                                <input type="text" class="form-control datepicker text-primary ps-2 "
                                       id="i_date"
                                       placeholder="mm/dd/yyyy"
                                       name="i_date" readonly style="z-index: 1 !important;">
                                <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                        <div class=" col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                            <label for="i_type" class="form-label">Documents inclus</label>
                            <select name="i_type" class="form-control" id="i_type" multiple>
                                @foreach($types as $type)
                                    <option @if(in_array($type,$types_inclus)) selected @endif value="{{$type}}">@lang('ventes.'.$type)</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <table id="datatable" class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th style="width: 1%;white-space: nowrap"></th>
                            <th>Référence</th>
                            <th>Date d'emission</th>
                            <th>Montant TTC</th>
                            <th>Comission</th>
                        </tr>
                        </thead>
                    </table>
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
            maxDate: __datepicker_max_date,

        })
    </script>
    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference'},
            {data: 'date_emission'},
            {data: 'total_ttc'},
            {data: 'commission_par_defaut'},
        ];
        const __dataTable_ajax_link = "{{ route('commercials.afficher',$o_commercial->id) }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            i_date: '#i_date',
            i_types: '#i_type'
        }
        $('#i_date , #i_type').change(function () {
            table.ajax.reload();
        })
        $('#i_type').select2({
            minimumResultsForSearch:-1,
            multiple:!0,
        })
    </script>
    <script src="{{asset('js/dataTable_init.js')}}" ></script>
@endpush
