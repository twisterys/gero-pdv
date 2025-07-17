@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapport achat-vente 2')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col-12 mb-4 row m-0 justify-content-between">
            <div class="col-md-6 col-12">
                <h2 class="m-0" >Rapport d'achat et vente</h2>
            </div>
            <div class="page-title-right  col-xl-3 col-lg-4 col-md-5 col-sm-6">
                <form action="{{ route('rapports.achat_vente') }}" method="get">
                    <div class="input-group  border-1 border border-light rounded" id="datepicker1" style="z-index: 9;">
                        <input type="text" class="form-control datepicker text-primary ps-2 "
                               id="i_date"
                               placeholder="mm/dd/yyyy"
                               name="i_date" readonly>
                        <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Display the calculated values -->
                    <div class="card-title">
                        <h4>Achats</h4>
                        <hr>
                    </div>
                    <div class="row w-100 align-items-center">
                        <div class="col-6">
                            <label for="types_achat" class="form-label">Documents d'achat</label>
                            <select name="types_achat" id="types_achat" multiple class="form-control">
                                @foreach($achats_types as $type)
                                    <option
                                        @selected(in_array($type,$types_achat)) value="{{$type}}">@lang('achats.'.$type)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="retours_achat" class="form-label">Documents de retour</label>
                            <select name="retours_achat" id="retours_achat" multiple class="form-control">
                                @foreach($achats_types as $type)
                                    <option
                                        @selected(in_array($type,$retours_achat)) value="{{$type}}">@lang('achats.'.$type)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mt-3">
                            <label for="balises_achat" class="form-label ">Balises</label>
                            <select name="balises_achat" id="balises_achat" multiple class="form-control">
                                @foreach($balises as $balise)
                                    <option
                                         value="{{$balise->id}}">{{$balise->nom}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div id="achat-container" class="position-relative">
                        @include('rapports.partials.achat_vente.achat')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body ">
                    <!-- Display the calculated values -->
                    <div class="card-title">
                        <h4 class="mb-0 me-2">Ventes </h4>
                        <hr>
                    </div>
                    <div class="row w-100 align-items-center">
                        <div class="col-6">
                            <label for="types_vente" class="form-label">Documents de vente</label>
                            <select name="types_vente" id="types_vente" multiple class="form-control">
                                @foreach($ventes_types as $type)
                                    <option
                                        @selected(in_array($type,$types_vente)) value="{{$type}}">@lang('ventes.'.$type)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="retours_vente" class="form-label">Documents de retour</label>
                            <select name="retours_vente" id="retours_vente" multiple class="form-control">
                                @foreach($ventes_types as $type)
                                    <option
                                        @selected(in_array($type,$retours_vente)) value="{{$type}}">@lang('ventes.'.$type)</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mt-3">
                            <label for="balises_vente" class="form-label">Balises</label>
                            <select name="balises_vente" id="balises_vente" multiple class="form-control">
                                @foreach($balises as $balise)
                                    <option
                                        value="{{$balise->id}}">{{$balise->nom}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="vente-container" class="position-relative">
                        @include('rapports.partials.achat_vente.vente')
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body ">
                    <!-- Display the calculated values -->
                    <div class="card-title">
                        <h4 class="mb-0 me-2">Dépenses</h4>
                        <hr>
                    </div>
                    <div class="row w-100 align-items-center">
                    </div>
                    <div id="depense-container" class="position-relative">
                        @include('rapports.partials.achat_vente.depense')
                    </div>
                </div>

            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="card">
                <div class="card-body">
                    <!-- Display the calculated values -->
                    <div id="recap-container" class="position-relative">
                        @include('rapports.partials.achat_vente.recap')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script>
        @php
            $exercice = session()->get('exercice');
        @endphp
        const __datepicker_dates = {
            "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 1':['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}','{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2':['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3':['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4':['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
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
        $('#types_vente , #retours_vente , #retours_achat , #types_achat, #balises_achat , #balises_vente').select2({
            minimumResultsForSearch: -1,
            multiple: !0,
        })
        $(document).on('change', '#types_vente , #retours_vente , #retours_achat , #types_achat , #balises_achat, #balises_vente', function () {
            $('#vente-container,#achat-container,#recap-container').append('<div class="d-flex align-items-center justify-content-center position-absolute containers-spinner" style="inset: -5px; backdrop-filter: blur(4px)" >' + __spinner_element_lg + '</div>')
            $.ajax({
                url: "{{route('rapports.achat_vente')}}",
                data: {
                    'types_achat': $('#types_achat').val(),
                    'types_vente': $('#types_vente').val(),
                    'retours_vente': $('#retours_vente').val(),
                    'retours_achat': $('#retours_achat').val(),
                    'balises_achat':$('#balises_achat').val(),
                    'balises_vente':$('#balises_vente').val()
                },
                success: function (response) {
                    $('#vente-container').html(response.vente);
                    $('#achat-container').html(response.achat);
                    $('#recap-container').html(response.recap);
                },
                error: function () {
                    $('.containers-spinner').remove()
                }
            });
        })
    </script>
@endpush

