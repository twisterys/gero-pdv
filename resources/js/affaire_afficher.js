
// Initialisation de Select2 pour le sélecteur de documents
$('#documents-select').select2({
    width: "100%",
    placeholder: "Sélectionnez un document",
    minimumInputLength: 3, // Longueur minimale des caractères pour lancer la recherche
    dropdownParent: $("#affaireModal"),
    ajax: {
        // URL de votre serveur pour récupérer les données des documents
        url: __url_search,  // URL du serveur pour la recherche des documents
        cache: true, // Mettre en cache les résultats
        type: "GET",  // Type de la requête (GET ici)
        data: function (params) {
            // Paramètres envoyés à l'API pour la recherche de documents
            return {
                search: params.term,  // Terme de recherche
                type: $('#type-select').val()  // Type sélectionné (vente ou dépense)
            };
        },
        processResults: function (data) {
            // Traite les résultats de la réponse de l'API
            return {
                results: data,  // Retourne les résultats sous forme de tableau
            };
        },
    },
});

// Lors du clic sur le bouton "Attacher"
$(document).on('click', "#attach-btn", function () {
    // Préparation des données pour la requête AJAX
    let type = $('#type-select').val();  // Type sélectionné (vente ou dépense)
    let documentId = $('#documents-select').val();  // ID du document sélectionné dans le sélecteur

    // Vérification qu'un document a été sélectionné
    if (!documentId) {
        toastr.error('Veuillez sélectionner un document.');
        return;
    }

    // Préparation des données à envoyer avec la requête
    let data = { type: type };

    // Si le type est 'vente', ajouter l'ID de la vente
    if (type === 'vente') {
        data.vente_id = documentId;
    }
    // Si le type est 'depense', ajouter l'ID de la dépense
    else if (type === 'depense') {
        data.depense_id = documentId;
    }

    // Envoi de la requête AJAX pour attacher le document
    $.ajax({
        url: __url_attach,  // URL du serveur pour attacher le document
        method: 'POST',
        data: data,
        headers:{
            'X-CSRF-TOKEN' : __csrf_token
        },
        success: function(response) {
            // Si la réponse est "Document attché", afficher un message de succès
            if (response === 'Document attché') {
                toastr.success('Document attaché avec succès!');
                $('#affaireModal').modal('hide'); // Fermer la fenêtre modale en cas de succès
                location.reload();
            } else {
                // Si la réponse n'est pas celle attendue, afficher l'erreur
                toastr.error('Erreur: ' + response);
            }
        },
        error: function(xhr, status, error) {
            // Gérer les erreurs lors de la requête AJAX
            toastr.error('Une erreur est survenue : ' + error);
        }
    });
});

let validation_process = 0;

function loadData(button) {
    if (validation_process === 0) {
        validation_process = 1;
        let url = $(button).data('href');
        let btn = $(button); // Correctly reference the button

        btn.find('>i').addClass('d-none');
        let spinner = $(__spinner_element);
        btn.attr('disabled', true).prepend(spinner); // Disable the button and prepend the spinner

        $.ajax({
            url: url,
            type: 'GET',
            success: function (data) {
                // Assuming `data` contains the HTML content to be injected
                $('#offcanvasContent').html(data);
                btn.find('>i').removeClass('d-none');
                btn.removeAttr('disabled');
                validation_process = 0;
                spinner.remove();
            },
            error: function (xhr) {
                btn.find('>i').removeClass('d-none');
                btn.removeAttr('disabled');
                validation_process = 0;
                spinner.remove();
                if (xhr.status !== undefined) {
                    if (xhr.status === 403) {
                        toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                        return
                    }
                }
                toastr.error(xhr.responseText);
            }
        });
    }
}

$(document).on('click', '#supprimer-btn', function () {
    Swal.fire({
        title: "Est-vous sûr?",
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, supprimer!",
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
                if (jqXHR.status === 404) {
                    errorMessage = "La ressource n'a pas été trouvée.";
                }
                if (jqXHR.status === 403) {
                    errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
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
                    if (result.isConfirmed) {
                        window.location.href = __list_url;
                    }
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
});


//----------- Gantt

let gantt = new Gantt("#gantt", tasks ,{
    view_mode: 'Week', // Modes : 'Day', 'Week', 'Month'
    date_format: 'DD/MM/YYYY',
    container_height:'500',
    language:'fr',
    move_dependencies:false,
    infinite_padding:false,
    readonly:true,
    show_expected_progress:false,
    custom_popup_html: function (task) {
        // Format dates to 'DD/MM/YYYY'
        const startDate = new Date(task.start).toLocaleDateString('fr-FR');
        const endDate = new Date(task.end).toLocaleDateString('fr-FR');

        // Extract the start and end years
        const startYear = new Date(task.start).getFullYear();
        const endYear = new Date(task.end).getFullYear();

        // Return custom HTML for the popup
        return `
        <div class="details-container">
            <h5>${task.name}</h5>
            <p><strong>Début:</strong> ${startDate} (${startYear})</p>
            <p><strong>Fin:</strong> ${endDate} (${endYear})</p>
            <p><strong>Progression:</strong> ${task.progress}%</p>
        </div>
    `;
    }

});


//------------- DataTable --------------

const dataTable_lang = {
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
};

const __dataTable_filter_inputs_id = {
    affaire_id: '#affaire-input',
}
const __dataTable_filter = function (data) {
    d = __datatable_ajax_callback(data);
};
$(document).on('click', '.depense-tab', function () {
    $("#depense-table table").DataTable().destroy();
    const __dataTable_columns_depense = [
        {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
        {data: 'reference', name: 'reference'},
        {data: 'nom_depense', name: 'nom_depense'},
        {
            data: function(row) {
                return row.categorie ? row.categorie.nom : '';
            },
            name: 'categorie.nom'
        },
        {data: 'pour', name: 'pour'},
        {data: 'montant', name: 'montant'},
        {data: 'date_operation', name: 'date_operation'},
        {data: 'statut_paiement', name: 'statut_paiement'},

        {data: 'actions', name: 'actions', orderable: false,},
    ];
    $("#depense-table table").dataTable(
        {
            dom: 'lBrtip',
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Tous"]],
            processing: true,
            serverSide: true,
            responsive: true,
            language: dataTable_lang,
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
                url: url_depense,
                data: function (d) {
                    if (typeof __dataTable_filter_inputs_id === 'object') {
                        for (const key in __dataTable_filter_inputs_id) {
                            d[key] = $(__dataTable_filter_inputs_id[key]).val();
                        }
                    }
                    d.ignore_exercice = true;
                    d = __datatable_ajax_callback(d)
                }
            },
            columns: __dataTable_columns_depense,
            orderCellsTop: true,
            order: [[1, 'desc']],
            pageLength: 10,
        }
    )
})
$(document).on('click', '.ventes-tabs', function () {
    let nav_tab = $(this);
    $(nav_tab.attr('href') + ' table').DataTable().destroy();

    let url = vente_url + nav_tab.data('type') + '/liste';
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
            language: dataTable_lang,
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
                    d.ignore_exercice = true;
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
$('.ventes-tabs.active').trigger('click')
