@extends('layouts.main')
@section('document-title', 'Cheques à encaisser')
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
                                <i class="mdi mdi-contacts  me-2 text-success"></i>Chéques à encaisser
                            </h5>
                            <div class="page-title-right">
                                <button data-bs-target="#saveChequeModal" data-bs-toggle="modal"
                                        class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter
                                </button>

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
                            <label for="numero-filters" class="form-label required">Numéro de cheque</label>
                            <input type="text" class="form-control" id="numero-filters"/>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label for="client-filters" class="form-label required">Client</label>
                            <select required
                                    class="select2 form-control mb-3 custom-select "
                                    id="client-filters">
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label for="compte-filters" class="form-label">Compte bancaire</label>
                            <select
                                    class="form-select"
                                    style="width: 100%" id="compte-filters">
                                <option value="">Tous</option>
                                @foreach($comptes as $compte)
                                    <option value="{{$compte->id}}">{{$compte->nom}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label for="banque-filters" class="form-label">Banque émettrice</label>
                            <select
                                    class="form-select"
                                    style="width: 100%" id="banque-filters">
                                <option value="">Tous</option>
                                @foreach($banques as $banque)
                                    <option value="{{$banque->id}}">{{$banque->nom}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label for="statut-filters" class="form-label">Statut</label>
                            <select
                                    class="form-select"
                                    style="width: 100%" id="statut-filters">
                                <option value="">Tous</option>
                                @foreach($statuts as $statut)
                                    <option value="{{$statut}}">@lang('cheques.'.$statut)</option>
                                @endforeach
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
                                <h5 class="m-0">Listes des chéques</h5>
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

                                        <th>Numéro</th>
                                        <th>Client</th>
                                        <th>Date d'émission</th>
                                        <th>Date d'échéance</th>
                                        <th>Banque émettrice</th>
                                        <th>Compte bancaire</th>
                                        <th>Statut</th>
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

    <div class="modal fade" id="saveChequeModal" tabindex="-1" aria-labelledby="saveChequeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="saveChequeModalForm" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="saveChequeModalLable">Encaisser le chèque</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="numero_transaction" class="form-label required">Numéro de cheque</label>
                            <input type="text" class="form-control" id="numero_transaction" name="numero_transaction"/>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="client_id" class="form-label required">Client</label>
                            <select required
                                    class="select2 form-control mb-3 custom-select "
                                    id="client_id" name="client_id">
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="date_emission" class="form-label required">Date d'émission</label>
                            <div class="input-group">
                                <input required class="form-control datupickeru" data-provide="datepicker"
                                       data-date-autoclose="true"
                                       type="text" name="date_emission" id="date_emission">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="date_echeance" class="form-label required">Date d'écheance</label>
                            <div class="input-group">
                                <input required class="form-control datupickeru" data-provide="datepicker"
                                       data-date-autoclose="true"
                                       type="text" name="date_echeance" id="date_echeance">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="montant_encaisse" class="form-label required" >Montant encaissé</label>
                            <input type="number" class="form-control" id="montant_encaisse" name="montant_encaisse"
                                   required>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="compte_bancaire" class="form-label">Compte bancaire</label>
                            <select name="i_compte_id"
                                    class="form-select"
                                    style="width: 100%" id="i_compte_id">
                                <option value="">Compte bancaire</option>
                                @foreach($comptes as $compte)
                                    <option value="{{$compte->id}}">{{$compte->nom}}</option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="banque" class="form-label required">Banque émettrice</label>
                            <select name="banque" id="banque" class="form-select">
                                @foreach($banques as $banque)
                                    <option value="{{$banque->id}}" data-img="{{asset($banque->image)}}">
                                        {{$banque->nom}}
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback"></div>
                        </div>
                        <div class="mb-3">
                            <label for="note" class="form-label">Notes</label>
                            <textarea class="form-control" id="note" name="note" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="updateChequeModal" tabindex="-1" aria-labelledby="updateChequeModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">

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
            {data: 'number', name: 'number'},
            {data: 'client', name: 'client'},
            {data: 'date_emission', name: 'date_emission'},
            {data: 'date_echeance', name: 'date_echeance'},
            {data: 'banque_emettrice', name: 'banque_emettrice'},
            {data: 'compte_bancaire', name: 'compte_bancaire'},
            {data: 'statut', name: 'statut'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('cheques.encaisser_liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            client_id: '#client-filters',
            compte_id: '#compte-filters',
            banque_id: '#banque-filters',
            numero: '#numero-filters',
            statut: '#statut-filters',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
    @vite(['resources/js/cheques_encaisser.js']);
@endpush
