@extends('layouts.main')
@section('document-title', 'Clients')
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
                                <i class="mdi mdi-contacts  me-2 text-success"></i>Clients
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('clients.ajouter') }}">
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
                                <h5 class="m-0">Listes des clients</h5>
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
                                            <th>Dénomination</th>
                                            <th>Forme juridique</th>
                                            <th>ICE</th>
                                            <th>Email</th>
                                            <th>Téléphone</th>
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
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {data: 'nom', name: 'nom'},
            {
                data: function(row) {
                    return row.forme_juridique ? row.forme_juridique.nom : '--';
                },
                name: 'forme_juridique.nom'
            },
            {data: 'ice', name: 'ice'},
            {data: 'email', name: 'email'},
            {data: 'telephone', name: 'telephone'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('clients.liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            nom: '#nom-input',
            reference: '#reference-input',
            ice: '#ice-input',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
    @vite('resources/js/clients_liste.js')
@endpush
