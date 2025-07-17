var table ;
const __dataTable_filter = function (data) {
    d = __datatable_ajax_callback(data);
};
if (typeof  __sort_column != "undefined") {
    var sortby = __sort_column;
} else {
    var sortby = 1;
}
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
$('#datepicker').change(function () {
    table.ajax.reload()
})
$('#compte-input,#method-input').select2({
    minimumResultsForSearch: -1,
})
$('#method-input').on('change', function () {
    check()
})
check()
function check() {
    let methods = ['cheque', 'lcn'];
    if (methods.indexOf($('#method-input').find('option:selected').val()) !== -1) {
        $('.__variable').removeClass('d-none').find('input').attr('required','')
    }else {
        $('.__variable').addClass('d-none').find('input').removeAttr('required')
    }
}
var submit_paiement = !1;
$('#paiement_form').submit(function (e) {
    e.preventDefault();
    if(!submit_paiement){
        let spinner = $(__spinner_element);
        let  btn =$('#paiement_form').find('.btn-info');
        btn.attr('disabled','').prepend(spinner)
        submit_paiement = !0;
        $.ajax({
            url:$('#paiement_form').attr('action'),
            method:'POST',
            data: $(this).serialize(),
            headers:{
                'X-CSRF-Token':__csrf_token
            },
            success: function (response) {
                btn.removeAttr('disabled');
                submit_paiement = 0;
                spinner.remove();
                toastr.success(response);
                table.ajax.reload();
                $('#paiement_form').trigger('reset');
                $('#paiement_form').closest('.modal').modal('hide')
                $('#method-input').val(null).trigger('change');

            },
            error: function (xhr, ajaxOptions, thrownError) {
                btn.removeAttr('disabled');
                submit_paiement = !1;
                spinner.remove();
                toastr.error(xhr.responseText);
            }
        })
    }
})
check()
$('.delete').click(function (){
    Swal.fire({
        title: "Est-vous sûr?",
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimez!",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-danger mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
        },
        didOpen: () => {
            $('.btn').blur()
        },
        preConfirm: async () => {
            Swal.showLoading();
            try {
                const [response] = await Promise.all([new Promise((resolve, reject) => {
                    $.ajax({
                        url: $(this).data('url'), method: 'DELETE', headers: {
                            'X-CSRF-TOKEN': __csrf_token
                        }, success: resolve, error: (_, jqXHR) => reject(_)
                    });
                })]);

                return response;
            } catch (jqXHR) {
                let errorMessage = "Une erreur s'est produite lors de la demande.";
                if(jqXHR.status !== undefined) {
                    if (jqXHR.status === 402){
                        errorMessage = jqXHR.responseText;
                    }
                    if (jqXHR.status === 404) {
                        errorMessage = "La ressource n'a pas été trouvée.";
                    }
                    if (jqXHR.status === 403) {
                        errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                    }
                }
                Swal.fire({
                    title: 'Erreur',
                    text: errorMessage,
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });

                throw jqXHR;
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            if (result.value) {
                Swal.fire({
                    title: 'Succès',
                    text: result.value,
                    icon: 'success',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-success mx-2',
                    },
                }).then(result => {
                    location.assign(window.origin+'/tresorerie/comptes');
                });
            } else {
                Swal.fire({
                    title: 'Erreur',
                    text: "Une erreur s'est produite lors de la demande.",
                    icon: 'error',
                    buttonsStyling: false,
                    confirmButtonText: 'OK',
                    customClass: {
                        confirmButton: 'btn btn-soft-danger mx-2',
                    },
                });
            }
        }
    })
})
$('.datupickeru').datepicker({
    autoclose: true,
    language: "fr",
    changeYear: true,
    showButtonPanel: true,
    format: "dd/mm/yyyy",
})


$(document).ready(function () {
    table = $(__dataTable_id).DataTable({
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
            beforeSend : function (){
                $('#numbers-container').append('<div class="w-100  position-absolute top-0 d-flex align-items-center justify-content-center start-0 h-100" style="backdrop-filter: blur(8px)" >'+__spinner_element_lg+'</div>')
            },
            data: function (d) {
                if (typeof __dataTable_filter_inputs_id === 'object') {
                    for (const key in __dataTable_filter_inputs_id) {
                        d[key] = $(__dataTable_filter_inputs_id[key]).val();
                    }
                }
                d = __datatable_ajax_callback(d)
            },
            dataSrc:function (response){
                $('#numbers-container').html(response['numbers'])
                return response['data'];
            }
        },
        columns: __dataTable_columns,
        orderCellsTop: true,
        order: [[sortby, 'desc']],
        pageLength: 25,
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
})
