@extends('layouts.main')
@php use Carbon\Carbon; @endphp

@section('document-title', 'Authentication Logs')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
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
                        <div  class="d-flex switch-filter justify-content-between align-items-center">
                            <div>
                                <a href="{{route('utilisateurs.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3">
                                    <i class="mdi mdi-message-alert  me-2 text-success"></i> Logs d'authentications de {{$user->name}}
                                </h5>
                            </div>
                            <div class="pull-right page-title-right col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                                <div class="input-group  border-1 border border-light rounded" id="datepicker1">
                                    <input type="text" class="form-control"
                                           placeholder="mm/dd/yyyy" id="i_date" name="i_date" data-date-container="#datepicker1" data-date-autoclose="true" data-provide="datepicker">
                                    <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                                </div>
                            </div>
                        </div>

                        <hr class="border">
                    </div>

                    <div class="row px-3">
                        <div class="page-title-left col-xl-3 col-lg-4 col-md-5 col-sm-6 col-12 mt-2 mt-sm-0">
                            <div class="input-group mb-3">
                                <span class="input-group-text text-primary"><i class="fas fa-search"></i></span>
                                <input type="text" id="i_search" name="i_search" class="form-control" placeholder="Chercher par utilisateur">
                                <button id="i_search_button" class="btn btn-soft-secondary">Chercher</button>
                            </div>
                        </div>
                        <hr>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>

                                        <th>Utilisateur</th>
                                        <th>Date de connexion</th>
                                        <th>Date de déconnexion</th>
                                        <th>Agent</th>
                                        <th>Adresse IP</th>
                                        <th>Location</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>



@endsection


@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('libs/dropify/js/dropify.min.js')}}"></script>

    <script src="{{asset('js/form-validation.init.js')}}" ></script>
    <script>

        const __dataTable_columns = [
            // {data: 'reference',},
            {data : 'user_name'},
            {data : 'login_at'},
            {data : 'logout_at'},
            {data : 'user_agent'},
            {data : 'ip_address'},
            {data : 'location'},

        ];
        const __dataTable_ajax_link = "{{ route('utilisateurs.connexion', $user->id)}}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            i_date:'#i_date',
            i_search: '#i_search',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';

        const __dataTable_filter = function (data) {
            d = __datatable_ajax_callback(data);
        };
        var table = $(__dataTable_id).DataTable({
            dom: 'lBrtip',
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
                {extend: 'colvis', className: 'btn-soft-primary'}
            ],
            ajax: {
                url: __dataTable_ajax_link,
                data: function (d){
                    if (typeof __dataTable_filter_inputs_id === 'object'){
                        for (const key in __dataTable_filter_inputs_id){
                            d[key]= $(__dataTable_filter_inputs_id[key]).val();
                        }
                    }
                    d= __datatable_ajax_callback(d)
                }
            },
            columns:__dataTable_columns,
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 10,
        })
        function hideButtons() {
            table.buttons().container().hide();
        }

        $('#i_date').on('change', function(){
            table.ajax.reload();
        })
        $('#i_search').on('change', function(){
            table.ajax.reload();
        })
        $("#datatable_wrapper").prepend('<div class="row actions w-100"></div>')
        $("#datatable_wrapper>.dt-buttons").wrap('<div class="col-6 actions d-flex justify-content-end"></div>');
        $("#datatable_wrapper>.dataTables_length").wrap('<div class="col-6 actions"></div>');
        $("#datatable_wrapper>.col-6.actions").appendTo($('.row.actions'));
    </script>

    <script>
        var currentDate = new Date();
        currentDate.setFullYear(currentDate.getFullYear());
        var month = (currentDate.getMonth() + 1).toString().padStart(2, '0');
        var day = currentDate.getDate().toString().padStart(2, '0');
        document.getElementById('i_date').value = day + '/' + month + '/' + currentDate.getFullYear();
    </script>



@endpush
