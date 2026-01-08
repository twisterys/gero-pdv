@php
use Illuminate\Support\Carbon;

@endphp
@extends('layouts.main')
@section('document-title', ucwords($o_client->nom))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('clients.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                    {{ucwords($o_client->nom)}} <span class="text-muted font-size-10">({{$o_client->reference}})</span>
                                </h5>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="me-2">
                                    <div class="input-group border-1 border border-light rounded" id="datepicker1">
                                        <input type="text" class="form-control datepicker text-primary ps-2"
                                               id="date_emission_filter"
                                               placeholder="dd/mm/yyyy"
                                               name="date_emission" readonly>
                                        <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                                    </div>
                                </div>
                                <div class="pull-right">
                                    <a href="{{route('clients.modifier',$o_client->id)}}" class="btn btn-soft-warning"><i
                                            class="fa fa-edit"></i> Modifier</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="content-to-refresh" class="row">
            @include('clients.partials.afficher_content')
        </div>
    </div>
    <div class="modal fade" id="event-add-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center">Ajouter une activité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="event-add" action="{{route('events.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <input type="hidden" name="client_id" value="{{$o_client->id}}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="titre-add-input">Titre</label>
                                <input type="text" required class="form-control" id="titre-add-input" name="titre">
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="type-add-input">Type</label>
                                <select name="type" id="type-add-input">
                                    @foreach($types_event as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="date-add-input">Date</label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="date-add-input" name="date">
                                    <span class="input-group-text">
                                        <span class="fa fa-calendar-alt"></span>
                                    </span>
                                    <div class="invalid-feedback"></div>

                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="time-add-input">Heure</label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="debut-add-input" name="debut" data-inputmask="'alias': 'datetime'"
                                           placeholder="Début">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="fin-add-input" name="fin" data-inputmask="'alias': 'datetime'"
                                           placeholder="Fin">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="description-add-input">Description</label>
                                <textarea name="description" class="form-control" id="description-add-input" cols="30" rows="5"></textarea>
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
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="event-edit-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="event-show-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <input type="hidden" id="#client-select" value="{{$o_client->id}}">
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script>
        const __dataTable_filter_inputs_id = {
            client_id: '#client-input',
            date_emission: '#date_emission_filter'
        }
        const __dataTable_filter = function (data) {
            d = __datatable_ajax_callback(data);
        };

        $(function() {
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

            const __datepicker_start_date = '{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}';
            const __datepicker_end_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';
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
                    daysOfWeek: ["Di", "Lu", "Ma", "Me", "Je", "Ve", "Sa"],
                    monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
                    firstDay: 1
                },
                startDate: __datepicker_start_date,
                endDate: __datepicker_end_date,
                minDate: __datepicker_min_date,
                maxDate: __datepicker_max_date
            });

            $('.datepicker').on('apply.daterangepicker', function(ev, picker) {
                refresh_content();
            });

            function refresh_content() {
                let date_emission = $('#date_emission_filter').val();
                $.ajax({
                    url: "{{ route('clients.afficher', $o_client->id) }}",
                    data: { date_emission: date_emission },
                    success: function(html) {
                        $('#content-to-refresh').html(html);
                        // Re-initialize DataTables if a tab was active
                        let activeTab = $('.ventes-tabs.active');
                        if (activeTab.length > 0) {
                            activeTab.trigger('click');
                        }
                    }
                });
            }
        });

        $(document).on('click', '.ventes-tabs', function () {
            let nav_tab = $(this);
            $(nav_tab.attr('href') + ' table').DataTable().destroy();

            let url = "{{url('ventes/')}}/" + nav_tab.data('type') + '/liste';
            let __dataTable_columns = [
                {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
                {data: 'reference', name: 'reference'},
                {data: 'statut', name: 'statut'},
                {data: 'date_emission', name: 'date_emission'},
                {data: 'total_ttc', name: 'total_ttc'},
                {data: 'convertir_de', name: 'convertir_de', orderable: false,},
                {
                    data: 'actions', name: 'actions', orderable: false,
                },
            ];
            if (nav_tab.data('paiement')) {
                __dataTable_columns = [
                    {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
                    {data: 'reference', name: 'reference'},
                    {data: 'statut', name: 'statut'},
                    {
                        data: 'statut_paiement', name: 'statut_paiement'
                    },
                    {data: 'date_emission', name: 'date_emission'},
                    {data: 'total_ttc', name: 'total_ttc'},
                    {data: 'encaisser', name: 'encaisser'},
                    {data: 'solde', name: 'solde'},
                    {data: 'convertir_de', name: 'convertir_de', orderable: false,},
                    {
                        data: 'actions', name: 'actions', orderable: false,
                    },
                ];
            }
            $(nav_tab.attr('href') + ' table').dataTable(
                {
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
                        }
                    ],
                    ajax: {
                        url: url,
                        data: function (d) {
                            if (typeof __dataTable_filter_inputs_id === 'object') {
                                for (const key in __dataTable_filter_inputs_id) {
                                    d[key] = $(__dataTable_filter_inputs_id[key]).val();
                                }
                            }
                            d = __datatable_ajax_callback(d)
                        }
                    },
                    columns: __dataTable_columns,
                    orderCellsTop: true,
                    order: [[1, 'desc']],
                    pageLength: 10,
                }
            )
        })
    </script>
    <script>
        $('#date-add-input').datepicker({
            format: 'dd/mm/yyyy'
        })
        $('#debut-add-input,#fin-add-input').inputmask({
            inputFormat: "HH:MM"
        })
        $('#type-add-input').select2({
            width:'100%',
            minimumResultsForSearch:-1
        })
        $(document).on('submit','#event-add,#event-edit',function (e){
            e.preventDefault();
            let btn =  $(this).find('button').attr('disabled',"true")
            let form = $(this)
            form.find('.is-invalid').removeClass('is-invalid')
            $.ajax({
                url:form.attr('action'),
                method:'post',
                data:form.serialize(),
                success:function (response){
                    form.trigger('reset');
                    btn.removeAttr('disabled');
                    toastr.success(response);
                    $('#event-add-modal,#event-edit-modal').modal('hide');
                    location.reload()
                },
                error: function (xhr){
                    btn.removeAttr('disabled')

                    if(xhr.status === 422){
                        let errors = xhr.responseJSON.errors;
                        for (const [key,value] of Object.entries(errors)){
                            form.find('[name="'+key+'"]').addClass('is-invalid')
                            form.find('[name="'+key+'"]').siblings('.invalid-feedback').html(value)
                        }

                    }else{
                        toastr.error(xhr.responseText);
                    }
                }
            })
        })
    </script>
@endpush
