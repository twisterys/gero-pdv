@php
    use Carbon\Carbon;
@endphp
@extends('layouts.main')
@section('document-title',$o_compte->nom)
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <style>
        .last-col {
            width: unset !important;
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
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center flex-wrap flex-md-nowrap">
                            <div>
                                <a href="{{route('comptes.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3"><i
                                        class="fa  fas fa-university me-2 text-success"></i> {{$o_compte->nom}} @if($o_compte->rib)
                                        <span style="font-size: 11px"
                                              class=" text-muted fw-normal">(<img src="{{asset($o_compte->banque->image )}}" style="display: inline;width: 50px" alt=""> - {{$o_compte->rib}})</span>
                                    @endif </h5>
                            </div>
                            <div class="pull-right d-flex flex-wrap flex-md-nowrap">
                                <div class="input-group mb-2 mb-md-0  border-1 border border-light rounded me-2" id="datepicker1"
                                     style="z-index: 9; min-width: 250px">
                                    <input type="text" class="form-control datepicker text-primary ps-2 "
                                           id="datepicker"
                                           placeholder="mm/dd/yyyy"
                                           name="i_date" readonly>
                                    <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                                </div>
                                <button class="btn btn-soft-info text-nowrap mx-2 " data-bs-target="#paiement-modal"
                                        data-bs-toggle="modal"><i class="fa fa-plus"></i> Opération
                                </button>
                                <a href="{{route('comptes.modifier',$o_compte->id)}}"
                                   class="btn btn-soft-warning text-nowrap"><i class="fa fa-edit"></i> Modifier</a>
                                <button data-url="{{ route('comptes.supprimer',$o_compte->id) }}"
                                        class="btn btn-soft-danger delete text-nowrap mx-2" data-bs-custom-class="danger-tooltip"
                                        data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-danger font-size-10"></div></div>'
                                        data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="Supprimer">
                                    <i class="fa fa-trash-alt"></i>
                                    <span>Supprimer</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 d-flex">
            <div class="card w-100">
                <div class="card-body">
                    <div class="card-title">
                        <h5> Chiffres du compte</h5>
                        <hr class="border">
                    </div>
                    <div id="numbers-container" class="position-relative">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <h5>Liste de transactions</h5>
                        <hr class="border">
                    </div>
                    <div class="row">
                        <div class="col-12 px-4">
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 20px">
                                        <input type="checkbox" class="form-check-input" id="select-all-row">
                                    </th>
                                    <th>
                                        Date de transaction
                                    </th>
                                    <th>
                                        Méthode de paiement
                                    </th>
                                    <th>
                                        Encaissé
                                    </th>
                                    <th>
                                        Decaissé
                                    </th>
                                    <th>
                                        Source
                                    </th>
                                    <th>
                                        Objet
                                    </th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="paiement-modal" tabindex="-1" aria-labelledby="paiement-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
                        Ajouter une opération</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="paiement_form" action="{{ route('paiement.sauvegarder_operation') }}"
                      autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <div class="col-12 mt-3">
                            <label for="date_paiement" class="form-label required">Date de paiement</label>
                            <div class="input-group">
                                <input required class="form-control datupickeru" data-provide="datepicker"
                                       data-date-autoclose="true" type="text" name="i_date_paiement"
                                       value="{{ now()->format('d/m/Y') }}" id="date_paiement">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="compte-input" class="form-label required">Opération</label>
                            <select required name="i_operation_id" class="form-select " style="width: 100%"
                                    id="operation-input">
                                @foreach ($operations as $operation)
                                    <option value="{{ $operation->id }}">{{ $operation->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label for="montant" class="form-label required">Montant de paiement</label>
                            <div class="input-group">
                                <input required class="form-control" step="0.01" min="0.01" type="number"
                                       name="i_montant" id="montant">
                                <span class="input-group-text">MAD</span>
                            </div>
                        </div>
                        <input type="hidden" name="i_compte_id" value="{{$o_compte->id}}">
                        <div class="col-12 mt-3">
                            <label for="method-input" class="form-label required">Méthode de paiement</label>
                            <select required name="i_method_key" class="form-select " style="width: 100%"
                                    id="method-input">
                                @foreach ($methodes as $methode)
                                    @if($o_compte->type === 'banque')
                                        @if($methode->key !=='especes')
                                            <option value="{{ $methode->key }}">{{ $methode->nom }}</option>
                                        @endif
                                    @else
                                        <option value="{{ $methode->key }}">{{ $methode->nom }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3 __variable">
                            <label for="date" class="form-label required">Date prévu</label>
                            <div class="input-group">
                                <input required class="form-control datupickeru" data-provide="datepicker"
                                       data-date-autoclose="true" type="text" name="i_date" id="date">
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                        </div>
                        <div class="col-12 mt-3 __variable">
                            <label for="i_reference" class="form-label required">Référence de chéque</label>
                            <input required class="form-control" type="text" name="i_reference" id="i_reference">
                        </div>
                        <div class="col-12 mt-3">
                            <label for="i_note" class="form-label">Note</label>
                            <textarea name="i_note" id="i_note" cols="30" rows="3" class="form-control"></textarea>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-info">Payer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>

    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'date_paiement', name: 'date_paiement'},
            {data: 'methode_paiement_key', name: 'methode_paiement_key'},
            {data: 'encaisser', name: 'encaisser'},
            {data: 'decaisser', name: 'decaisser'},
            {data: 'payable_id', name: 'payable_id'},
            {data: 'objet', name: 'objet'},
        ];
        const __dataTable_ajax_link = "{{ route('comptes.afficher',$o_compte->id) }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            'date': '#datepicker'
        };
    </script>
    <script>
        @php
            $exercice = session()->get('exercice');
        @endphp
        const __datepicker_dates = {
            "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 1': ['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2': ['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3': ['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4': ['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{Carbon::today()->setYear($exercice)->firstOfMonth()->format('d/m/Y')}}';
        const __datepicker_end_date = '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}';
        const __datepicker_min_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_max_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';

    </script>
    @include('layouts.partials.js.__datatable_js')
    @vite(['resources/js/compte_show.js'])
@endpush
