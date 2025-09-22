@extends('layouts.main')
@section('document-title',__('ventes.'.$type.'.show.title'))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
@endpush
@section('page')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div  class="d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap">
                            <div>
                                <a href="{{route('ventes.liste',$type)}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3" style="margin-top: .1rem!important"><i
                                        class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                    @lang('ventes.'.$type.'.show.title') <span
                                        class="text-muted opacity-75 font-size-12 text-nowrap text-truncate">{{$o_vente->objet? '('.$o_vente->objet.')': null}}</span>
                                </h5>
                            </div>
                            <div class="pull-right d-md-none d-block">
                                <button class="btn btn-primary actions-mobile" ><i class="fa fa-bars"></i></button>
                            </div>
                            <div class="pull-right d-md-block flex-wrap gap-md-0 gap-2 actions-target-mobile">
                                @can('vente.controler')
                                @if($is_controled && $o_vente->is_controled)
                                    <button class="btn btn-soft-dark mx-1" disabled>
                                        <i class="fa fa-check-circle"></i> Contrôlée
                                    </button>
                                @elseif($is_controled)
                                    <button data-href="{{ route('ventes.controle', [$type, $o_vente->id]) }}"
                                            id="controle-btn" class="btn btn-soft-success mx-1">
                                        <i class="fa fa-check-circle"></i> Contrôler
                                    </button>
                                @endif
                                @endcan

                                @can('vente.mettre_a_jour')
                                @if($o_vente->statut === 'brouillon' )
                                <a @if($o_vente->statut !== 'validé')href="{{route('ventes.modifier',[$type,$o_vente->id])}}"
                                   @endif class="btn btn-soft-warning mx-1 @if($o_vente->statut === 'validé') disabled @endif "><i
                                        class="fa fa-edit"></i> Modifier </a>
                                @endif

                                @endcan

                                @if($o_vente->statut === 'brouillon')
                                    @can('vente.valider')
                                    <button data-href="{{route('ventes.validation_modal',[$type,$o_vente->id])}}"
                                            id="validation-btn" class="btn btn-soft-success mx-1"><i
                                            class="fa fa-check"></i> Valider
                                    </button>
                                    @endcan
                                @endif
                                @if($o_vente->statut === 'validé')
                                    @can('vente.devalider')
                                    <button data-href="{{route('ventes.devalidation_modal',[$type,$o_vente->id])}}"
                                            id="devalidation-btn" class="btn btn-soft-secondary mx-1"><i
                                            class="fa fa-times"></i> Dévalider
                                    </button>
                                    @endcan
                                @endif
                                    @can('vente.telecharger')
                                <a target="__blank" href="{{route('ventes.telecharger',[$type,$o_vente->id])}}"
                                   class="btn btn-soft-danger mx-1"><i class="fa fa-file-pdf"></i> Télécharger </a>
                                    @endcan
                                    @if($o_vente->statut === 'validé')
                                        @can('vente.convertir')
                                        <button id="conversion-btn"
                                                data-href="{{route('ventes.conversion_modal',[$type,$o_vente->id])}}"
                                                class="btn btn-soft-purple mx-1"><i class="fa fa-sync-alt"></i> Convertir
                                        </button>
                                        @endcan
                                    @endif

                                @if($o_vente->type_document === 'dv')
                                    <button id="statut-com-btn"
                                            data-href="{{route('ventes.statut_com_modal',[$type,$o_vente->id])}}"
                                            class="btn btn-soft-warning mx-1">

                                        <i class="fa fa-exchange-alt"></i> Changer Statut
                                    </button>
                                @endif

                                @if(in_array($type,$payabale_types) && $o_vente->statut_paiement !== 'paye')
                                    @can('promesse.sauvegarder')
                                    <button id="promesse-btn"
                                            @if($o_vente->solde != "0") data-href="{{route('ventes.promesse_modal',[$type,$o_vente->id])}}"
                                            @else disabled @endif class="btn btn-soft-secondary mx-1">
                                        <i class="fa fa-piggy-bank"></i>
                                        Promesse
                                    </button>
                                    @endcan
                                    @can('paiement.vente')
                                    <button id="paiement-btn"
                                            @if($o_vente->solde != '0' )data-href="{{route('ventes.paiement_modal',[$type,$o_vente->id])}}"
                                            @else disabled @endif class="btn btn-soft-info mx-1"><i
                                            class="fa fa-cash-register"></i> Payer
                                    </button>
                                        @endcan

                                @endif

                                @can('vente.cloner')
                                <button id="clone-btn"
                                        data-href="{{route('ventes.clone_modal',[$type,$o_vente->id])}}"
                                        class="btn btn-soft-primary mx-1"><i class="fa fa-clone"></i> Cloner
                                </button>
                                @endcan
{{--                                <form method="post" action="{{route('ventes.cloner',[$type,$o_vente->id])}}"--}}
{{--                                      class="d-inline">--}}
{{--                                    @csrf--}}
{{--                                    <button type="button" class="btn clone-btn btn-soft-primary mx-1"><i--}}
{{--                                            class="fa fa-clone"></i> Cloner--}}
{{--                                    </button>--}}
{{--                                </form>--}}

                                <div class="dropdown d-inline-block">
                                    @can('vente.historique')
                                    <button data-href="{{ route('ventes.history_modal', [$type, $o_vente->id]) }}"
                                            id="history-btn"
                                            class="btn right-bar-toggle waves-effect btn-soft-info mx-1"
                                            type="button"
                                            data-bs-toggle="offcanvas"
                                            data-bs-target="#offcanvasRight"
                                            aria-controls="offcanvasRight"
                                            onclick="loadData(this)">
                                        <i class="fas fa-history"></i> Historique
                                    </button>
                                    @endcan
                                </div>
                                @if($o_vente->type_document ==='av')
                                    <button class="btn btn-warning" data-bs-target="#solde-modal"
                                            @if(count($o_vente->vente_solde) >0) disabled @endif data-bs-toggle="modal">
                                        <i class="fa fa-money-bill"></i> Solder une facture
                                    </button>
                                @endif
                                @can('vente.supprimer')
                                @if($o_vente->statut !== 'validé')
                                    <button id="supprimer-btn"
                                            data-url="{{route('ventes.supprimer',[$type,$o_vente->id])}}"
                                            class="btn btn-danger mx-1"><i
                                            class="fa fa-trash-alt"></i> Supprimer
                                    </button>
                                @endif
                                @endcan

                                @can('vente.relancer')
                                @if(in_array($type, ['dv', 'fa', 'fp'])
                                            && $o_vente->solde > 0
                                            && $o_vente->statut === "validé"
                                            && $o_vente->date_expiration <= \Carbon\Carbon::today()->format('d/m/Y'))
                                    <button id="relancer-btn"
                                        data-href="{{route('ventes.relancer_modal',[$type,$o_vente->id])}}"
                                        class="btn btn-soft-purple mx-1"><i
                                        class="fa fa-paper-plane"></i> Relancer
                                    </button>
                                @endif
                                @endcan
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @if (count($o_vente->document_parent)>0)
                        <div class="col-12 px-2">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fa fa-info-circle me-2"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <div>
                                        Ce document a été converti à partir
                                        des {{strtolower(__('ventes.'.$o_vente->document_parent->first()->type_document.'s'))}}
                                        @foreach($o_vente->document_parent as $parent)
                                            <a target="_blank" class="alert-link"
                                               href="{{route('ventes.afficher',[$parent->type_document,$parent->id])}}">{{$parent->reference}}</a>
                                            ,
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(count($o_vente->solde_par)>0)
                        <div class="col-12 px-2">
                            <div class="alert alert-info d-flex align-items-center" role="alert">
                                <i class="fa fa-info-circle me-2"></i>
                                <div class="d-flex justify-content-between w-100">
                                    <div>
                                        Ce document a été soldé à partir
                                        de {{strtolower(__('ventes.'.$o_vente->solde_par->first()->type_document.'s'))}}
                                        @foreach($o_vente->solde_par as $parent)
                                            <a target="_blank" class="alert-link"
                                               href="{{route('ventes.afficher',[$parent->type_document,$parent->id])}}">{{$parent->reference}}</a>
                                            ,
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                            aria-label="Close"></button>
                                </div>
                            </div>
                        </div>
                    @endif
                    <button class="btn btn-success d-block d-md-none w-100 show-more" ><i class="fa fa-plus"></i> plus</button>
                    <div class="d-md-flex flex-wrap d-none py-3 px-1 mx-0 my-3 rounded more-target">
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 my-2  col-12  d-flex align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-id-card fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Reference</span>
                                <p class="mb-0 h5 text-black">{{$o_vente->reference??'-'}}</p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  col-12  my-2 d-flex align-items-center">
                            <div class="rounded bg-soft-primary  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-store fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Magasin</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->magasin->nom}} </p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4  col-12  my-2 d-flex align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-building fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Client</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->client->nom}} <span
                                        class="text-muted font-size-10">({{$o_vente->client->reference}})</span></p>
                            </div>
                        </div>
                        @if(in_array($type,['dv','fa','fp','bc']))
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2  d-flex align-items-center">
                                <div class="rounded bg-soft-danger p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-calendar-alt fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span
                                        class="font-weight-bolder font-size-sm">@lang('ventes.'.$type.'.date_emission')</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->date_emission}}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-center">
                                <div class="rounded bg-soft-warning p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-calendar-alt fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span
                                        class="font-weight-bolder font-size-sm">@lang('ventes.'.$type.'.date_expiration')</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->date_expiration}}</p>
                                </div>
                            </div>
                        @else
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2  d-flex align-items-center">
                                <div class="rounded bg-soft-danger p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-calendar-alt fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span
                                        class="font-weight-bolder font-size-sm">@lang('ventes.'.$type.'.date_emission')</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->date_emission}}</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-center">
                                <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-star fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Statut</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->statut}} @if($o_vente->piece_jointe)
                                            <a class="text-warning mx-1"
                                               href="{{route('ventes.piece_jointe',[$type,$o_vente->id])}}"> <i
                                                    class="fa fa-paperclip"></i></a>
                                        @endif</p>

                                </div>
                            </div>
                        @endif



                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-md-start">
                            <div
                                class="rounded bg-info text-white  p-2 d-flex align-items-center justify-content-center"
                                style="width: 49px">
                                <i class="fa fa-coins fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Montant TTC </span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->total_ttc}} MAD</p>
                            </div>
                        </div>
                        @if(in_array($type,$payabale_types))
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-md-start">
                                <div
                                    class="rounded bg-success text-white p-2 d-flex align-items-center justify-content-center"
                                    style="width: 49px">
                                    <i class="fa fa-cash-register fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Paiement</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->encaisser}} MAD</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-md-start">
                                <div
                                    class="rounded bg-danger text-white  p-2 d-flex align-items-center justify-content-center"
                                    style="width: 49px">
                                    <i class="fa fa-money-bill fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Montant restant</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->solde}} MAD</p>
                                </div>
                            </div>
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-md-start">
                                <div
                                    class="rounded bg-warning text-white  p-2 d-flex align-items-center justify-content-center"
                                    style="width: 49px">
                                    <i class="fa fa-file-invoice-dollar fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Statut de paiement</span>
                                    <p class="mb-0 h5 text-black text-capitalize">@lang('ventes.'.$o_vente->statut_paiement)</p>
                                </div>
                            </div>
                        @endif

                        @if($type === 'dv')
                            <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 col-12  my-2 d-flex align-items-center">
                                <div class="rounded bg-soft-secondary  p-2 d-flex align-items-center justify-content-center"
                                     style="width: 49px">
                                    <i class="fa fa-star fa-2x"></i>
                                </div>
                                <div class="ms-3 ">
                                    <span class="font-weight-bolder font-size-sm">Statut commercial</span>
                                    <p class="mb-0 h5 text-black text-capitalize">{{$o_vente->statut_com ?? 'Créé'}}

                                </div>
                            </div>
                        @endif
                    </div>
                    <ul class="nav nav-tabs nav-tabs-custom pt-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active p-3" data-bs-toggle="tab" href="#articles"
                               role="tab">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-boxes  me-3"></i>
                                    <h5 class="m-0">Articles</h5>
                                </div>
                            </a>
                        </li>

                    @if(in_array($type,$payabale_types))
                            <li class="nav-item">
                                <a class="nav-link p-3" data-bs-toggle="tab" href="#paiements"
                                   role="tab">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-cash-register  me-3"></i>
                                        <h5 class="m-0">Paiements ({{$o_vente->paiements()->count()}})</h5>
                                    </div>
                                </a>
                            </li>
                            @can('promesse.liste')
                            <li class="nav-item">
                                <a class="nav-link p-3" data-bs-toggle="tab" href="#promesses"
                                   role="tab">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-file-contract  me-3"></i>
                                        <h5 class="m-0">Promesses ({{$o_vente->promesses()->count()}})</h5>
                                    </div>
                                </a>
                            </li>
                            @endcan
                        @endif
                        @if($globals->pieces_jointes)
                            <li class="nav-item">
                                <a class="nav-link  p-3" data-bs-toggle="tab" href="#pieces_jointes"
                                   role="tab">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-paperclip  me-3"></i>
                                        <h5 class="m-0">Pièces jointes ({{$o_vente->piecesJointes()->count()}})
                                        </h5>
                                    </div>
                                </a>
                            </li>
                        @endif
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active p-3" id="articles" role="tabpanel">
                            <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped"
                                       id="table">
                                    <thead>
                                    <tr class="bg-primary text-white ">
                                        <th class="text-white " style="width: 1%; white-space: nowrap">Référence</th>
                                        <th class="text-white ">Article</th>
                                        <th class="text-white px-4" style="width: 1%;white-space: nowrap;">Quantité</th>
                                        <th class="text-white px-4  @if(!$prix_revient) d-none @endif"
                                            style="width: 1%;white-space: nowrap;">Revient (MAD)
                                        </th>
                                        <th class="text-white px-4" style="width: 1%;white-space: nowrap;">HT (MAD)</th>
                                        <th class="text-white px-4" style="width: 1%;white-space: nowrap;">TVA (%)</th>
                                        <th class="text-white px-4" style="width: 1%;white-space: nowrap;"> Réduction
                                            HT
                                        </th>
                                        <th class="text-white px-4"
                                            style="width: 1%;white-space: nowrap;min-width: 130px">
                                            Total TTC (MAD)
                                        </th>
                                        @if($prix_revient)
                                            <th class="text-white px-4"
                                                style="width: 1%;white-space: nowrap;min-width: 130px">
                                                Marge profit (MAD)
                                            </th>
                                        @endif
                                    </tr>
                                    </thead>
                                    <!-- The tbody tag will be populated by JavaScript -->
                                    <tbody id="productTableBody">
                                    @forelse($o_vente->lignes->sortby('position') as $ligne)
                                        <tr>
                                            <td>
                                                {{$ligne->article ? $ligne->article->reference :'---'}}

                                            </td>
                                            <td>
                                                {{$ligne->nom_article}}
                                                <p class="m-0">{!!  $ligne->description !!}</p>
                                            </td>
                                            <td class="text-end"
                                                style="white-space: nowrap">{{$ligne->quantite}} {{$ligne->unite?->nom}}</td>
                                            <td class="text-end  @if(!$prix_revient) d-none @endif"
                                                style="white-space: nowrap">{{$ligne->revient ?? "0.00"}} MAD
                                            </td>
                                            <td class="text-end"
                                                style="white-space: nowrap">{{number_format($ligne->ht,2,'.',' ')}} MAD
                                            </td>
                                            <td class="text-end" style="white-space: nowrap">{{$ligne->taxe}} %</td>
                                            <td class="text-end"
                                                style="white-space: nowrap">{{$ligne->reduction}} {{$ligne->mode_reduction ==='fixe' ? 'MAD' : '%'}}</td>
                                            <td class="text-end" style="white-space: nowrap">{{$ligne->total_ttc}}MAD
                                            </td>
                                            @if($prix_revient)
                                                <td class="text-end"
                                                    style="white-space: nowrap">{{$ligne->ht == 0 ? 0 :  ($ligne->ht - $ligne->revient) * $ligne->quantite}}
                                                    MAD <span class="font-size-12 text-success mx-1">{{$ligne->ht == 0 ? 0 : round(($ligne->ht - $ligne->revient)*100/$ligne->ht,2)}}%</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @empty
                                    @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-12 mt-3 row mx-0 justify-content-between ">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 row mx-0 bg-primary p-3 rounded text-white" style="max-width: 500px">
                                    <h5 class="col-4 fw-normal">Total HT</h5>
                                    <h5 class="col-8 text-end fw-normal"
                                        id="total-ht-text">{{$o_vente->total_ht}} MAD</h5>
                                    <h5 class="col-4 fw-normal">Total Réduction</h5>
                                    <h5 class="col-8 text-end fw-normal"
                                        id="total-reduction-text">{{$o_vente->total_reduction}}
                                        MAD</h5>
                                    <h5 class="col-4 fw-normal">Total TVA</h5>
                                    <h5 class="col-8 text-end fw-normal" id="total-tva-text">{{$o_vente->total_tva}}
                                        MAD</h5>
                                    <h5 class="col-4 mb-0 fw-normal">Total TTC</h5>
                                    <h2 class="col-8 mb-0 text-end" id="total-ttc-text">{{$o_vente->total_ttc}} MAD</h2>
                                </div>
                            </div>
                            @if($o_vente->note)
                                <div class="col-12 pt-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-pen text-primary me-3"></i>
                                        <h5 class="m-0">Note</h5>
                                    </div>
                                    <hr class="border">
                                    <div class="col-12 rounded p-2" style="background-color: var(--bs-gray-100)">
                                        {!! $o_vente->note !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if(in_array($type,$payabale_types))
                            <div class="tab-pane p-3" id="paiements" role="tabpanel">
                                <div class="col-12 pt-2">
                                   <div class="table-responsive">
                                       <table class="table table-striped table-bordered">
                                           <tr>
                                               <th>Date</th>
                                               <th>Montant</th>
                                               <th>Compte</th>
                                               <th>Méthode de paiement</th>
                                               <th>Référence de chéque</th>
                                               <th>Date prévu</th>
                                               <th>Note</th>
                                               <th>Action</th>
                                           </tr>
                                           @forelse($o_vente->paiements as $paiement)
                                               <tr>
                                                   <td>{{$paiement->date_paiement}}</td>
                                                   <td>{{$paiement->encaisser == 0 ? $paiement->decaisser : $paiement->encaisser }}
                                                       MAD
                                                   </td>
                                                   <td>{{$paiement->compte->nom}}</td>
                                                   <td>{{$paiement->methodePaiement->nom}}</td>
                                                   <td>{{$paiement->cheque_lcn_reference}}</td>
                                                   <td>{{$paiement->cheque_lcn_date}}</td>
                                                   <td>{{$paiement->note}}</td>
                                                   <td style="width: 1%;white-space: nowrap" >
                                                       <a data-url="{{ route('paiement.afficher',$paiement->id) }}"
                                                          data-target="paiement-modal"
                                                          class="btn btn-sm btn-primary __datatable-edit-modal">
                                                           <i class="fa fa-eye"></i>
                                                       </a>
                                                       <a data-url="{{ route('paiement.modifier',$paiement->id) }}"
                                                          data-target="paiement-modal"
                                                          class="btn btn-sm btn-soft-warning __datatable-edit-modal">
                                                           <i class="fa fa-edit"></i>
                                                       </a>
                                                       <form method="post"
                                                             action="{{route('paiement.supprimer',$paiement->id)}}"
                                                             class="d-inline">
                                                           @method('delete')
                                                           @csrf
                                                           <button type="button"
                                                                   class="btn btn__delete_paiement btn-soft-danger btn-sm">
                                                               <i
                                                                   class="fa fa-trash"></i></button>
                                                       </form>
                                                   </td>
                                               </tr>
                                           @empty
                                               <tr>
                                                   <td colspan="333">
                                                       <p class="text-center m-0 p-0">Aucun paiement</p>
                                                   </td>
                                               </tr>
                                           @endforelse
                                       </table>
                                   </div>
                                </div>
                            </div>
                            @can('promesse.liste')
                            <div class="tab-pane p-3" id="promesses" role="tabpanel">
                                <div class="col-12 pt-2">
                                    <hr class="border">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <tr>
                                                <th>Date</th>
                                                <th>Type</th>
                                                <th>Montant</th>
                                                <th>Date prévu</th>
                                                <th>Statut</th>
                                                <th>Action</th>
                                            </tr>
                                            @forelse($o_vente->promesses as $promesse)
                                                <tr>
                                                    <td>{{$promesse->created_at->format('d/m/Y')}}</td>
                                                    <td>{{$promesse->type}}</td>
                                                    <td>{{$promesse->montant}}</td>
                                                    <td>{{\Carbon\Carbon::make($promesse->date)->format('d/m/Y')}}</td>
                                                    <td>@lang('ventes.promesses.statut.'.($promesse->statut ?? 'null'))</td>
                                                    <td style="width: 1%;white-space: nowrap" >

                                                        @if($promesse->statut === null)
                                                            @can('promesse.respecter')
                                                            <button type="button"
                                                                    data-url="{{route('promesse.respecter',$promesse->id)}}"
                                                                    class="btn btn-soft-success btn-sm __promesse_respecter-btn" data-bs-toggle="tooltip" data-bs-title="Réspécter">
                                                                <i
                                                                    class="fa fa-check"></i></button>
                                                            @endcan
                                                        @can('promesse.rompre')
                                                            <button type="button" data-url="{{route('promesse.rompre',$promesse->id)}}"
                                                                    class="btn btn-soft-warning btn-sm __promesse_rompre-btn" data-bs-toggle="tooltip" data-bs-title="Rompre">
                                                                <i
                                                                    class="fa fa-times"></i></button>

                                                            @endcan
                                                        @endif
                                                        @can('promesse.supprimer')
                                                        <form method="post"
                                                              action="{{route('promesse.supprimer',$promesse->id)}}"
                                                              class="d-inline">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="button"
                                                                    class="btn btn__delete_promesse btn-soft-danger btn-sm">
                                                                <i
                                                                    class="fa fa-trash"></i></button>
                                                        </form>
                                                            @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="333">
                                                        <p class="text-center m-0 p-0">Il n'y a aucune promesse</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endcan
                        @endif
                        @if($globals->pieces_jointes)
                            <div class="tab-pane p-3" id="pieces_jointes" role="tabpanel">
                                <div class="col-12 pt-2">
                                    <div class="d-flex justify-content-end mb-2">
                                        @can('vente.piece_jointe_attacher')
                                        <button class="btn btn-soft-success" data-bs-toggle="modal" data-bs-target="#pieceJointeModal">+
                                            Attacher une pièce jointe
                                        </button>
                                            @endcan
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered">
                                            <tr>
                                                <th style="width: 10%; white-space: nowrap">Titre</th>
                                                <th>Lien</th>
                                                <th style="width: 1%; white-space: nowrap">Action</th>
                                            </tr>
                                            @forelse($o_vente->piecesJointes()->orderBy('created_at', 'desc')->get() as $piece)
                                                <tr>
                                                    <td>{{$piece->title}}</td>
                                                    <td>{{$piece->url}}</td>

                                                    <td style="width: 1%;white-space: nowrap" >
                                                        <a href="{{ $piece->url }}" target="_blank"
                                                           class="btn btn-sm btn-primary">
                                                            <i class="fa fa-eye"></i>
                                                        </a>

                                                        @can('vente.piece_jointe_supprimer')
                                                        <form method="post"
                                                              action="{{route('ventes.supprimer.piece_jointe',[$type,$piece->id])}}"
                                                              class="d-inline">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="button"
                                                                    class="btn btn__delete_piece_jointe btn-soft-danger btn-sm">
                                                                <i
                                                                    class="fa fa-trash"></i></button>
                                                        </form>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="333">
                                                        <p class="text-center m-0 p-0">Aucune pièce jointe</p>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="pieceJointeModal" tabindex="-1" aria-labelledby="pieceJointeModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="affaireModalTitle">Attacher une pièce jointe</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                        <div class="form-group">
                            <label class="form-label required" for="title-input">Titre</label>
                            <input type="text" class="form-control mb-3" name="title" id="title-input" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label required" for="url-input">Lien</label>
                            <input type="url" class="form-control mb-3" name="url" id="url-input" required >
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" id="attach-piece-jointe-btn" class="btn btn-primary">Attacher</button>
                </div>
            </div>
        </div>
    </div>
    @if(in_array($type,$payabale_types))
        <div class="modal fade" id="paiement-modal" tabindex="-1" aria-labelledby="paiement-modal-title"
             aria-hidden="true"
             style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                </div>
            </div>
        </div>
        <div class="modal fade" id="promesse-modal" tabindex="-1" aria-labelledby="paiement-modal-title"
             aria-hidden="true"
             style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                </div>
            </div>
        </div>
    @endif
    @if($o_vente->statut === 'brouillon')
        <div class="modal fade" id="validation-modal" tabindex="-1" aria-labelledby="validation-modal-title"
             aria-hidden="true"
             style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                </div>
            </div>
        </div>
    @endif
    @if($o_vente->statut === 'validé')
        <div class="modal fade" id="devalidation-modal" tabindex="-1" aria-labelledby="devalidation-modal-title"
             aria-hidden="true"
             style="display: none;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                </div>
            </div>
        </div>
    @endif
    <div class="modal fade" id="conversion-modal" tabindex="-1" aria-labelledby="conversion-modal-title"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="statut-com-modal" tabindex="-1" aria-labelledby="statut-com-modal-title"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="clone-modal" tabindex="-1" aria-labelledby="clonage-modal-title"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

            </div>
        </div>
    </div>

    <div class="modal fade" id="solde-modal" tabindex="-1" aria-labelledby="conversion-modal-title"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Solder un document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" class="needs-validation" enctype="multipart/form-data"
                      action="{{route('ventes.solde',[$o_vente->type_document,$o_vente->id])}}" novalidate>
                    @csrf
                    <div class="modal-body">
                        <label for="solde-select" class="form-label mt-2">Document</label>
                        <select name="vente_id" id="solde-select"></select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Solder</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{--    history--}}


    <div style="width: 500px;" class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight"
         aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Historique de {{ $o_vente->reference ?? '-' }}</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="offcanvasContent">

            </div>

        </div>
    </div>

    @if(in_array($type,['dv', 'fa', 'fp']))
        <div class="modal fade" id="relancer-modal" tabindex="-1" aria-labelledby="relancer-modal-title"
             aria-hidden="true"
             style="display: none;">
            <div class="modal-dialog  modal-dialog-centered">
                <div class="modal-content">

                </div>
            </div>
        </div>
    @endif

    @if(in_array($type,['dv', 'fa', 'fp']))
        <div class="modal fade" id="edit-relancer-modal" tabindex="-1" aria-labelledby="edit-relancer-modal-title"
             aria-hidden="true"
             style="display: none;">
            <div class="modal-dialog modal-xl  modal-dialog-centered">
                <div class="modal-content">

                </div>
            </div>
        </div>
    @endif


@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        const __url_attach = "{{route('ventes.attacher.piece_jointe',[$type,$o_vente->id])}}";
    </script>

    <script>
        @if($o_vente->type_document == 'av')
        function formatState(state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span>' + state.text + '</span><span class="text-white-50 ms-3">' + state.total_ttc + ' DH</span>'
            );
            return $state;
        }
        $('#solde-select').select2({
            width: '100%',
            dropdownParent: $('#solde-modal'),
            ajax: {
                url: '{{route('ventes.solde_select',$o_vente->type_document)}}',
                dataType: "json",
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                        id: '{{$o_vente->id}}'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
                cache: false,
            },
            templateResult: formatState,
            minimumInputLength: 2,
        })
        @endif
        var validation_process = 0;

        function loadData(button) {
            if (validation_process === 0) {
                validation_process = 1;
                var url = $(button).data('href');
                var btn = $(button); // Correctly reference the button

                btn.find('>i').addClass('d-none');
                let spinner = $(__spinner_element);
                btn.attr('disabled', true).prepend(spinner); // Disable the button and prepend the spinner

                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function (data) {
                        // Assuming `data` contains the HTML content to be injected
                        $('#offcanvasContent').html(data);
                        btn.find('>i').removeClass('d-none');
                        btn.removeAttr('disabled');
                        validation_process = 0;
                        spinner.remove();
                    },
                    error: function (xhr, status, error) {
                        btn.find('>i').removeClass('d-none');
                        btn.removeAttr('disabled');
                        validation_process = 0;
                        spinner.remove();
                        if (xhr.status != undefined) {
                            if (xhr.status === 403) {
                                toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                return
                            }
                        }
                        toastr.error(xhr.responseText);
                    }
                });
            }
        }
    </script>

    @if($o_vente->statut === 'brouillon')
        <script>
            var validation_process = 0;
            $('#validation-btn').click(function (e) {
                if (validation_process === 0) {
                    validation_process = 1;
                    $(this).find('>i').addClass('d-none');
                    let spinner = $(__spinner_element);
                    let btn = $(this);
                    $(this).attr('disabled', '').prepend(spinner);
                    $.ajax({
                        url: $(this).data('href'),
                        success: function (response) {
                            $('#validation-modal').find('.modal-content').html(response);
                            $('#validation-modal').modal("show");
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            validation_process = 0;
                            spinner.remove();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            validation_process = 0;
                            spinner.remove();
                            if (xhr.status != undefined) {
                                if (xhr.status === 403) {
                                    toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                    return
                                }
                            }
                            toastr.error('Une erreur est produite');
                        }
                    })
                }
            });
        </script>
    @endif
    @if($o_vente->statut === 'validé')
        <script>
            var validation_process = 0;
            $('#devalidation-btn').click(function (e) {
                if (validation_process === 0) {
                    validation_process = 1;
                    $(this).find('>i').addClass('d-none');
                    let spinner = $(__spinner_element);
                    let btn = $(this);
                    $(this).attr('disabled', '').prepend(spinner);
                    $.ajax({
                        url: $(this).data('href'),
                        success: function (response) {
                            $('#devalidation-modal').find('.modal-content').html(response);
                            $('#devalidation-modal').modal("show");
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            validation_process = 0;
                            spinner.remove();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            validation_process = 0;
                            spinner.remove();
                            if (xhr.status != undefined) {
                                if (xhr.status === 403) {
                                    toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                    return
                                }
                            }
                            toastr.error(xhr.responseText);
                        }
                    })
                }
            });
        </script>
    @endif
    @if(in_array($type,$payabale_types))
        <script>
            var paiement_process = 0;
            $('#paiement-btn').click(function (e) {
                if (paiement_process === 0) {
                    paiement_process = 1;
                    $(this).find('>i').addClass('d-none');
                    let spinner = $(__spinner_element);
                    let btn = $(this);
                    $(this).attr('disabled', '').prepend(spinner);
                    $.ajax({
                        url: $(this).data('href'),
                        success: function (response) {
                            $('#paiement-modal').find('.modal-content').html(response);
                            $('#paiement-modal').modal("show");
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            paiement_process = 0;
                            spinner.remove();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            paiement_process = 0;
                            spinner.remove();
                            if (xhr.status === 403) {
                                if (xhr.status != undefined) {
                                    if (xhr.status === 403) {
                                        toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                        return
                                    }
                                }
                            }
                            toastr.error(xhr.responseText);

                        }
                    })
                }
            });
            $('#promesse-btn').click(function (e) {
                if (paiement_process === 0) {
                    paiement_process = 1;
                    $(this).find('>i').addClass('d-none');
                    let spinner = $(__spinner_element);
                    let btn = $(this);
                    $(this).attr('disabled', '').prepend(spinner);
                    $.ajax({
                        url: $(this).data('href'),
                        success: function (response) {
                            $('#promesse-modal').find('.modal-content').html(response);
                            $('#promesse-modal').modal("show");
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            paiement_process = 0;
                            spinner.remove();
                        },
                        error: function (xhr, ajaxOptions, thrownError) {
                            btn.find('>i').removeClass('d-none');
                            btn.removeAttr('disabled');
                            paiement_process = 0;
                            spinner.remove();
                            if (xhr.status === 403) {
                                if (xhr.status != undefined) {
                                    if (xhr.status === 403) {
                                        toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                        return
                                    }
                                }
                            }
                            toastr.error(xhr.responseText);

                        }
                    })
                }
            });
        </script>
    @endif


    <script>
        $(document).on('click', '#supprimer-btn', function () {
            Swal.fire({
                title: "Est-vous sûr?",
                text: "Vous ne pourrez pas revenir en arrière !",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer!",
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-soft-danger mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
                },
                didOpen: () => {
                    $('.btn').blur()
                },
                preConfirm: async () => {
                    Swal.showLoading();
                    try {
                        const [response] = await Promise.all([new Promise((resolve, reject) => {
                            $.ajax({
                                url: $(this).data('url'), method: 'DELETE', headers: {
                                    'X-CSRF-TOKEN': __csrf_token
                                }, success: resolve, error: (_, jqXHR) => reject(_)
                            });
                        })]);

                        return response;
                    } catch (jqXHR) {
                        let errorMessage = "Une erreur s'est produite lors de la demande.";
                        if (jqXHR.status === 404) {
                            errorMessage = "La ressource n'a pas été trouvée.";
                        }
                        if (jqXHR.status === 403) {
                            errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                        }
                        Swal.fire({
                            title: 'Erreur',
                            text: errorMessage,
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-danger mx-2',
                            },
                        });
                        throw jqXHR;
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (result.value) {
                        Swal.fire({
                            title: 'Succès',
                            text: result.value,
                            icon: 'success',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-success mx-2',
                            },
                        }).then(result => {
                            if (result.isConfirmed) {
                                window.location.href = "{{route('ventes.liste', $type)}}";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: 'Erreur',
                            text: "Une erreur s'est produite lors de la demande.",
                            icon: 'error',
                            buttonsStyling: false,
                            confirmButtonText: 'OK',
                            customClass: {
                                confirmButton: 'btn btn-soft-danger mx-2',
                            },
                        });
                    }
                }
            })
        });
    </script>
    <script>
        var conversion_modal_process = !1;
        $('#statut-com-btn').on('click', function () {
            if (!conversion_modal_process) {
                conversion_modal_process = !0;
                let spinner = $(__spinner_element);
                $(this).prepend(spinner).find('i').addClass('d-none');
                $.ajax({
                    url: $(this).data('href'),
                    method: 'GET',
                    success: function (response) {
                        conversion_modal_process = !1;
                        spinner.parent().find('.d-none').removeClass('d-none')
                        spinner.remove();
                        $('#statut-com-modal .modal-content').html(response);
                        $('#statut-com-modal').modal('show')
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        conversion_modal_process = !1;
                        spinner.parent().find('.d-none').removeClass('d-none')
                        spinner.remove();
                        if (xhr.status != undefined) {
                            if (xhr.status === 403) {
                                toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                return
                            }
                        }
                        toastr.error(xhr.responseText);
                    }
                })
            }
        });
        $('#conversion-btn').on('click', function () {
            if (!conversion_modal_process) {
                conversion_modal_process = !0;
                let spinner = $(__spinner_element);
                $(this).prepend(spinner).find('i').addClass('d-none');
                $.ajax({
                    url: $(this).data('href'),
                    method: 'GET',
                    success: function (response) {
                        conversion_modal_process = !1;
                        spinner.parent().find('.d-none').removeClass('d-none')
                        spinner.remove();
                        $('#conversion-modal .modal-content').html(response);
                        $('#conversion-modal').modal('show')
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        conversion_modal_process = !1;
                        spinner.parent().find('.d-none').removeClass('d-none')
                        spinner.remove();
                        if (xhr.status != undefined) {
                            if (xhr.status === 403) {
                                toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                return
                            }
                        }
                        toastr.error(xhr.responseText);
                    }
                })
            }
        });
        $('#clone-btn').on('click', function () {
            if (!conversion_modal_process) {
                conversion_modal_process = !0;
                let spinner = $(__spinner_element);
                $(this).prepend(spinner).find('i').addClass('d-none');
                $.ajax({
                    url: $(this).data('href'),
                    method: 'GET',
                    success: function (response) {
                        conversion_modal_process = !1;
                        spinner.parent().find('.d-none').removeClass('d-none')
                        spinner.remove();
                        $('#clone-modal .modal-content').html(response);
                        $('#clone-modal').modal('show')
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        conversion_modal_process = !1;
                        spinner.parent().find('.d-none').removeClass('d-none')
                        spinner.remove();
                        if (xhr.status != undefined) {
                            if (xhr.status === 403) {
                                toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                return
                            }
                        }
                        toastr.error(xhr.responseText);
                    }
                })
            }
        });

        $(document).on('click', '#controle-btn', function () {
            let url = $(this).data('href');
            let btn = $(this);

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                beforeSend: function () {
                    btn.prop('disabled', true);
                },
                success: function (response) {
                    if (response.success) {
                        toastr.success(response.message, "Succès");
                        // Remplacer le bouton par celui déjà contrôlé
                        btn.replaceWith(`
                    <button class="btn btn-soft-dark mx-1" disabled>
                        <i class="fa fa-check-circle"></i> Contrôlée
                    </button>
                `);
                    } else {
                        toastr.error("Une erreur est survenue", "Erreur");
                        btn.prop('disabled', false);
                    }
                },
                error: function () {
                    toastr.error("Impossible de contrôler la vente", "Erreur");
                    btn.prop('disabled', false);
                },
                complete: function () {
                    btn.prop('disabled', false);
                }
            });
        });


        // $(document).on('click', '.clone-btn', function (e) {
        //     e.preventDefault()
        //     let form = $(this).closest('form');
        //     Swal.fire({
        //         title: "Est-vous sûr?",
        //         text: "voulez-vous cloner ce document ?",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Oui, cloner!",
        //         buttonsStyling: false,
        //         customClass: {
        //             confirmButton: 'btn btn-primary mx-2',
        //             cancelButton: 'btn btn-light mx-2',
        //         },
        //         didOpen: () => {
        //             $('.btn').blur()
        //         },
        //         preConfirm: async () => {
        //             form.submit()
        //         }
        //     })
        // });
        $(document).on('click', '.btn__delete_paiement', function (e) {
            e.preventDefault()
            let form = $(this).closest('form');
            Swal.fire({
                title: "Est-vous sûr?",
                text: "voulez-vous supprimer ce paiement ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer!",
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-light mx-2',
                },
                didOpen: () => {
                    $('.btn').blur()
                },
                preConfirm: async () => {
                    form.submit()
                }
            })
        });
        $(document).on('click', '.btn__delete_promesse', function (e) {
            e.preventDefault()
            let form = $(this).closest('form');
            Swal.fire({
                title: "Est-vous sûr?",
                text: "voulez-vous supprimer cette promesse ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer!",
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-light mx-2',
                },
                didOpen: () => {
                    $('.btn').blur()
                },
                preConfirm: async () => {
                    form.submit()
                }
            })
        });
    </script>
    <script>
        $(document).on('change', '#method-input', function () {
            let value = $(this).val();
            if (value == 'cheque' || value == 'lcn') {
                $('.__variable').removeClass('d-none').find('input').attr('required', '')
            } else {
                $('.__variable').addClass('d-none').find('input').removeAttr('required')
            }
        })

        function checkModal() {
            let methods = ['cheque', 'lcn'];
            if (methods.indexOf($('#method-input').find('option:selected').val()) !== -1) {
                $('.__variable').removeClass('d-none').find('input').attr('required', '')
            } else {
                $('.__variable').addClass('d-none').find('input').removeAttr('required')
            }
        }

        $('#paiement-modal').on('show.bs.modal', function () {
            checkModal()
        })
        var submit_paiement = !1;
        $(document).on('submit', '#paiement_form_edit', function (e) {
            e.preventDefault();
            if (!submit_paiement) {
                let spinner = $(__spinner_element);
                let btn = $('#paiement_form_edit').find('.btn-info');
                btn.attr('disabled', '').prepend(spinner)
                submit_paiement = !0;
                $.ajax({
                    url: $('#paiement_form_edit').attr('action'),
                    method: 'PUT',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-Token': __csrf_token
                    },
                    success: function (response) {
                        btn.removeAttr('disabled');
                        submit_paiement = 0;
                        spinner.remove();
                        toastr.success(response);
                        location.reload()
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        btn.removeAttr('disabled');
                        submit_paiement = !1;
                        spinner.remove();
                        toastr.error(xhr.responseText);
                    }
                })
            }


        })
    </script>

    <script>

        $('#pieceJointeModal').on('show.bs.modal', function () {
            clearModalErrors();
        });
        function clearModalErrors() {
            $('#title-input').removeClass('is-invalid');
            $('#url-input').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        $(document).on('click', "#attach-piece-jointe-btn", function () {
            // Clear previous error messages
            $('#title-input').removeClass('is-invalid');
            $('#url-input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Préparation des données pour la requête AJAX
            let title = $('#title-input').val();  // Titre de la pièce jointe
            let url = $('#url-input').val();  // URL de la pièce jointe

            // Préparation des données à envoyer avec la requête
            let data = {
                title: title,
                url: url
            };

            // Envoi de la requête AJAX pour attacher le document
            $.ajax({
                url: __url_attach,  // URL du serveur pour attacher le document
                method: 'POST',
                data: data,
                headers:{
                    'X-CSRF-TOKEN' : __csrf_token
                },
                success: function(response) {
                    // Si la réponse est "Document attché", afficher un message de succès
                    if (response === 'Pièce jointe attaché') {
                        $('#pieceJointeModal').modal('hide'); // Fermer la fenêtre modale en cas de succès
                        localStorage.setItem('activeTab', '#pieces_jointes');
                        location.reload();

                    } else {
                        // Si la réponse n'est pas celle attendue, afficher l'erreur
                        toastr.error('Erreur: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    // Gérer les erreurs lors de la requête AJAX
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.title) {
                            $('#title-input').addClass('is-invalid');
                            $('#title-input').after('<div class="invalid-feedback">' + errors.title[0] + '</div>');
                        }
                        if (errors.url) {
                            $('#url-input').addClass('is-invalid');
                            $('#url-input').after('<div class="invalid-feedback">' + errors.url[0] + '</div>');
                        }
                    } else {
                        toastr.error('Une erreur est survenue : ' + error);
                    }
                }
            });
        });

        $(document).on('click', '.btn__delete_piece_jointe', function (e) {
            e.preventDefault()
            let form = $(this).closest('form');
            localStorage.setItem('activeTab', '#pieces_jointes');
            Swal.fire({
                title: "Est-vous sûr?",
                text: "voulez-vous supprimer cette promesse ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Oui, supprimer!",
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'btn btn-danger mx-2',
                    cancelButton: 'btn btn-light mx-2',
                },
                didOpen: () => {
                    $('.btn').blur()
                },
                preConfirm: async () => {
                    form.submit()
                }
            })
        });
        $(document).ready(function() {
            // Check if the flag is set in local storage
            let activeTab = localStorage.getItem('activeTab');
            if (activeTab) {
                // Activate the tab
                $('.nav-tabs a[href="' + activeTab + '"]').tab('show');
                // Clear the flag
                localStorage.removeItem('activeTab');
            }
        });
    </script>
    <script>
        var relancer_process = 0;
        $('#relancer-btn').click(function (e) {
            if (relancer_process === 0) {
                relancer_process = 1;
                $(this).find('>i').addClass('d-none');
                let spinner = $(__spinner_element);
                let btn = $(this);
                $(this).attr('disabled', '').prepend(spinner);
                $.ajax({
                    url: $(this).data('href'),
                    success: function (response) {
                        $('#relancer-modal').find('.modal-content').html(response);
                        $('#relancer-modal').modal("show");

                        btn.find('>i').removeClass('d-none');
                        btn.removeAttr('disabled');
                        relancer_process = 0;
                        spinner.remove();
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        btn.find('>i').removeClass('d-none');
                        btn.removeAttr('disabled');
                        relancer_process = 0;
                        spinner.remove();
                        if (xhr.status != undefined) {
                            if (xhr.status === 403) {
                                toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                                return;
                            }
                        }
                        toastr.error(xhr.responseText);
                    }
                });

            }
        });
    </script>



@endpush
