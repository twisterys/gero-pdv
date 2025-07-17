@extends('layouts.main')
@section('document-title','Banques')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
    <style>
        .last-col {
            width: 1%;
            white-space: nowrap;
        }
    </style>
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <a href="{{route('parametres.liste')}}"><i
                                    class="fa fa-arrow-left text-success me-2"></i></a>
                            <h5 class="m-0">Banques</h5>
                        </div>
                        <div class="page-title-right">
                            <button class="btn btn-soft-success" data-bs-target="#add-modal"
                                    data-bs-toggle="modal"><i class="mdi mdi-plus"></i> Ajouter
                            </button>
                        </div>
                    </div>
                    <hr class="border">
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-centered table-bordered rounded mb-0" id="datatable">
                        <thead>
                        <tr>
                            <th>Logo</th>
                            <th>Nom</th>
                            <th style="width: 1%; white-space: nowrap">Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="edit-modal" tabindex="-1" aria-labelledby="add-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="add-modal" tabindex="-1" aria-labelledby="add-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="edit-modal-title">Ajouter une banque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="add-form" action="{{route('banques.sauvegarder')}}" enctype="multipart/form-data" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="name-input">Nom</label>
                                <input type="text"  required class="form-control" id="name-input" name="nom">
                                <div class="invalid-feedback"></div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="i_image"
                                       class="form-label ">Logo</label>
                                <input class="i_image" name="i_image" type="file" id="i_image-add" accept="image/*">
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script  >
        const __dataTable_filter = function (data) {
            d = __datatable_ajax_callback(data);
        };
        var table = $('#datatable').DataTable({
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
                    className: 'last-col',
                    targets: -1,
                },
                {
                    className: 'last-col',
                    targets: 0,
                }
            ],
            ajax: {
                url: "{{ route('banques.liste') }}",
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
                {data: 'image', name: 'image',orderable: false},
                {data: 'nom', name: 'nom'},
                {
                    data: 'actions', name: 'actions', orderable: false,
                },
            ],
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 25,
        })
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
    </script>
    <script>
        $(".i_image").dropify({
            messages: {
                default: "Glissez-déposez un fichier ici ou cliquez",
                replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
                remove: "Supprimer",
                error: "Désolé, le fichier trop volumineux",
            },
        });
        $(document).on('show.bs.modal','#edit-modal',function (){
            $(".i_image").dropify({
                messages: {
                    default: "Glissez-déposez un fichier ici ou cliquez",
                    replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
                    remove: "Supprimer",
                    error: "Désolé, le fichier trop volumineux",
                },
            });
        })
        $(document).on('submit','#edit-form,#add-form',function (e){
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            $('.is-invalid').removeClass('is-invalid')
            $.ajax({
                url: form.attr('action'),
                method:'POST',
                headers: {
                    'X-CSRF-TOKEN':__csrf_token
                },
                data: formData,
                contentType: false,
                processData: false,
                success : function (response){
                    table.ajax.reload();
                    form.closest('.modal').modal('hide');
                    toastr.success(response)
                },
                error: function (xhr){
                    if(xhr.status === 422){
                        let errors = xhr.responseJSON.errors;
                        for (const [key,value] of Object.entries(errors)){
                            form.find('[name="'+key+'"]').addClass('is-invalid')
                            form.find('[name="'+key+'"]').closest('.col-12').find('.invalid-feedback').addClass('d-block').html(value)
                        }
                    }else{
                        toastr.error(xhr.responseText);
                    }
                }
            })
        })
    </script>
@endpush



