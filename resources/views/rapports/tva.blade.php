@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapport de TVA')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col">
            <div class="card-title justify-content-between align-items-center">
                <h2>Rapport de TVA</h2>
            </div>
        </div>
        <div class="page-title-right col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
            <div class="input-group border-1 border border-light rounded" id="datepicker1">
                <input type="text" class="form-control datepicker text-primary ps-2"
                       id="i_date"
                       placeholder="mm/dd/yyyy"
                       name="i_date" readonly style="z-index: 1 !important;">
                <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <br>
                    <!-- Display the calculated values -->
                    <div class="d-flex flex-md-nowrap flex-wrap">
                        <div class="card-title me-5 justify-content-between align-items-center">
                            <h5 > TVA encaissée sur les ventes</h5>
                            <hr class="border">
                            <div id="ventes-tva" class="text-danger"></div>
                        </div>
                        <div class="card-title me-5 justify-content-between align-items-center">
                            <h5> TVA décaissée sur les achats</h5>
                            <hr class="border">
                            <div id="achats-tva" class="text-success"></div>
                        </div>
                        <div class="card-title me-5 justify-content-between align-items-center">
                            <h5> Résultat de TVA</h5>
                            <hr class="border">
                            <div id="somme-tva" class="text-warning"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 mt-4">
        <div class="card">
            <div class="card-body ">
                <div class="col-12">
                    <div>
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th style="width: 1%; white-space: nowrap"></th>
                                <th>Date de paiement</th>
                                <th>Elément payé</th>
                                <th>Type de document</th>
                                <th>TVA encaissée</th>
                                <th>TVA décaissée</th>
                                <th>Total TVA</th>
                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('rapports.partials.rapport_help')
@endsection

@push('scripts')
    @include('layouts.partials.js.__datatable_js')
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
            maxDate: __datepicker_max_date,

        })
    </script>
    @include('layouts.partials.js.__datatable_js')
    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'date_paiement'},
            {data: 'reference'},
            {data: 'payable_type'},
            {data: 'tva_ventes', render: function(data) { return data ? data : '--'; }},
            {data: 'tva_achats', render: function(data) { return data ? data : '--'; }},
            {data: 'tva_result'},
        ];
        const __dataTable_ajax_link = "{{ route('rapports.tva') }}";
        const __dataTable_id = "#datatable";
        const __sort_column=2;
        const __dataTable_filter_inputs_id = {
            i_date: '#i_date',
        }
        const __dataTable_filter_trigger_button_id = '#i_search_button';


        const __dataTable_filter = function (data) {
            d = __datatable_ajax_callback(data);
        };
        if (typeof  __sort_column != "undefined") {
            var sortby = __sort_column;
        } else {
            var sortby = 1;
        }
        var table = $(__dataTable_id).DataTable({
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
                    orderable: false,
                    className: 'select-checkbox',
                    targets: 0
                },
                {
                    orderable: false,
                    className: 'last-col',
                    targets: -1,
                }
            ],
            ajax: {
                url: __dataTable_ajax_link,
                data: function (d) {
                    if (typeof __dataTable_filter_inputs_id === 'object') {
                        for (const key in __dataTable_filter_inputs_id) {
                            d[key] = $(__dataTable_filter_inputs_id[key]).val();
                        }
                    }
                    d = __datatable_ajax_callback(d)
                },
                dataSrc:function (response){
                    $('#achats-tva').text(response['achats_tva'] + ' MAD');
                    $('#ventes-tva').text(response['ventes_tva'] + ' MAD');
                    $('#somme-tva').text(response['somme'] + ' MAD');
                    return response['data'];
                }
            },
            columns: __dataTable_columns,
            orderCellsTop: true,
            order: [[sortby, 'desc']],
            pageLength: 10,
        })

        $(document).on('click', '#select-all-row', function (e) {
            if (this.checked) {
                $('#datatable')
                    .find('tbody')
                    .find('input.row-select')
                    .each(function () {
                        if (!this.checked) {
                            $(this)
                                .prop('checked', true)
                                .change();
                        }
                    });
            } else {
                $('#datatable')
                    .find('tbody')
                    .find('input.row-select')
                    .each(function () {
                        if (this.checked) {
                            $(this)
                                .prop('checked', false)
                                .change();
                        }
                    });
            }
        });
        if (typeof __dataTable_filter_trigger_button_id !== 'undefined') {
            $(__dataTable_filter_trigger_button_id).click(e => table.ajax.reload())
        }
        $("#datatable_wrapper").prepend('<div class="row actions w-100"></div>')
        $("#datatable_wrapper>.dt-buttons").wrap('<div class="col-6 actions d-flex justify-content-end"></div>');
        $("#datatable_wrapper>.dataTables_length").wrap('<div class="col-6 actions"></div>');
        $("#datatable_wrapper>.col-6.actions").appendTo($('.row.actions'));

        function getSelectedRows() {
            var selected_rows = [];
            var i = 0;
            $('.row-select:checked').each(function () {
                selected_rows[i++] = $(this).val();
            });
            return selected_rows;
        }

        $(document).ajaxComplete(function () {
            // Required for Bootstrap tooltips in DataTables
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

        });
        $('#i_date').change(function () {
            table.ajax.reload();
        })


    </script>

@endpush
