@extends('layouts.main')
@section('document-title', 'List des dépenses')
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
          <div class="d-flex  justify-content-between align-items-center" >
              <h5 class="m-0"><i class="mdi  mdi-shopping me-2 text-success"></i>Dépenses
              </h5>
              <div class="page-title-right">
                  <a href="{{route('depenses.ajouter')}}">
                      <button class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter</button>
                  </a>

                  <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer</button>
              </div>
          </div>
          <hr class="border">
        </div>
          <!-- #####--Filtres --##### -->
          <div class="switch-filter row px-3 d-none mt-2 mb-4">
              <div class="card-title col-12">
                  <div class="d-flex justify-content-between align-items-center">
                      <h5 class="m-0">Filtres</h5>
                  </div>
                  <hr class="border">
              </div>



              <div class="col-sm-3 col-12 mb-3">
                  <label for="categorie_select" class="form-label">Catégorie</label>

                      <select
                              class="select2 form-control mb-3 custom-select"
                              id="categorie_select"
                              name="i_categorie">
                      </select>
              </div>

              <div class="col-sm-3 col-12 mb-3">
                  <label class="form-label" for="date_operation">Date d'opération</label>
                  <div class="input-group" id="datepicker1">
                      <input type="text" class="form-control datepicker " id="date_operation"
                             placeholder="mm/dd/yyyy"
                             name="date" readonly>
                      <span class="input-group-text"><i class="mdi mdi-calendar"></i></span>
                  </div>
              </div>
              <div class="col-sm-3 col-12 mb-3">
                  <label class="form-label" for="ttc-input">Montant</label>
                  <input type="number" class="form-control" id="montant-input"
                         name="montant">
              </div>

              <div class="col-sm-3 col-12 mb-3">
                  <label class="form-label" for="statut-paiement-select">Statut de paiement</label>
                  <select class="select2 form-control mb-3 custom-select "  id="statut-paiement-select">
                      <option value=""></option>
                      @foreach($status_paiement as  $s)
                          <option value="{{$s}}">{{ucfirst(__('ventes.'.$s))}}</option>
                      @endforeach
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
              <div class="col-12 d-flex justify-content-end">
                  <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                  </button>
              </div>
          </div>

          <!-- #####--DataTable--##### -->
        <div class="row">
          <div class="card-title switch-filter d-none col-12">
              <div class="d-flex justify-content-between align-items-center">
                  <h5 class="m-0">Listes des dépenses</h5>
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
                              <th>Référence</th>
                              <th>Nom de dépense</th>
                              <th>Catégorie</th>
                              <th>Bénéficiaire</th>
                              <th>Montant</th>
                              <th>Date d'opération</th>
                              <th style="max-width: 100px">Statut de paiement</th>
                              <th>Contrôle</th>
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
        @php
            use Carbon\Carbon;
            $exercice = session()->get('exercice')
        @endphp

        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {data: 'nom_depense', name: 'nom_depense'},
            {
                data: function(row) {
                    return row.categorie ? row.categorie.nom : '';
                },
                name: 'categorie.nom'
            },
            {data: 'pour', name: 'pour'},
            {data: 'montant', name: 'montant'},
            {data: 'date_operation', name: 'date_operation'},
            {data: 'statut_paiement', name: 'statut_paiement'},
            {data: 'is_controled', name: 'is_controled'},

            {data: 'actions', name: 'actions', orderable: false,},
        ];

        const __dataTable_ajax_link = "{{ route('depenses.liste') }}";
        const __dataTable_id = "#datatable";


        const __dataTable_filter_inputs_id = {
            categories_id: '#categorie_select',
            date_operation: '#date_operation',
            montant: '#montant-input',
            statut_paiement: '#statut-paiement-select',
            statut_controle: '#statut-controle-select'
        }
        const __sort_column = 6;


        const __dataTable_filter_trigger_button_id = '#search-btn';


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


    <script>
        $('#categorie_select').select2({
            width: '100%',
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: !0,
            ajax: {
                url: "{{ route('categories.select') }}",
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
        $('#statut-paiement-select').select2({
            width: '100%',
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: !0,
            minimumResultsForSearch: -1,
            selectOnClose: false
        })
    </script>

    <script  src="{{asset('js/dataTable_init.js')}}"></script>
    <script>
        $('.filter-btn').click(e => {
            $('.switch-filter').toggleClass('d-none')
        })


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

    </script>

@endpush
