@php use App\Models\Vente;@endphp

@extends('layouts.main')
@section('document-title',"Détails d'affaire" )
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <style>
        .last-col {
            width: 1%;
            white-space: nowrap;
        }

    </style>
    <link rel="stylesheet" href="{{asset("libs/frappe-gantt/index.css")}}">
@endpush
@section('page')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed"
                             class="d-flex justify-content-between align-items-center flex-wrap flex-md-nowrap">
                            <div>
                                <a href="{{route('affaire.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="float-end ms-3">
                                    <i class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                    Détails d'affaire
                                </h5>
                            </div>
                            <div class="pull-right d-md-none d-block">
                                <button class="btn btn-primary actions-mobile"><i class="fa fa-bars"></i></button>
                            </div>
                            <div class="pull-right d-md-block flex-wrap gap-md-0 gap-2 actions-target-mobile">
                                <a href="{{route('affaire.modifier', $affaire->id)}}" class="btn btn-soft-warning mx-1">
                                    <i class="fa fa-edit"></i> Modifier
                                </a>

                                <button id="supprimer-btn"
                                        data-url="{{route('affaire.supprimer',['id'=> $affaire->id])}}"
                                        class="btn btn-danger mx-1">
                                    <i class="fa fa-trash-alt"></i> Supprimer
                                </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    {{--                    @if (count($o_vente->document_parent)>0)--}}
                    {{--                        <div class="col-12 px-2">--}}
                    {{--                            <div class="alert alert-info d-flex align-items-center" role="alert">--}}
                    {{--                                <i class="fa fa-info-circle me-2"></i>--}}
                    {{--                                <div class="d-flex justify-content-between w-100">--}}
                    {{--                                    <div>--}}
                    {{--                                        Ce document a été converti à partir--}}
                    {{--                                        des {{strtolower(__('ventes.'.$o_vente->document_parent->first()->type_document.'s'))}}--}}
                    {{--                                        @foreach($o_vente->document_parent as $parent)--}}
                    {{--                                            <a target="_blank" class="alert-link"--}}
                    {{--                                               href="{{route('ventes.afficher',[$parent->type_document,$parent->id])}}">{{$parent->reference}}</a> ,--}}
                    {{--                                        @endforeach--}}
                    {{--                                    </div>--}}
                    {{--                                    <button type="button" class="btn-close" data-bs-dismiss="alert"--}}
                    {{--                                            aria-label="Close"></button>--}}
                    {{--                                </div>--}}
                    {{--                            </div>--}}
                    {{--                        </div>--}}
                    {{--                    @endif--}}
                    <div class="row py-3 px-1 mx-0 my-3 rounded">
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-id-card fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Reference</span>
                                <p class="mb-0 h5 text-black">{{$affaire->reference}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-wallet fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Total des charges TTC</span>
                                <p class="mb-0 h5 text-black">{{number_format(0,2,'.',' ')}} MAD</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-dollar-sign fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Chiffre d'affaires TTC</span>
                                <p class="mb-0 h5 text-black">{{number_format($totalAv,2,'.',' ')}} MAD</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-file-invoice fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Total des dépenses TTC</span>
                                <p class="mb-0 h5 text-black">{{number_format(0,2,'.',' ')}} MAD</p>
                            </div>
                        </div>

                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-building fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Client</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$affaire->client->nom}} <span
                                        class="text-muted font-size-10">({{$affaire->client->reference}})</span></p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 47px">
                                <i class="fa fas fa-keyboard fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Titre</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$affaire->titre}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-info p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-calendar-alt fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                    <span
                                        class="font-weight-bolder font-size-sm">Date de début</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$affaire->date_debut}}</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-info p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-calendar-alt fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                    <span
                                        class="font-weight-bolder font-size-sm">Date de fin</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$affaire->date_fin}}</p>
                            </div>
                        </div>


                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-center">
                            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                                 style="width: 49px">
                                <i class="fa fa-star fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Statut</span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$affaire->statut}}

                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-md-start">
                            <div
                                class="rounded bg-success text-white  p-2 d-flex align-items-center justify-content-center"
                                style="width: 49px">
                                <i class="fa fa-coins fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">Budget estimatif </span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$affaire->budget_estimatif ?? '-'}}
                                    MAD</p>
                            </div>
                        </div>
                        <div class="col-lg-4 col-xl-3 col-sm-6 col-md-4 d-flex p-2 align-items-md-start">
                            <div
                                class="rounded bg-success text-white  p-2 d-flex align-items-center justify-content-center"
                                style="width: 49px">
                                <i class="fa fa-money-bill fa-2x"></i>
                            </div>
                            <div class="ms-3 ">
                                <span class="font-weight-bolder font-size-sm">CA global </span>
                                <p class="mb-0 h5 text-black text-capitalize">{{$affaire->ca_global ?? '-'}} MAD</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>


        </div>

    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <input type="hidden" id="affaire-input" value="{{$affaire->id}}">
                <div class="d-flex justify-content-between align-items-center">
                    <ul class="nav nav-tabs nav-tabs-custom pt-3" role="tablist">
                        @foreach($types as $key => $type)
                            <li class="nav-item">
                                <a class="nav-link ventes-tabs {{$key === 0 ? 'active' : null}}  p-3"
                                   data-type="{{$type}}"
                                   data-paiement="{{in_array($type,$payables) ? 1 : 0}}" data-bs-toggle="tab"
                                   href="#ventes-{{$type}}"
                                   role="tab">
                                    <div class="d-flex align-items-center">
                                        <i class="fa fa-chart-line text-success me-3"></i>
                                        <h5 class="m-0"> @lang('ventes.'.$type.'s')
                                            ({{$affaire->ventes()->where('type_document',$type)->count()}})</h5>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                        <li class="nav-item">
                            <a class="nav-link depense-tab  p-3"
                               data-bs-toggle="tab"
                               href="#depense-table"
                               role="tab">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-chart-line text-success me-3"></i>
                                    <h5 class="m-0"> Depenses
                                        ({{$affaire->depenses()->count()}})</h5>
                                </div>
                            </a>
                        </li>
                    </ul>
                    <button class="btn btn-soft-success" data-bs-toggle="modal" data-bs-target="#affaireModal">+
                        Attacher un document
                    </button>
                </div>
                <!-- Tab panes -->
                <div class="tab-content">
                    @foreach($types as $key  => $type)
                        <div class="tab-pane {{$key === 0 ? 'active' : null}} p-3" id="ventes-{{$type}}"
                             role="tabpanel">
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
                    @endforeach
                    <div class="tab-pane  p-3" id="depense-table"
                         role="tabpanel">
                        <table class="table table-striped table-bordered rounded overflow-hidden">
                            <thead>
                            <tr class="table-head-bg">
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
                                <th>Actions</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="gantt"></div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="affaireModal" tabindex="-1" aria-labelledby="affaireModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="affaireModalTitle">Modal title</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label" for="type-select">Type</label>
                        <select class="form-select mb-3 " name="type" id="type-select">
                            <option value="depense">Dépense</option>
                            <option value="vente">Vente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="documents-select">Document</label>
                        <select class="select2 form-control mb-3 custom-select" name="document" id="documents-select">
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" id="attach-btn" class="btn btn-primary">Attacher</button>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/frappe-gantt/dist/frappe-gantt.umd.js"></script>
    @include('layouts.partials.js.__datatable_js')
    <script>
        const __list_url = "{{route('affaire.liste')}}"
        const url_depense = "{{route('depenses.liste')}}";
        const vente_url = "{{url('ventes/')}}/"
        const __affaire_id = '{{$affaire->id}}'
        const __url_search = '{{route('affaire.attacher.recherche',$affaire->id)}}';
        const __url_attach = '{{route('affaire.attacher',$affaire->id)}}';
        const tasks = @json($gantt)
    </script>
    @vite(['resources/js/affaire_afficher.js']);

@endpush
