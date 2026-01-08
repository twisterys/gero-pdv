<div class="col-12" id="client-stats">
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
<div class="col-12" id="client-tabs-content">
    <div class="card">
        <div class="card-body">
            <input type="hidden" id="client-input" value="{{$o_client->id}}">
            <ul class="nav nav-tabs nav-tabs-custom pt-3" role="tablist">
                <li class="nav-item ">
                    <a class="nav-link active p-3" data-bs-toggle="tab" href="#ventes-impaye"
                       role="tab">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-file me-3"></i>
                            <h5 class="m-0">Ventes impayés ({{count($ventesImpaye)}})</h5>
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
                                <i class="fa fa-chart-line me-3"></i>
                                <h5 class="m-0"> @lang('ventes.'.$type.'s')
                                    ({{$o_client->ventes()->where('type_document',$type)->when(request('date_emission'), function($query) {
                                        $start = Carbon\Carbon::createFromFormat('d/m/Y', trim(explode('-', request('date_emission'))[0]))->toDateString();
                                        $end = Carbon\Carbon::createFromFormat('d/m/Y', trim(explode('-', request('date_emission'))[1]))->toDateString();
                                        return $query->whereBetween('date_emission', [$start, $end]);
                                    }, function($query) {
                                        return $query->whereRaw('Year(date_emission) = '.session()->get('exercice'));
                                    })->count()}})</h5>
                            </div>
                        </a>
                    </li>
                @endforeach
                <li class="nav-item">
                    <a class="nav-link  p-3" data-bs-toggle="tab" href="#contacts"
                       role="tab">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-users me-3"></i>
                            <h5 class="m-0">Contacts ({{$o_client->contacts()->count()}})</h5>
                        </div>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link  p-3" data-bs-toggle="tab" href="#events"
                       role="tab">
                        <div class="d-flex align-items-center">
                            <i class="fa fa-users me-3"></i>
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
                            @forelse($ventesImpaye as $vente)
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
