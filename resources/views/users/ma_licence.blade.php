@extends('layouts.main')
@section('document-title','Ma license')

@section('page')
    <div class="row ">

    </div>
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card-title m-0 p-2 pt-0">
                <div id="__fixed"
                     class="d-flex flex-wrap flex-sm-nowrap  switch-filter justify-content-between align-items-center">
                    <h3 class="m-0 ">Ma licence
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="border p-3 rounded shadow card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <i class=" text-success fa fa-globe me-2"></i>
                        <h5 class="m-0">Domain</h5>
                    </div>
                </div>
                <div class="d-flex align-items-end">
                    <h4 style="width: 100%" class="mb-1 text-danger border-bottom border-2 border-success me-2"></h4>
                    <h4 class="m-0 text-end text-muted"
                        style="white-space: nowrap;">{{$tenant->domains->first()->domain}}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="border p-3 rounded shadow card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <i class=" text-info fa fa-ribbon me-2"></i>
                        <h5 class="m-0">Mon plan</h5>
                    </div>
                </div>
                <div class="d-flex align-items-end">
                    <h4 style="width: 100%" class="mb-1 text-danger border-bottom border-2 border-info me-2"></h4>
                    <h4 class="m-0 text-end text-muted" style="white-space: nowrap;">B2B Option Basique</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="border p-3 rounded shadow card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <i class=" text-warning fa fa-users me-2"></i>
                        <h5 class="m-0">Nombre d'utilisateurs</h5>
                    </div>
                </div>
                <div class="d-flex align-items-end">
                    <h4 style="width: 100%" class="mb-1 text-danger border-bottom border-2 border-warning me-2"></h4>
                    <h4 class="m-0 text-end text-muted" style="white-space: nowrap;">{{$users_count}}
                        /{{$max_users_count}}</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-4 col-sm-6">
            <div class="border p-3 rounded shadow card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center">
                        <i class=" text-danger fa fa-clock me-2"></i>
                        <h5 class="m-0">Date expiration</h5>
                    </div>
                </div>
                <div class="d-flex align-items-end">
                    <h4 style="width: 100%" class="mb-1 text-danger border-bottom border-2 border-danger me-2"></h4>
                    <h4 class="m-0 text-end text-muted"
                        style="white-space: nowrap;">{{\Carbon\Carbon::make($tenant->date_expiration)->format('d/m/Y H:i:s')}}</h4>
                </div>
            </div>
        </div>

        <div class="">
            <div class="custom-tab tab-profile">

                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link pb-3 pt-0 active" data-bs-toggle="tab" href="#octroi" role="tab"
                           aria-selected="true"><i class="fa fa-id-badge me-2"></i>Octroi de licence</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link pb-3 pt-0" data-bs-toggle="tab" href="#restrictions" role="tab"
                           aria-selected="false" tabindex="-1"><i class="fas fa-ban me-2"></i>Restrictions
                            d'utilisation</a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link pb-3 pt-0" data-bs-toggle="tab" href="#protection" role="tab"
                           aria-selected="false" tabindex="-1"><i class="fas fa-shield-alt me-2"></i> Protection des
                            données</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content pt-4">
                    <div class="tab-pane active show" id="octroi" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5>Octroi de licence</h5>
                                <hr>
                                <p>
                                    Nous vous accordons une licence non exclusive et non transférable pour accéder et
                                    utiliser
                                    l'application logicielle <b>GERO</b> uniquement à des fins
                                    commerciales internes. Cette licence ne vous confère aucun droit de propriété sur le
                                    Logiciel.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="restrictions" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5>Restrictions d'utilisation</h5>
                                <hr>
                                <p>
                                    Vous ne pouvez pas :
                                </p>
                                <ul>
                                    <li> Copier, modifier, rétroconcevoir, décompiler ou désassembler le Logiciel.</li>
                                    <li>
                                        Distribuer, sous-licencier, louer, céder à bail ou transférer de toute autre
                                        manière le
                                        Logiciel à un tiers.
                                    </li>
                                    <li>Utiliser le Logiciel à des fins illégales ou non autorisées.</li>
                                    <li>
                                        Tenter de désactiver ou de contourner les fonctionnalités de sécurité du
                                        Logiciel.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="protection" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5> Protection des données</h5>
                                <hr>
                                <p>
                                    Nous prenons la sécurité de vos données très au sérieux. Nous déploierons des
                                    efforts
                                    commercialement raisonnables pour protéger vos données contre tout accès,
                                    utilisation,
                                    divulgation, altération ou destruction non autorisés. Pour plus d'informations sur
                                    nos pratiques
                                    de protection des données.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
@endpush
