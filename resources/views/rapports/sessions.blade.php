@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Mouvement de stock')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col">
            <div  class="card-title justify-content-between align-items-center">
                <h2 >Rapport des sessions </h2>
            </div>
        </div>
        <div class="page-title-right col-xl-1 col-lg-4 col-md-5 col-sm-6 col-12 ">
{{--            <div class="input-group  border-1 border border-light rounded" id="datepicker1">--}}
{{--                <input type="text" class="form-control datepicker text-primary ps-2 "--}}
{{--                       id="i_date"--}}
{{--                       placeholder="mm/dd/yyyy"--}}
{{--                       name="i_date" readonly style="z-index: 1000 !important;">--}}
{{--                <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>--}}
{{--            </div>--}}

        </div>
    </div>
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-body ">
                    <div class="card-title">
                        <div class="d-flex  justify-content-between align-items-center flex-md-nowrap flex-wrap" id="__fixed">
                            <h5 class="m-0">Liste des sessions
                            </h5>
                            <div class="page-title-left col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                                <div class="input-group mb-3 flex-nowrap">
                                    <span class="input-group-text text-primary"><i class="fas fa-search"></i></span>
                                    <select name="i_search" id="i_search">
                                        <option value="" >Tous</option>
                                    @foreach($magasins as $magasin)
                                            <option value="{{$magasin->id}}">{{$magasin->nom}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
{{--                    <div class="card-title justify-content-between align-items-center">--}}
{{--                        <h4>Liste des sessions</h4>--}}
{{--                        <hr>--}}
{{--                    </div>--}}
{{--                    <div class="page-title-right col-xl-1 col-lg-4 col-md-5 col-sm-6 col-12 ">--}}
{{--                        <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer</button>--}}
{{--                    </div>--}}
{{--                    <div class="page-title-left col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">--}}
{{--                        <div class="input-group mb-3">--}}
{{--                            <span class="input-group-text text-primary"><i class="fas fa-search"></i></span>--}}
{{--                            <input type="text" id="i_search" name="i_search" class="form-control" placeholder="Chercher par produit">--}}
{{--                            <button id="i_search_button" class="btn btn-soft-secondary">Chercher</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <hr>--}}



                    <div class="col-12 ">
                        <div>
                            <table  id="datatable" class="table table-bordered table-striped" >
                                <thead>
                                <tr>
                                    <th>Utilisateur</th>
                                    <th>Magasin</th>
                                    <th>Etat</th>
                                    <th>Date ouverture</th>
                                    <th>Date fermeture</th>
                                    <th>Total ventes</th>
                                    <th style="width: 100px">Actions</th>

                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
{{--    <div  class="modal fade " id="show-modal" tabindex="-1" aria-hidden="true"--}}
{{--         style="display: none;">--}}
{{--        <div class="modal-dialog  modal-dialog-centered" style="max-width: 1000px !important;">--}}
{{--            <div class="modal-content  ">--}}

{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

@endsection

@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script>
        @php
            $exercice = session()->get('exercice');
        @endphp
        {{--const __datepicker_dates = {--}}
        {{--    "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],--}}
        {{--    'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],--}}
        {{--    'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],--}}
        {{--    'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],--}}
        {{--    'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],--}}
        {{--    'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],--}}
        {{--    'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],--}}
        {{--};--}}
        {{--const __datepicker_start_date = '{{$date_picker_start}}';--}}
        {{--const __datepicker_end_date = '{{$date_picker_end}}';--}}
        {{--const __datepicker_min_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';--}}
        {{--const __datepicker_max_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';--}}
        // $('.datepicker').daterangepicker({
        //     ranges: __datepicker_dates,
        //     locale: {
        //         format: "DD/MM/YYYY",
        //         separator: " - ",
        //         applyLabel: "Appliquer",
        //         cancelLabel: "Annuler",
        //         fromLabel: "De",
        //         toLabel: "à",
        //         customRangeLabel: "Plage personnalisée",
        //         weekLabel: "S",
        //         daysOfWeek: [
        //             "Di",
        //             "Lu",
        //             "Ma",
        //             "Me",
        //             "Je",
        //             "Ve",
        //             "Sa"
        //         ],
        //         monthNames: [
        //             "Janvier",
        //             "Février",
        //             "Mars",
        //             "Avril",
        //             "Mai",
        //             "Juin",
        //             "Juillet",
        //             "Août",
        //             "Septembre",
        //             "Octobre",
        //             "Novembre",
        //             "Décembre"
        //         ],
        //         firstDay: 1
        //     },
        //     startDate: __datepicker_start_date,
        //     endDate: __datepicker_end_date,
        //     minDate: __datepicker_min_date,
        //     maxDate: __datepicker_max_date
        // })
        $('#i_date').change(function () {
            $(this).closest('form').submit()
        })
    </script>
    <script>
        $('#i_search').select2({
            width: '100%',
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: !0,
        });
    </script>
    <script>
        $('.filter-btn').click(e => {
            $('.switch-filter').toggleClass('d-none')
        })
    </script>
    <script>
        const __dataTable_filter_trigger_button_id = '#search-btn';
        const __dataTable_filter_inputs_id = {
            i_search: '#i_search',

        }
        const __dataTable_filter = function (data) {
            d = __datatable_ajax_callback(data);
        };


        var table = $('#datatable').DataTable({
            dom: 'lrtip',
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

            ajax: {
                url: "{{ route('rapports.sessions') }}",
                data: function (d){
                    if (typeof __dataTable_filter_inputs_id === 'object'){
                        for (const key in __dataTable_filter_inputs_id){
                            d[key]= $(__dataTable_filter_inputs_id[key]).val();
                        }
                    }
                    d= __datatable_ajax_callback(d)
                }

            },
            columns: [
                { data: 'user_id' },
                { data: 'magasin_id' },
                { data: 'ouverte' },
                { data: 'created_at' },
                { data: 'date_fin' },
                { data: 'total_ttc' },
                {data: 'actions', name: 'actions', orderable: false,},

            ],

            orderCellsTop: true,
            order: [[1, 'desc']],
            footerCallback:
                function (row, data, start, end, display) {
                    var api = this.api(), data;
                    var total = api.column(2).data().reduce(function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0);
                    $('#total_ventes').html(total.toFixed(2) + ' MAD');

                }
        });

        function hideButtons() {
            table.buttons().container().hide();
        }
        $("#datatable_wrapper").prepend('<div class="row actions w-100"></div>')
        $("#datatable_wrapper>.dt-buttons").wrap('<div class="col-6 actions d-flex justify-content-end"></div>');
        $("#datatable_wrapper>.dataTables_length").wrap('<div class="col-6 actions"></div>');
        $("#datatable_wrapper>.col-6.actions").appendTo($('.row.actions'));

        // $('#i_date').on('change', function(){
        //     table.ajax.reload();
        // })
        $('#i_search').on('change', function(){
            table.ajax.reload();
        })
        if(typeof __dataTable_filter_trigger_button_id !== 'undefined'){
            $(__dataTable_filter_trigger_button_id).click(e=> table.ajax.reload())
        }

    </script>
    <script>
        $(document).on('click','.show-btn',function (){
            let btn = $(this);
            let html = btn.html();
            $.ajax({
                url: btn.data('url'),
                success: function (response) {
                    btn.html(html)
                    btn.closest('form').submit();
                },
                error: function (){
                }
            })
        })
    </script>
@endpush

