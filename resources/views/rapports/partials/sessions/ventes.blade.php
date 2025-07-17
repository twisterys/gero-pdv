@php use Carbon\Carbon; @endphp
@extends('layouts.main')
@section('document-title', 'Rapport de session')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
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
                                <a href="{{ route('rapports.sessions') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3">
                                    Ventes de session
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body row">
                        <!-- Display the calculated values -->
                        <div class="card-title col-md-4   justify-content-between align-items-center">
                            <h5 class="text-center">Nombre de {{(__('ventes.'. $vente->value))}}</h5>
                            <h3 id="total_achats" class="text-danger text-center">
                                {{$count_ventes}}
                            </h3>
                        </div>
                        <div class="card-title col-md-4 justify-content-between align-items-center">
                            <h5 class="text-center">Nombre de {{(__('ventes.'. $retour->value))}}</h5>
                            <h3 id="total_ventes" class="text-success text-center">
                                {{$count_retours}}
                            </h3>
                        </div>
                        <div class="card-title col-md-4 justify-content-between align-items-center">
                            <h5 class="text-center">Total TTC de {{(__('ventes.'. $vente->value))}}</h5>
                            <h3 id="total_benefice" class="text-warning text-center">
                                {{$total_ventes}} DH
                            </h3>
                        </div>
                        <div class="card-title col-md-4  justify-content-between align-items-center">
                            <h5 class="text-center">Total TTC de {{(__('ventes.'. $retour->value))}}</h5>
                            <h3 id="proft_margin" class="text-info text-center">
                                {{$total_retours}} DH
                            </h3>
                        </div>
                        <div class="card-title col-md-4  justify-content-between align-items-center">
                            <h5 class="text-center">Total TTC de dépenses</h5>
                            <h3 id="proft_margin" class="text-primary text-center">
                                {{$depenses_total}} DH
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body ">
                        <!-- Display the calculated values -->
                        <div class="card-title justify-content-between align-items-center">
                            <h4>Ventes</h4>
                            <hr>
                        </div>
                        <div class="col-12">
                            <div>
                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Référence de vente</th>
                                        <th>Type document</th>
                                        <th>Date document</th>
                                        <th>Montant TTC</th>
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
                    <div class="card-body ">
                        <!-- Display the calculated values -->
                        <div class="card-title justify-content-between align-items-center">
                            <h4>Dépenses</h4>
                            <hr>
                        </div>
                        <div class="col-12">
                            <div>
                                <table id="datatable_depense" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>Référence</th>
                                        <th>Nom</th>
                                        <th>Catégorie</th>
                                        <th>Montant TTC</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @endsection

        @push('scripts')
            @include('layouts.partials.js.__datatable_js')
            <script>
                let __dataTable_columns = [
                    {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
                    {data: 'reference'},
                    {data: 'type_document'},
                    {data: 'date_document'},
                    {data: 'total_ttc'},

                ];
                let __dataTable_ajax_link = "{{ route('rapports.sessions.ventes', $o_session->id) }}";
                let __dataTable_id = "#datatable";
                let __dataTable_filter_inputs_id = {
                    i_date: '#i_date',
                    i_search: '#i_search',
                    i_types: '#i_type'
                }
                let __dataTable_filter_trigger_button_id = '#i_search_button';
                $('#i_date').change(function () {
                    table.ajax.reload();
                })
                $('#i_type').select2({
                    minimumResultsForSearch: -1,
                    multiple: !0,
                })
            </script>
            <script src="{{asset('js/dataTable_init.js')}}"></script>
            <script>
                var table2 = $('#datatable_depense').DataTable({
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
                        url: "{{ route('rapports.sessions.ventes', ['id'=>$o_session->id,'type'=>'depense']) }}",
                        data: function (d) {
                            if (typeof __dataTable_filter_inputs_id === 'object') {
                                for (const key in __dataTable_filter_inputs_id) {
                                    d[key] = $(__dataTable_filter_inputs_id[key]).val();
                                }
                            }
                            d = __datatable_ajax_callback(d)
                        }
                    },
                    columns: [
                        {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
                        {data: 'reference'},
                        {data: 'nom_depense'},
                        {data: 'categorie_depense_id'},
                        {data: 'montant'},

                    ],
                    orderCellsTop: true,
                    order: [[0, 'desc']],
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
                $("#datatable_depense_wrapper").prepend('<div class="row actions_depense w-100"></div>')
                $("#datatable_depense_wrapper>.dt-buttons").wrap('<div class="col-6 actions d-flex justify-content-end"></div>');
                $("#datatable_depense_wrapper>.dataTables_length").wrap('<div class="col-6 actions"></div>');
                $("#datatable_depense_wrapper>.col-6.actions").appendTo($('.row.actions_depense'));
            </script>

    @endpush

