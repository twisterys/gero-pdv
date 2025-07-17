@php
    use Carbon\Carbon;
@endphp
@extends('layouts.main')

@section('document-title', 'Abonnements')

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
                    <!-- ##### Card Title ##### -->
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i class="mdi mdi-repeat me-2 text-success"></i> Abonnements
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('abonnements.ajouter') }}">
                                    <button class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter</button>
                                </a>
                                <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer</button>
                                <a href="{{ route('abonnements.archives') }}">
                                    <button class="btn btn-soft-secondary"><i class="fa fa-archive"></i> Archive</button>
                                </a>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <!-- ##### Filters ##### -->
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

                        <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                            <label for="article_select" class="form-label required">Type d'abonnement</label>
                            <div class="input-group">
                                <select required
                                        class="select2 form-control mb-3 custom-select {{ $errors->has('article_id') ? 'is-invalid' : '' }} "
                                        id="article_select" name="article_id">
                                </select>
                                @if ($errors->has('article_id'))
                                    <div class="invalid-feedback flex-fill">
                                        {{ $errors->first('article_id') }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="prix">Prix </label>
                            <input type="number" class="form-control" name="prix" id="prix" min="0" step="0.01" placeholder="Prix">
                        </div>


                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="titre">Titre</label>
                            <input type="text" class="form-control" name="titre" id="titre" placeholder="Rechercher par titre">
                        </div>



                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="date_emission">Date d'émission</label>
                            <div class="input-group" >
                                <input type="text" class="form-control datepicker " id="date_abonnement"
                                       placeholder="mm/dd/yyyy"
                                       name="date" readonly value="">
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="date_emission">Date d'expiration</label>
                            <div class="input-group" >
                                <input type="text" class="form-control datepicker " id="date_expiration"
                                       placeholder="mm/dd/yyyy"
                                       name="date" readonly>
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>


                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher</button>
                        </div>
                    </div>

                    <!-- ##### DataTable ##### -->
                    <div class="row px-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 20px">
                                            <input type="checkbox" class="form-check-input" id="select-all-row">
                                        </th>
                                        <th>Type abonnement</th>
                                        <th>Titre</th>
                                        <th>Nom du client</th>
                                        <th>Prix</th>
                                        <th>Date d'abonnement</th>
                                        <th>Date d'expiration</th>
                                        <th>Restant</th>
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
    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>

    <script>
        const __dataTable_columns= [
            { data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell' },
            { data: 'article_id', name: 'article_id' },
            { data: 'titre', name: 'titre' },
            { data: 'client_id', name: 'client_id' },
            { data: 'prix', name: 'prix' },
            { data: 'date_abonnement', name: 'date_abonnement' },
            { data: 'date_expiration', name: 'date_expiration' },

            {
                data: 'remain',
                name: 'remain',
            },

            { data: 'action', name: 'action', orderable: false, searchable: false }
        ];
        const __dataTable_ajax_link= "{{ route('abonnements.liste') }}";
        const __dataTable_id= "#datatable";



        const __dataTable_filter_inputs_id= {
            client_id: '#client-select',
            article_id: '#article_id-input',



            date_abonnement: '#date_abonnement',
            date_expiration: '#date_expiration'
        };

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





        const __datepicker_end_extend_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';





        const __dataTable_filter_trigger_button_id= '#search-btn';

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
            autoUpdateInput: false,
            ranges: __datepicker_dates,
            locale: datepicker_locale,
        }).on("apply.daterangepicker", function (e, picker) {
            // Format both start and end date and display in the input field when dates are selected
            picker.element.val(picker.startDate.format(picker.locale.format) + ' - ' + picker.endDate.format(picker.locale.format));
        });

        $('.filter-btn').click(e => {
            $('.switch-filter').toggleClass('d-none');
        });







        $('#client-select').select2({
            width: '100%',
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: true,
            ajax: {
                url:__client_select2_route,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
                cache: false,
            },
            minimumInputLength: 3
        });


        $("#article_select").select2({
            width: "100%",
            placeholder: "Sélectionnez un article",
            minimumInputLength: 3, // Specify the ajax options for loading the product data
            ajax: {
                // The URL of your server endpoint that returns the product data
                url: "{{ route('article.select') }}",
                cache: true, // The type of request, GET or POST
                type: "GET",
                processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data,
                    };
                },
            },
        });
        $('#search-btn').click(function () {
            const client_id = $('#client-select').val();

            const article_id = $('#article_select').val();

            const prix = $('#prix').val();
            const titre = $('#titre').val();


            let date_abonnement = $('#date_abonnement').val();
            let date_expiration = $('#date_expiration').val();

            $('#datatable').DataTable().ajax.reload(null, false);

            const params = {
                client_id,
                article_id,
                prix,
                titre,
                date_abonnement,
                date_expiration
            };

            $(__dataTable_id).DataTable().ajax.url(__dataTable_ajax_link+ '?' + $.param(params)).load();
        });




    </script>
    <script type="module" src="{{ asset('js/dataTable_init.js') }}"></script>
@endpush
