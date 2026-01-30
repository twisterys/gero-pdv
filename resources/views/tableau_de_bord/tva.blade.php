@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title','Tableau de bord')
@push('styles')
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{asset('libs/chartist/chartist.min.css')}}" rel="stylesheet">
    <link href="{{asset('libs/morris.js/morris.css')}}" rel="stylesheet">
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
                    <div class="page-title-right col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                        <form action="{{route('tableau_bord.liste')}}" method="get">
                            <div class="input-group  border-1 border border-light rounded" id="datepicker1">
                                <input type="text" class="form-control datepicker text-primary ps-2 "
                                       id="i_date"
                                       placeholder="mm/dd/yyyy"
                                       name="i_date" readonly>
                                <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row __dashboard_container">
        <!-- Chiffres d'affaires -->
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-success  overflow-hidden position-relative  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-dollar-sign text-success fa-3x"></i>
                        <div class="ms-4 ca">
                            <h4 class="text-muted dashboard-text ">
                                {{number_format($ca_vente,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">Total des ventes TTC</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-info  overflow-hidden  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-shopping-cart text-info fa-3x"></i>
                        <div class="ms-4">
                            <h4 class="text-muted dashboard-text">
                                {{number_format($ca_achat,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">Total Achats TTC</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-warning  overflow-hidden  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-cash-register text-warning fa-3x"></i>
                        <div class="ms-4">
                            <h4 class="text-muted dashboard-text">
                                {{number_format($encaissements,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">Total Encaissement TTC</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-danger  overflow-hidden  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-coins text-danger fa-3x"></i>
                        <div class="ms-4">
                            <h4 class="text-muted dashboard-text">
                                {{number_format($decessements,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">Total Décaissement</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-success  overflow-hidden position-relative  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-money-bill text-success fa-3x"></i>
                        <div class="ms-4 ">
                            <h4 class="text-muted dashboard-text ">
                                {{number_format($creance_client,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">Créance Clients</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-info  overflow-hidden  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-wallet text-info fa-3x"></i>
                        <div class="ms-4">
                            <h4 class="text-muted dashboard-text">
                                {{number_format($creance_fournisseur,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">Créance Fournisseurs</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-warning  overflow-hidden  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-file-invoice-dollar text-warning fa-3x"></i>
                        <div class="ms-4">
                            <h4 class="text-muted dashboard-text">
                                {{number_format($tva_facture,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">TVA Facturé</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-xl-3 col-12 col-md-6  __dashboard_sortable_item">
            <div class="card overflow-hidden">
                <div class="card-body bg-soft-danger  overflow-hidden  rounded">
                    <div class="d-flex flex-nowrap align-items-center">
                        <i class="fa fa-percentage text-danger fa-3x"></i>
                        <div class="ms-4">
                            <h4 class="text-muted dashboard-text">
                                {{number_format($tva_recup,3,'.',' ')}} MAD
                            </h4>
                            <h6 class="m-0 text-muted  ">TVA Récuperé</h6>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-8 col-12 __dashboard_item __dashboard_sortable_item">
            <div class="card" style="height: calc(100% - 24px)">
                <div class="card-body">
                    <div class="card-title">
                        <h5 class="text-black-50">Aperçu graphique annuel des ventes</h5>
                    </div>
                    <div id="morris-line-chart">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-12 __dashboard_item __dashboard_sortable_item">
            <div class="card" style="height: calc(100% - 24px)">
                <div class="card-body">
                    <h5 class="text-black-50 pb-3"> Trésorerie au: {{today()->format('d/m/Y')}}</h5>
                        <div class="col-12 my-3">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <div class="overview-content">
                                        <i class="fa fa-university text-success"></i>
                                    </div>
                                </div>
                                <div class="col-8 text-end">
                                    <p class="text-muted font-size-13 mb-1">Banque </p>
                                    <h4 class="mb-0 font-size-20 dashboard-text">{{number_format($total_banque,3,'.',' ')}}
                                        MAD</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 my-3">
                            <div class="row align-items-center">
                                <div class="col-4">
                                    <div class="overview-content">
                                        <i class="fa fa-cash-register text-success"></i>
                                    </div>
                                </div>
                                <div class="col-8 text-end">
                                    <p class="text-muted font-size-13 mb-1">Caisse </p>
                                    <h4 class="mb-0 font-size-20 dashboard-text">{{number_format($total_caisse,3,'.',' ')}}
                                        MAD</h4>
                                </div>
                            </div>
                        </div>
                    <hr class="border">
                    <h5 class="text-primary" >Résultat de période</h5>
                    <h2 class="text-end text-success" >{{number_format($ca_vente - $ca_achat,3,'.',' ')}} MAD  </h2>
                    <hr class="border">
                    <h5 class="text-primary" >Marge net</h5>
                    <h2 class="text-end text-success">
                        {{ number_format($ca_vente != 0 ? (($ca_vente - $ca_achat) / $ca_vente * 100) : 0, 3, '.', ' ') }} %
                    </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{asset('libs/raphael/raphael.min.js')}}"></script>
    <script src="{{asset('libs/morris.js/morris.min.js')}}"></script>
    <script>
        $('.peity-line').each(function () {
            $(this).peity("line", $(this).data());
        });
        var line = new Morris.Line({
            element: 'morris-line-chart',
            resize: false,
            data: @json($chart_data),
            xkey: 'y',
            ykeys: ['ca', 'depenses', 'commandes', 'achats', 'encaissement'],
            labels: ['CA', 'Depenses', 'Commandes', 'Achats', 'Encaissement'],
            gridLineColor: 'rgba(108, 120, 151, 0.2)',
            lineColors: ['#0acf97', '#44a2d2', '#f9bc0b', '#f1556c', '#3b5461'],
            lineWidth: 3,
            parseTime: false,
            hideHover: 'auto'
        });
    </script>
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
            if (customize_mode) {
                customize_mode = !customize_mode;
                $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                $('.dashboard-text').each(function (e) {
                    let text = $(this).html()
                    $(this).data('value', text);
                    $(this).html(text.replace(/\d/g, '*'))

                })
            } else {
                $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye')
                customize_mode = !customize_mode;
                $('.dashboard-text').each(function (e) {
                    $(this).html($(this).data('value'))
                })
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
            $(this).html(text.replace(/\d/g, '*'))
        })
        $('.ca-trigger').click(function () {
            $('.ca').toggleClass('d-none')
        })
    </script>
@endpush
