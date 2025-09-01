@php use App\Services\LimiteService;use Carbon\Carbon; use App\Models\Vente @endphp
@extends('layouts.main')
@section('document-title', __('ventes.'.$type.'.index.title'))
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
                                    class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i> @lang('ventes.'.$type.'s')
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('ventes.ajouter', ['type' => $type]) }}">
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
                            <label for="magasin_id" class="form-label">
                                Magasin
                            </label>
                            <select name="magasin_id" class="form-select " id="magasin-select">
                                <option></option>
                                @foreach ($o_magasins as $o_magasin)
                                    <option value="{{ $o_magasin->id }}">{{ $o_magasin->text }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="client-select">Client</label>
                            <select class="select2 form-control mb-3 custom-select" name="client_id" id="client-select">
                            </select>
                        </div>
                        @if(LimiteService::is_enabled('commerciaux'))
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label" for="commercial-select">Commercial</label>
                                <select class="select2 form-control mb-3 custom-select" name="commercial_id"
                                        id="commercial-select">
                                </select>
                            </div>
                        @endif
                        @if(LimiteService::is_enabled('methode_livraison'))
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label" for="livrasion-select">Méthode de livraison</label>
                                <select class="select2 form-control mb-3 custom-select" name="livraison_id"
                                        id="livraison-select">
                                </select>
                            </div>
                        @endif
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="statut-select">Statut</label>
                            <select class="select2 form-control mb-3 custom-select" id="statut-select">
                                <option value=""></option>
                                @foreach($status as $s)
                                    <option
                                        @selected(($filter === 'brouillon' && $s === 'brouillon' )|| ($filter ==='echeance' && $s === 'validé'))  value="{{$s}}">{{ucfirst($s)}}</option>
                                @endforeach
                            </select>
                        </div>
                        @if(in_array($type,$payabale_types))
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label" for="statut-paiement-select">Statut de paiement</label>
                                <select class="select2 form-control mb-3 custom-select " id="statut-paiement-select">
                                    <option value=""></option>
                                    @foreach($status_paiement as  $s)
                                        <option
                                            @selected(($filter === 'non_paye' || $filter ==='echeance') && $s ==='non_paye' )  value="{{$s}}">{{ucfirst(__('ventes.'.$s))}}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="sku-input">Référence</label>
                            <input type="text" class="form-control" id="reference-input"
                                   name="sku">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="ttc-input">Montant TTC</label>
                            <input type="number" class="form-control" id="ttc-input"
                                   name="ttc">
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label"
                                   for="date_emission">@lang('ventes.'.$type.'.date_emission')</label>
                            <div class="input-group" id="datepicker1">
                                <input type="text" class="form-control datepicker " id="date_emission"
                                       placeholder="mm/dd/yyyy"
                                       name="date" readonly>
                                <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                        @if(in_array($type,['dv','fa','fp','bc']))
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label"
                                       for="date_expiration">@lang('ventes.'.$type.'.date_expiration')</label>
                                <div class="input-group" id="datepicker1">
                                    <input type="text" class="form-control datepicker " id="date_expiration"
                                           placeholder="mm/dd/yyyy"
                                           name="date" readonly>
                                    <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        @endif
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="balises-select">Balise</label>
                            <select multiple class="select2 form-control mb-3 custom-select" id="balises-select">
                                <option value=""></option>
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="statut-controle-select">Statut de contrôle</label>
                            <select class="select2 form-control mb-3 custom-select" id="statut-controle-select">
                                <option value=""></option>
                                <option value="Tous">Tous</option>
                                <option value="controle">Contrôlé</option>
                                <option value="non_controle">Non contrôlé</option>
                            </select>
                        </div>
                        <input type="hidden" id="promesses_a_traiter" name="promesses_a_traiter"
                               value="{{($filter === 'promesses_a_traiter' ? '1' : 0)}}">
                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                            </button>
                        </div>
                    </div>                    <!-- #####--DataTable--##### -->
                    <div class="row px-3">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">@lang('ventes.'.$type.'.index.title')</h5>
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
                                        <th>Client</th>
                                        <th>@lang('ventes.'.$type.'.date_emission')</th>
                                        <th>Montant TTC</th>
                                        <th style="max-width: 250px">Objet</th>
                                        <th>Statut</th>
                                        @if(in_array($type,['dv','fa','fp','bc']))
                                            <th>@lang('ventes.'.$type.'.date_expiration')</th>
                                        @endif
                                    @if(in_array($type,$payabale_types))
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
    <div class="modal fade" id="conversion-modal" tabindex="-1" aria-labelledby="conversion-modal-title"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

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
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {data: 'client_id', name: 'client_id'},
            {data: 'date_emission', name: 'date_emission'},
            {data: 'total_ttc', name: 'total_ttc'},
            {data: 'objet', name: 'objet'},
            {data: 'statut', name: 'statut'},
                @if(in_array($type,['dv','fa','fp','bc']))
            {data: 'date_expiration', name: 'date_expiration'},
                @endif
                @if(in_array($type,$payabale_types))
            {data: 'statut_paiement', name: 'statut_paiement'},
                @endif
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('ventes.liste',$type) }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            client_id: '#client-select',
            commercial_id: '#commercial-select',
            livraison_id: '#livraison-select',
            statut_controle: '#statut-controle-select',
            date_emission: '#date_emission',
            statut: '#statut-select',
            @if(in_array($type,$payabale_types))
            statut_paiement: '#statut-paiement-select',
            @endif
                @if(in_array($type,['dv','fa','fp','bc']))
            date_expiration: '#date_expiration',
            @endif
            reference: '#reference-input',
            balises: '#balises-select',
            total_ttc: '#ttc-input',
            promesses_a_traiter: '#promesses_a_traiter',
            magasin_id: '#magasin-select',
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
        @if($filter === 'echeance')
        const startDate_echeance = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const endDate_echeance = '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}';
        @else
        const startDate_echeance = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const endDate_echeance = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';
        @endif
        const __multiconvert_route = '{{route('ventes.convertir_multi_modal',$type)}}';

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
        $('#date_expiration').daterangepicker({
            ranges: __datepicker_dates,
            locale: datepicker_locale,
            startDate: startDate_echeance,
            endDate: __datepicker_end_extend_date,
            minDate: __datepicker_min_date,
            maxDate: __datepicker_end_extend_date
        })
        $("#magasin-select").select2({
            width: "100%",
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: true,
        });
    </script>
    @vite('resources/js/ventes_liste.js')
    <script src="{{asset('js/dataTable_init.js')}}"></script>

@endpush
