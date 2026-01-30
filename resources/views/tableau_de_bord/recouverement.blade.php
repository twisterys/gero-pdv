@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title','Tableau de bord')
@push('styles')
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{asset('libs/chartist/chartist.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/tippy.js/tippy.css')}}">
    <style>
        .__dashboard_item {
            transform-origin: 50% 10%;
            position: relative;
        }

        .__dashboard_item.__dashboard_item_hide {
            visibility: hidden;
            position: absolute !important;
        }

        .__dashboard_item.__dashboard_item_hide > div {
            opacity: 50% !important;
        }

        .__dashboard_item.__customize_state, .__dashboard_sortable_item.__sortable_state {
            overflow: visible !important;
            opacity: 90%;
        }

        .__dashboard_item-eye, .__dashboard_item-sort-cursor {
            position: absolute !important;
            top: 0;
            right: 0;
            transform: translateY(-50%) translateX(50%);
            width: 25px !important;
            height: 25px !important;
            background-color: var(--bs-primary);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--bs-box-shadow);
            border-radius: 50%;
        }

        .__dashboard_item.__customize_state.__dashboard_item_hide {
            position: static !important;
            visibility: visible !important;
        }

        .__dashboard_item.__customize_state > div, .__dashboard_sortable_item.__sortable_state > div {
            border: 1px dashed var(--bs-primary) !important;
            overflow: visible !important;
            box-sizing: border-box;
            position: relative;

        }


    </style>
@endpush
@section('page')
    <div class="row ">
        <div class="col-12 mb-4">
            <div class="card-title m-0 p-2 pt-0">
                <div id="__fixed"
                     class="d-flex flex-wrap flex-sm-nowrap  switch-filter justify-content-between align-items-center">
                    <h5 class="m-0 ">Tableau de bord
                        <button class="btn btn-sm  btn-soft-primary btn-rounded view-toggle ratio-1x1 ms-2"
                                style="height: 30px"><i class="fa fa-eye "></i></button>
                        <button class="btn btn-sm  btn-soft-primary btn-rounded sort-toggle ratio-1x1 ms-2"
                                style="height: 30px"><i class="fa fa-th"></i></button>
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <div class="row __dashboard_container">
        <!-- Chiffres d'affaires -->
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <a href="{{route('ventes.liste',['type'=>'fa','f'=>'brouillon'])}}">
                <div class="card overflow-hidden">
                    <div class="card-body bg-soft-success  overflow-hidden position-relative  rounded">
                        <div class="d-flex flex-nowrap align-items-center">
                            <i class="fa fa-file-contract text-success fa-3x"></i>
                            <div class="ms-4 ">
                                <h4 class="text-muted dashboard-text ">
                                    {{number_format($fa_a_valider,0,'.',' ')}}
                                </h4>
                                <h6 class="m-0 text-muted  ">Facture à valider</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <a href="{{route('ventes.liste',['type'=>'fa','f'=>'echeance'])}}">

            <div class="card overflow-hidden">
                    <div class="card-body bg-soft-info  overflow-hidden  rounded">
                        <div class="d-flex flex-nowrap align-items-center">
                            <i class="fa fa-clock text-info fa-3x"></i>
                            <div class="ms-4">
                                <h4 class="text-muted dashboard-text">
                                    {{number_format($fa_echeance,0,'.',' ')}}
                                </h4>
                                <h6 class="m-0 text-muted  ">Factures en écheance</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <a href="{{route('ventes.liste',['type'=>'fa','f'=>'non_paye'])}}">

                <div class="card overflow-hidden">
                    <div class="card-body bg-soft-warning  overflow-hidden  rounded">
                        <div class="d-flex flex-nowrap align-items-center">
                            <i class="fa fa-money-bill text-warning fa-3x"></i>
                            <div class="ms-4">

                                <h4 class="text-muted dashboard-text">
                                    {{number_format($fa_non_paye,0,'.',' ')}}
                                </h4>
                                <h6 class="m-0 text-muted  ">Factures non payés</h6>

                            </div>
                        </div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <a href="{{route('ventes.liste',['type'=>'fa','f'=>'promesses_a_traiter'])}}">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-danger  overflow-hidden  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-business-time text-danger fa-3x"></i>
                        <div class="ms-4">
                            <h4 class="text-muted dashboard-text">
                                {{number_format($promesses_a_traiter,0,'.',' ')}}
                            </h4>
                            <h6 class="m-0 text-muted  ">Promesses à traiter</h6>
                        </div>
                    </div>
                </div>
            </div>
            </a>
        </div>
        <div class=" col-12 col-md-6  __dashboard_sortable_item d-flex" >
            <div class="card col-12" >
                <div class="card-body d-flex flex-column h-100">
                    <div class="card-title">
                        <h5 class="text-black-50">Factures de vente en échéance </h5>
                    </div>
                    <div class="card mb-0 overview shadow-none pt-3 overflow-y-scroll " style="max-height: 320px">
                        <table class="table table-bordered table-striped" style="border-collapse: collapse !important;">
                            <thead>
                            <tr>
                                <th>Facture</th>
                                <th>Client</th>
                                <th>Jours de retard</th>
                                <th>Total</th>
                                <th>Montant a payé</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($factures_echeance as $facture)
                                <tr>
                                    <td><a href="{{route('ventes.afficher',[$facture->type_document,$facture->id])}}" class="link-underline-info text-info" >{{$facture->reference}}</a></td>
                                    <td><a class="link-underline-info text-info" href="{{route('clients.afficher',$facture->client_id)}}" target="_blank">{{$facture->client->nom}}</a></td>
                                    <td>{{$facture->jours_de_retard}}</td>
                                    <td class="dashboard-text" >{{number_format($facture->total_ttc,3,'.',' ')}} MAD</td>
                                    <td class="dashboard-text">{{number_format($facture->solde,3,'.',' ')}} MAD</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="23" class="text-center">Aucun facture</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class=" col-12 col-md-6  __dashboard_sortable_item d-flex" >
            <div class="card col-12" >
                <div class="card-body d-flex flex-column h-100">
                    <div class="card-title">
                        <h5 class="text-black-50">Factures d'achat en échéance </h5>
                    </div>
                    <div class="card mb-0 overview shadow-none pt-3 overflow-y-scroll " style="max-height: 320px">
                        <table class="table table-bordered table-striped" style="border-collapse: collapse !important;">
                            <thead>
                            <tr>
                                <th>Facture</th>
                                <th>Fournisseur</th>
                                <th>Jours de retard</th>
                                <th>Total</th>
                                <th>Montant a payé</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($factures_achats_echeance as $facture_achat)
                                <tr>
                                    <td><a href="{{route('achats.afficher',[$facture_achat->type_document,$facture_achat->id])}}" class="link-underline-info text-info" >{{$facture_achat->reference}}</a></td>
                                    <td><a class="link-underline-info text-info" href="{{route('fournisseurs.afficher',$facture_achat->fournisseur_id)}}" target="_blank">{{$facture_achat->fournisseur->nom}}</a></td>
                                    <td>{{$facture_achat->jours_de_retard}}</td>
                                    <td class="dashboard-text" >{{number_format($facture_achat->total_ttc,3,'.',' ')}} MAD</td>
                                    <td class="dashboard-text">{{number_format($facture_achat->debit,3,'.',' ')}} MAD</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="23" class="text-center">Aucune facture</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class=" col-12 col-md-6  __dashboard_sortable_item d-flex" >
            <div class="card col-12" >
                <div class="card-body d-flex flex-column h-100">
                    <div class="card-title">
                        <h5 class="text-black-50">Devis en attente </h5>
                    </div>
                    <div class="card mb-0 overview shadow-none pt-3 overflow-y-scroll " style="max-height: 320px">
                        <table class="table table-bordered table-striped" style="border-collapse: collapse !important;">
                            <thead>
                            <tr>
                                <th>Devis</th>
                                <th>Client</th>
                                <th>Jours de retard</th>
                                <th>Montant total</th>
                            </thead>
                            <tbody>
                            @forelse($devis_ventes_echeance as $devis_vente)
                                <tr>
                                    <td><a href="{{route('ventes.afficher',[$devis_vente->type_document,$devis_vente->id])}}" class="link-underline-info text-info" >{{$devis_vente->reference ?? 'Brouillon'}}</a></td>
                                    <td><a class="link-underline-info text-info" href="{{route('clients.afficher',$devis_vente->client_id)}}" target="_blank">{{$devis_vente->client->nom}}</a></td>
                                    <td>{{$devis_vente->jours_de_retard}}</td>
                                    <td class="dashboard-text">{{$devis_vente->total_ttc}} MAD</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="23" class="text-center">Aucun devis</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class=" col-12 col-md-6  __dashboard_sortable_item d-flex" >
            <div class="card col-12" >
                <div class="card-body d-flex flex-column h-100">
                    <div class="card-title">
                        <h5 class="text-black-50">Promesses à traiter </h5>
                    </div>
                    <div class="card mb-0 overview shadow-none pt-3 overflow-y-scroll " style="max-height: 320px">
                        <table class="table table-bordered table-striped" style="border-collapse: collapse !important;">
                            <thead>
                            <tr>
                                <th>Vente</th>
                                <th>Client</th>
                                <th>Date prévu</th>
                                <th>Type</th>
                                <th>Montant</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($promesses_a_traiter_table as $promesse)
                                <tr>
                                   <td><a target="_blank" class="link-underline-info text-info" href="{{route('ventes.afficher',['type'=>$promesse->vente->type_document,'id'=>$promesse->vente_id])}}">{{$promesse->vente->reference}}</a></td>
                                    <td><a target="_blank" class="link-underline-info text-info" href="{{route('clients.afficher',$promesse->vente->client_id)}}">{{$promesse->vente->client->nom}}</a></td>
                                    <td>{{\Carbon\Carbon::make($promesse->date)->format('d/m/Y')}}</td>

                                    <td>{{$promesse->type}}</td>
                                    <td>{{$promesse->montant}}</td>
                                    <td>
                                        @if($promesse->statut === null)
                                            <button type="button"
                                                    data-url="{{route('promesse.respecter',$promesse->id)}}"
                                                    class="btn btn-soft-success btn-sm __promesse_respecter-btn" data-bs-toggle="tooltip" data-bs-title="Réspécter">
                                                <i
                                                    class="fa fa-check"></i></button>
                                            <button type="button" data-url="{{route('promesse.rompre',$promesse->id)}}"
                                                    class="btn btn-soft-warning btn-sm __promesse_rompre-btn" data-bs-toggle="tooltip" data-bs-title="Rompre">
                                                <i
                                                    class="fa fa-times"></i></button>

                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="23" class="text-center">Aucun promesse</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection
@push('scripts')
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script>

        @php
            $exercice = session()->get('exercice')
        @endphp
         const __datepicker_dates = {
            "Hier":['{{Carbon::yesterday()->format('d/m/Y')}}','{{Carbon::yesterday()->format('d/m/Y')}}'],
            "Aujourd'hui": ['{{Carbon::today()->format('d/m/Y')}}', '{{Carbon::today()->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            "Trimestre 1": ['{{Carbon::create($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->create($exercice,3)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2': ['{{Carbon::create($exercice,4)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::create($exercice,6)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3': ['{{Carbon::create($exercice,7)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::create($exercice,9)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4': ['{{Carbon::now()->create($exercice,10)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::create($exercice,12)->lastOfMonth()->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Cette exercice': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{$date_picker_start}}';
        const __datepicker_end_date = '{{$date_picker_end}}';
        const __datepicker_min_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
        const __datepicker_max_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';
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
        $('#i_date').change(function () {
            $(this).closest('form').submit()
        })
    </script>
    <script>
        var customize_mode = false;
        var dashboard_positions = {};


        if (localStorage.getItem('dashboard_positions')) {
            dashboard_positions = JSON.parse(localStorage.getItem('dashboard_positions'));
            $('.__dashboard_sortable_item').each(function (index, item) {
                $(item).attr('data-position', index);
                $(item).insertBefore(`.__dashboard_sortable_item:nth-child(${dashboard_positions[index] + 1})`)
            });
        } else {
            $('.__dashboard_sortable_item').each(function (index, item) {
                dashboard_positions[index] = index;
                $(item).attr('data-position', index);
            });
        }

        $('.view-toggle').on('click', function () {
            if (!customize_mode) {
                customize_mode = !customize_mode;
                $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                $('.dashboard-text').each(function (e) {
                    let text = $(this).html()
                    $(this).data('value', text);
                    $(this).html(text.replace(/\d/g, '*'))

                })
            } else {
                $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye')
                $('.dashboard-text').each(function (e) {
                    $(this).html($(this).data('value'))
                })
                customize_mode = !customize_mode;
            }
        })
        $('.__dashboard_container').sortable({
            cursor: "move",
            placeholder: "sortable-placeholder",
            handle: ".__dashboard_item-sort-cursor"
        })

        var sortable_mode = false;
        $('.sort-toggle').on('click', function () {
            if (!sortable_mode) {
                sortable_mode = !sortable_mode;
                $(this).find('i').removeClass('fa-th').addClass('fa-check');
                $('.__dashboard_sortable_item').addClass('__sortable_state');
                $('.__dashboard_sortable_item > div').each(function (e) {
                    $(this).append('<div class="__dashboard_item-sort-cursor" ><i class=" fa fa-grip-lines" ></i><div>');
                })
            } else {
                $(this).find('i').removeClass('fa-check').addClass('fa-th');
                sortable_mode = !sortable_mode;
                $('.__dashboard_sortable_item .__dashboard_item-sort-cursor').remove();
                $('.__dashboard_sortable_item').each(function (index, item) {
                    $(item).removeClass('__sortable_state');
                    dashboard_positions[$(item).data('position')] = index;
                })
                localStorage.setItem('dashboard_positions', JSON.stringify(dashboard_positions));
            }
        })
        $('.dashboard-text').each(function (e) {
            let text = $(this).html()
            $(this).data('value', text);
            // $(this).html(text.replace(/\d/g, '*'))
        })
        $('.ca-trigger').click(function () {
            $('.ca').toggleClass('d-none')
        })
    </script>
@endpush
