@extends('layouts.main')
@section('document-title', ucwords($o_client->nom))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
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
                                <a href="{{ route('clients.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                    {{ucwords($o_client->nom)}} <span class="text-muted font-size-10">({{$o_client->reference}})</span>
                                </h5>
                            </div>
                            <div class="pull-right">
                                <a href="{{route('clients.modifier',$o_client->id)}}" class="btn btn-soft-warning"><i
                                        class="fa fa-edit"></i> Modifier</a>
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
                                <img src="https://placehold.co/150x150?text={{$o_client->reference}}"
                                     class="border-0 w-100" alt="">
                            </div>
                            <h5 class="mb-0 mt-2 text-white text-center">{{$o_client->nom}} </h5>

                            <p class="text-center text-white-50  mb-0">{{$o_client->reference}}
                                -{{strtoupper($o_client->forme_juridique->nom)}}</p>
                        </div>
                        <div class=" p-3 row col-xxl-10 col-lg-9 col-md-8 col-12 align-items-start">
                            <div class="col-12 row">
                                <div class="col-xxl-3  col-sm-6 col-12  my-1 my-xxl-0  d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-file-invoice fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">@lang('ventes.bcs')</span>
                                        <p class="mb-0 h5 text-black">{{number_format($commandes,2,'.','')}} MAD</p>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-sm-6 col-12 my-1 my-xxl-0 d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-dollar-sign text-success fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">Chiffre d'affaires</span>
                                        <p class="mb-0 h5 text-black">{{number_format($ca,2,'.','')}} MAD</p>
                                    </div>
                                </div>
                                <div class="col-xxl-3  col-sm-6 col-12  my-1 my-xxl-0  d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-wallet fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">Total encaissement</span>
                                        <p class="mb-0 h5 text-black">{{number_format($encaissement,2,'.','')}} MAD</p>
                                    </div>
                                </div>
                                <div class="col-xxl-3  col-sm-6 col-12  my-1 my-xxl-0  d-flex align-items-center">
                                    <div
                                        class="rounded bg-soft-danger  p-2 d-flex align-items-center justify-content-center"
                                        style="width: 49px">
                                        <i class="fa fa-credit-card fa-2x"></i>
                                    </div>
                                    <div class="ms-3 ">
                                        <span class="font-weight-bolder font-size-sm">Crédit</span>
                                        <p class="mb-0 h5 text-black">{{number_format($credit,2,'.','')}} MAD</p>
                                    </div>
                                </div>
                                <div class="col-12 ">
                                    <hr class="border">
                                </div>
                            </div>
                            <div class="col-lg-6 row m-0">
                                <p class=" col-xxl-12 col-lg-3 col-sm-6"><i
                                        class="fa fa-envelope me-2"></i> {{$o_client->email??'-'}}</p>
                                <p class="text-capitalize  col-xxl-12 col-lg-3 col-sm-6"><i
                                        class="fa fa-phone fa-flip-horizontal me-2"></i> {{$o_client->telephone ?? '-'}}
                                </p>
                                <p class="text-capitalize col-xxl-12 col-lg-3 col-sm-6"><i
                                        class="fa fa-id-badge me-2"></i> {{$o_client->ice??'-'}}</p>
                                <p class="text-capitalize  col-xxl-12 col-lg-3 col-sm-6"><i
                                        class="fa fa-location-arrow me-2"></i> {{$o_client->adresse??'-'}}</p>
                            </div>
                            <div class="col-lg-6  m-0">
                                <p class=" "><b>Ville :</b> {{$o_client->ville??'-'}}</p>
                                <p class=" "><b>Limite de crédit :</b> {{$o_client->limite_de_credit??'-'}} MAD</p>
                                <p class="text-capitalize "><b>Note:</b> <br> {{$o_client->note??'-'}}</p>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <input type="hidden" id="client-input" value="{{$o_client->id}}">
                    <ul class="nav nav-tabs nav-tabs-custom pt-3" role="tablist">
                        <li class="nav-item ">
                            <a class="nav-link active p-3" data-bs-toggle="tab" href="#ventes-impaye"
                               role="tab">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-file text-success me-3"></i>
                                    <h5 class="m-0">Ventes impayés ({{$o_client->ventesImpaye()->count()}})</h5>
                                </div>
                            </a>
                        </li>

                        @foreach($types as $type)
                            <li class="nav-item">
                                <a class="nav-link ventes-tabs  p-3" data-type="{{$type}}"
                                   data-paiement="{{in_array($type,$payables) ? 1 : 0}}" data-bs-toggle="tab"
                                   href="#ventes-{{$type}}"
                                   role="tab">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-chart-line text-success me-3"></i>
                                        <h5 class="m-0"> @lang('ventes.'.$type.'s')
                                            ({{$o_client->ventes()->where('type_document',$type)->whereRaw('Year(date_emission) = '.session()->get('exercice'))->count()}})</h5>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                        <li class="nav-item">
                            <a class="nav-link  p-3" data-bs-toggle="tab" href="#contacts"
                               role="tab">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-users text-success me-3"></i>
                                    <h5 class="m-0">Contacts ({{$o_client->contacts()->count()}})</h5>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link  p-3" data-bs-toggle="tab" href="#events"
                               role="tab">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-users text-success me-3"></i>
                                    <h5 class="m-0">Activités ({{$o_client->events()->count()}})</h5>
                                </div>
                            </a>
                        </li>

                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane  p-3" id="contacts" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tr>
                                        <td>Nom</td>
                                        <td>Prénom</td>
                                        <td>Email</td>
                                        <td>Téléphone</td>
                                        <td style="width: 1px; white-space: nowrap">Contact principal</td>
                                    </tr>
                                    @forelse($o_client->contacts as $contact)
                                        <tr>
                                            <td>{{$contact->nom}}</td>
                                            <td>{{$contact->prenom}}</td>
                                            <td>{{$contact->email}}</td>
                                            <td>{{$contact->telephone}}</td>
                                            <td>
                                                <div
                                                    class="badge {{$contact->is_principal ? 'bg-soft-success' : 'bg-soft-delete'}}">{{$contact->is_principal ? 'Oui' : 'Non'}}</div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center" colspan="10">{{$o_client->nom}} n'a aucun contact</td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane active  p-3" id="ventes-impaye" role="tabpanel">
                           <div class="table-responsive">
                               <table class="table table-striped rounded overflow-hidden">
                                   <tr class="table-head-bg">
                                       <th>Référence</th>
                                       <th>Date d'emission</th>
                                       <th>Montant TTC</th>
                                       <th>Montant payé TTC</th>
                                       <th>Montant impayé TTC</th>
                                       <th style="width: 70px">Actions</th>
                                   </tr>
                                   @forelse($o_client->ventesImpaye as $vente)
                                       <tr>
                                           <td>{{$vente->reference}}</td>
                                           <td>{{$vente->date_emission}}</td>
                                           <td>{{$vente->total_ttc}} MAD</td>
                                           <td>{{$vente->encaisser}} MAD</td>
                                           <td>{{$vente->solde}} MAD</td>
                                           <td><a class="btn btn-sm btn-soft-primary"
                                                  data-bs-template='<div class="tooltip mb-1 rounded " role="tooltip"><div class="tooltip-inner bg-primary font-size-10"></div></div>'
                                                  data-bs-toggle="tooltip" data-bs-custom-class="bg-primary"
                                                  data-bs-placement="top" data-bs-original-title="Consulter"
                                                  href="{{route('ventes.afficher',[$vente->type_document,$vente->id])}}"><i
                                                       class="fa fa-eye"></i></a></td>
                                       </tr>
                                   @empty
                                       <tr>
                                           <td colspan="10"><p class="m-0 text-center">Aucun document</p></td>
                                       </tr>
                                   @endforelse
                               </table>
                           </div>
                        </div>
                        <div class="tab-pane  p-3" id="events" role="tabpanel">
                            <div class="text-end mb-3">
                                <button class="btn btn-soft-success" data-bs-target="#event-add-modal"
                                        data-bs-toggle="modal"> <i class="fa fa-plus"></i> Ajouter une activité
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped rounded overflow-hidden">
                                    <tr class="table-head-bg">
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Titre</th>
                                        <th>Début</th>
                                        <th>Fin</th>
                                        <th>Durée</th>
                                        <th style="width: 70px">Actions</th>
                                    </tr>
                                    @forelse($o_client->events as $event)
                                        <tr>
                                            <td>{{$event->date}}</td>
                                            <td>{{$types_event[$event->type]}}</td>
                                            <td>{{$event->titre}}</td>
                                            <td>{{$event->debut}}</td>
                                            <td>{{$event->fin}}</td>
                                            <td>{{\Carbon\Carbon::createFromFormat('H:s',$event->debut)->diffForHumans(\Carbon\Carbon::createFromFormat('H:i',$event->fin),true)}}</td>
                                            <td>
                                                <div class="d-flex">
                                                    <a data-url="{{ route('events.afficher',$event->id) }}" data-target="event-show-modal"
                                                       class="btn btn-sm btn-primary __datatable-edit-modal mx-1">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <a data-url="{{route('events.modifier',$event->id)}}" data-target="event-edit-modal"
                                                       class="btn btn-sm btn-soft-warning __datatable-edit-modal mx-1">
                                                        <i class="fa fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="10"><p class="m-0 text-center">Aucune activité</p></td>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                        @foreach($types as $type)
                            <div class="tab-pane  p-3" id="ventes-{{$type}}" role="tabpanel">
                                <style>
                                    #datatable td:last-child {
                                        width: 1%;
                                        white-space: nowrap;
                                    }
                                </style>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered rounded overflow-hidden">
                                        <thead>
                                        <tr class="table-head-bg">
                                            <th style="width: 1%;white-space: nowrap"></th>
                                            <th>Référence</th>
                                            <th>Statut</th>
                                            @if(in_array($type,$payables))
                                                <th>Statut de paiement</th>
                                            @endif
                                            <th>Date d'emission</th>
                                            <th>Montant TTC</th>
                                            @if(in_array($type,$payables))
                                                <th>Montant payé TTC</th>
                                                <th>Montant impayé TTC</th>
                                            @endif
                                            <th>Convertir de</th>
                                            <th>Actions</th>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="event-add-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center">Ajouter une activité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="event-add" action="{{route('events.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="client_id" value="{{$o_client->id}}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="titre-add-input">Titre</label>
                                <input type="text" required class="form-control" id="titre-add-input" name="titre">
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="type-add-input">Type</label>
                                <select name="type" id="type-add-input">
                                    @foreach($types_event as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="date-add-input">Date</label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="date-add-input" name="date">
                                    <span class="input-group-text">
                                        <span class="fa fa-calendar-alt"></span>
                                    </span>
                                    <div class="invalid-feedback"></div>

                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="time-add-input">Heure</label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="debut-add-input" name="debut" data-inputmask="'alias': 'datetime'"
                                           placeholder="Début">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="fin-add-input" name="fin" data-inputmask="'alias': 'datetime'"
                                           placeholder="Fin">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="description-add-input">Description</label>
                                <textarea name="description" class="form-control" id="description-add-input" cols="30" rows="5"></textarea>
                                <div class="invalid-feedback"></div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="event-edit-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="event-show-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <input type="hidden" id="#client-select" value="{{$o_client->id}}">
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script>
        const __dataTable_filter_inputs_id = {
            client_id: '#client-input',
        }
        const __dataTable_filter = function (data) {
            d = __datatable_ajax_callback(data);
        };
        $(document).on('click', '.ventes-tabs', function () {
            let nav_tab = $(this);
            $(nav_tab.attr('href') + ' table').DataTable().destroy();

            let url = "{{url('ventes/')}}/" + nav_tab.data('type') + '/liste';
            let __dataTable_columns = [
                {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
                {data: 'reference', name: 'reference'},
                {data: 'statut', name: 'statut'},
                {data: 'date_emission', name: 'date_emission'},
                {data: 'total_ttc', name: 'total_ttc'},
                {data: 'convertir_de', name: 'convertir_de', orderable: false,},
                {
                    data: 'actions', name: 'actions', orderable: false,
                },
            ];
            if (nav_tab.data('paiement')) {
                __dataTable_columns = [
                    {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
                    {data: 'reference', name: 'reference'},
                    {data: 'statut', name: 'statut'},
                    {
                        data: 'statut_paiement', name: 'statut_paiement'
                    },
                    {data: 'date_emission', name: 'date_emission'},
                    {data: 'total_ttc', name: 'total_ttc'},
                    {data: 'encaisser', name: 'encaisser'},
                    {data: 'solde', name: 'solde'},
                    {data: 'convertir_de', name: 'convertir_de', orderable: false,},
                    {
                        data: 'actions', name: 'actions', orderable: false,
                    },
                ];
            }
            $(nav_tab.attr('href') + ' table').dataTable(
                {
                    dom: 'lBrtip',
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    language: {
                        "emptyTable": "Aucune donnée disponible dans le tableau",
                        "loadingRecords": "Chargement...",
                        "processing": "Traitement...",
                        "select": {
                            "rows": {
                                "_": "%d lignes sélectionnées",
                                "1": "1 ligne sélectionnée"
                            },
                            "cells": {
                                "1": "1 cellule sélectionnée",
                                "_": "%d cellules sélectionnées"
                            },
                            "columns": {
                                "1": "1 colonne sélectionnée",
                                "_": "%d colonnes sélectionnées"
                            }
                        },
                        "autoFill": {
                            "cancel": "Annuler",
                            "fill": "Remplir toutes les cellules avec <i>%d<\/i>",
                            "fillHorizontal": "Remplir les cellules horizontalement",
                            "fillVertical": "Remplir les cellules verticalement"
                        },
                        "searchBuilder": {
                            "conditions": {
                                "date": {
                                    "after": "Après le",
                                    "before": "Avant le",
                                    "between": "Entre",
                                    "empty": "Vide",
                                    "not": "Différent de",
                                    "notBetween": "Pas entre",
                                    "notEmpty": "Non vide",
                                    "equals": "Égal à"
                                },
                                "number": {
                                    "between": "Entre",
                                    "empty": "Vide",
                                    "gt": "Supérieur à",
                                    "gte": "Supérieur ou égal à",
                                    "lt": "Inférieur à",
                                    "lte": "Inférieur ou égal à",
                                    "not": "Différent de",
                                    "notBetween": "Pas entre",
                                    "notEmpty": "Non vide",
                                    "equals": "Égal à"
                                },
                                "string": {
                                    "contains": "Contient",
                                    "empty": "Vide",
                                    "endsWith": "Se termine par",
                                    "not": "Différent de",
                                    "notEmpty": "Non vide",
                                    "startsWith": "Commence par",
                                    "equals": "Égal à",
                                    "notContains": "Ne contient pas",
                                    "notEndsWith": "Ne termine pas par",
                                    "notStartsWith": "Ne commence pas par"
                                },
                                "array": {
                                    "empty": "Vide",
                                    "contains": "Contient",
                                    "not": "Différent de",
                                    "notEmpty": "Non vide",
                                    "without": "Sans",
                                    "equals": "Égal à"
                                }
                            },
                            "add": "Ajouter une condition",
                            "button": {
                                "0": "Recherche avancée",
                                "_": "Recherche avancée (%d)"
                            },
                            "clearAll": "Effacer tout",
                            "condition": "Condition",
                            "data": "Donnée",
                            "deleteTitle": "Supprimer la règle de filtrage",
                            "logicAnd": "Et",
                            "logicOr": "Ou",
                            "title": {
                                "0": "Recherche avancée",
                                "_": "Recherche avancée (%d)"
                            },
                            "value": "Valeur",
                            "leftTitle": "Désindenter le critère",
                            "rightTitle": "Indenter le critère"
                        },
                        "searchPanes": {
                            "clearMessage": "Effacer tout",
                            "count": "{total}",
                            "title": "Filtres actifs - %d",
                            "collapse": {
                                "0": "Volet de recherche",
                                "_": "Volet de recherche (%d)"
                            },
                            "countFiltered": "{shown} ({total})",
                            "emptyPanes": "Pas de volet de recherche",
                            "loadMessage": "Chargement du volet de recherche...",
                            "collapseMessage": "Réduire tout",
                            "showMessage": "Montrer tout"
                        },
                        "buttons": {
                            "collection": "Collection",
                            "colvis": "Visibilité colonnes",
                            "colvisRestore": "Rétablir visibilité",
                            "copy": "Copier",
                            "copySuccess": {
                                "1": "1 ligne copiée dans le presse-papier",
                                "_": "%d lignes copiées dans le presse-papier"
                            },
                            "copyTitle": "Copier dans le presse-papier",
                            "csv": "CSV",
                            "excel": "Excel",
                            "pageLength": {
                                "-1": "Afficher toutes les lignes",
                                "_": "Afficher %d lignes",
                                "1": "Afficher 1 ligne"
                            },
                            "pdf": "PDF",
                            "print": "Imprimer",
                            "copyKeys": "Appuyez sur ctrl ou u2318 + C pour copier les données du tableau dans votre presse-papier.",
                            "createState": "Créer un état",
                            "removeAllStates": "Supprimer tous les états",
                            "removeState": "Supprimer",
                            "renameState": "Renommer",
                            "savedStates": "États sauvegardés",
                            "stateRestore": "État %d",
                            "updateState": "Mettre à jour"
                        },
                        "decimal": ",",
                        "datetime": {
                            "previous": "Précédent",
                            "next": "Suivant",
                            "hours": "Heures",
                            "minutes": "Minutes",
                            "seconds": "Secondes",
                            "unknown": "-",
                            "amPm": [
                                "am",
                                "pm"
                            ],
                            "months": {
                                "0": "Janvier",
                                "2": "Mars",
                                "3": "Avril",
                                "4": "Mai",
                                "5": "Juin",
                                "6": "Juillet",
                                "8": "Septembre",
                                "9": "Octobre",
                                "10": "Novembre",
                                "1": "Février",
                                "11": "Décembre",
                                "7": "Août"
                            },
                            "weekdays": [
                                "Dim",
                                "Lun",
                                "Mar",
                                "Mer",
                                "Jeu",
                                "Ven",
                                "Sam"
                            ]
                        },
                        "editor": {
                            "close": "Fermer",
                            "create": {
                                "title": "Créer une nouvelle entrée",
                                "button": "Nouveau",
                                "submit": "Créer"
                            },
                            "edit": {
                                "button": "Editer",
                                "title": "Editer Entrée",
                                "submit": "Mettre à jour"
                            },
                            "remove": {
                                "button": "Supprimer",
                                "title": "Supprimer",
                                "submit": "Supprimer",
                                "confirm": {
                                    "_": "Êtes-vous sûr de vouloir supprimer %d lignes ?",
                                    "1": "Êtes-vous sûr de vouloir supprimer 1 ligne ?"
                                }
                            },
                            "multi": {
                                "title": "Valeurs multiples",
                                "info": "Les éléments sélectionnés contiennent différentes valeurs pour cette entrée. Pour modifier et définir tous les éléments de cette entrée à la même valeur, cliquez ou tapez ici, sinon ils conserveront leurs valeurs individuelles.",
                                "restore": "Annuler les modifications",
                                "noMulti": "Ce champ peut être modifié individuellement, mais ne fait pas partie d'un groupe. "
                            },
                            "error": {
                                "system": "Une erreur système s'est produite (<a target=\"\\\" rel=\"nofollow\" href=\"\\\">Plus d'information<\/a>)."
                            }
                        },
                        "stateRestore": {
                            "removeSubmit": "Supprimer",
                            "creationModal": {
                                "button": "Créer",
                                "order": "Tri",
                                "paging": "Pagination",
                                "scroller": "Position du défilement",
                                "search": "Recherche",
                                "select": "Sélection",
                                "columns": {
                                    "search": "Recherche par colonne",
                                    "visible": "Visibilité des colonnes"
                                },
                                "name": "Nom :",
                                "searchBuilder": "Recherche avancée",
                                "title": "Créer un nouvel état",
                                "toggleLabel": "Inclus :"
                            },
                            "renameButton": "Renommer",
                            "duplicateError": "Il existe déjà un état avec ce nom.",
                            "emptyError": "Le nom ne peut pas être vide.",
                            "emptyStates": "Aucun état sauvegardé",
                            "removeConfirm": "Voulez vous vraiment supprimer %s ?",
                            "removeError": "Échec de la suppression de l'état.",
                            "removeJoiner": "et",
                            "removeTitle": "Supprimer l'état",
                            "renameLabel": "Nouveau nom pour %s :",
                            "renameTitle": "Renommer l'état"
                        },
                        "info": "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
                        "infoEmpty": "Affichage de 0 à 0 sur 0 entrées",
                        "infoFiltered": "(filtrées depuis un total de _MAX_ entrées)",
                        "lengthMenu": "Afficher _MENU_ entrées",
                        "paginate": {
                            "first": "Première",
                            "last": "Dernière",
                            "next": "Suivante",
                            "previous": "Précédente"
                        },
                        "zeroRecords": "Aucune entrée correspondante trouvée",
                        "aria": {
                            "sortAscending": " : activer pour trier la colonne par ordre croissant",
                            "sortDescending": " : activer pour trier la colonne par ordre décroissant"
                        },
                        "infoThousands": " ",
                        "search": "Rechercher :",
                        "thousands": " "
                    },
                    buttons: [
                        {extend: 'copy', className: 'btn-soft-primary'},
                        {extend: 'excel', className: 'btn-soft-primary'},
                        {extend: 'pdf', className: 'btn-soft-primary'},
                        {extend: 'colvis', className: 'btn-soft-primary'}
                    ],
                    columnDefs: [
                        {
                            className: 'last-col',
                            targets: -1,
                        }
                    ],
                    ajax: {
                        url: url,
                        data: function (d) {
                            if (typeof __dataTable_filter_inputs_id === 'object') {
                                for (const key in __dataTable_filter_inputs_id) {
                                    d[key] = $(__dataTable_filter_inputs_id[key]).val();
                                }
                            }
                            d = __datatable_ajax_callback(d)
                        }
                    },
                    columns: __dataTable_columns,
                    orderCellsTop: true,
                    order: [[1, 'desc']],
                    pageLength: 10,
                }
            )
        })
    </script>
    <script>
        $('#date-add-input').datepicker({
            format: 'dd/mm/yyyy'
        })
        $('#debut-add-input,#fin-add-input').inputmask({
            inputFormat: "HH:MM"
        })
        $('#type-add-input').select2({
            width:'100%',
            minimumResultsForSearch:-1
        })
        $(document).on('submit','#event-add,#event-edit',function (e){
            e.preventDefault();
            let btn =  $(this).find('button').attr('disabled',"true")
            let form = $(this)
            form.find('.is-invalid').removeClass('is-invalid')
            $.ajax({
                url:form.attr('action'),
                method:'post',
                data:form.serialize(),
                success:function (response){
                    form.trigger('reset');
                    btn.removeAttr('disabled');
                    toastr.success(response);
                    $('#event-add-modal,#event-edit-modal').modal('hide');
                    location.reload()
                },
                error: function (xhr){
                    btn.removeAttr('disabled')

                    if(xhr.status === 422){
                        let errors = xhr.responseJSON.errors;
                        for (const [key,value] of Object.entries(errors)){
                            form.find('[name="'+key+'"]').addClass('is-invalid')
                            form.find('[name="'+key+'"]').siblings('.invalid-feedback').html(value)
                        }

                    }else{
                        toastr.error(xhr.responseText);
                    }
                }
            })
        })
    </script>
@endpush
