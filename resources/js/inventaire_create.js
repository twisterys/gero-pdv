import "./article_modal.js";
import "./historique_prix.js"

// Variables
var table_body = $("#table tbody");
var deleting = false;

// Event Listners
$(document).on("click", ".delete-row", function () {
    delete_row($(this));
});

// Event listener for the "Add Row" button
$("#addRowBtn,.add-row").on("click", function () {
    addRow();
});
$("#magasin-select").on("change", function () {
    const previousValue = $(this).data("previous");
    const currentSelect = $(this);

    // Vérifie s'il y a des lignes non vides
    const hasNonEmptyRows = $("#table tbody tr").filter(function () {
        return $(this).find("input, select, textarea").filter(function () {
            return $(this).val().trim() !== "";
        }).length > 0;
    }).length > 0;

    if (hasNonEmptyRows) {
        Swal.fire({
            title: "Changer de magasin?",
            text: "Cela supprimera toutes les lignes actuelles de la table.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Oui, continuer",
            cancelButtonText: "Annuler",
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-soft-danger mx-2',
                cancelButton: 'btn btn-soft-secondary mx-2',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                // Supprime toutes les lignes, mais garde une ligne vide
                table_body.empty();

                // Ajouter une ligne vide
                let emptyRow = getRow(); // Utiliser la fonction existante `getRow` pour obtenir une ligne vide
                table_body.append(emptyRow);
                orderLignes(); // Réindexer les lignes
            } else {
                // Réinitialiser le menu select à sa valeur précédente
                currentSelect.val(previousValue);
            }
        });
    } else {
        // Pas de lignes non vides, on permet le changement de magasin sans alerte
        currentSelect.val(currentSelect.val());
    }
});

// Stocker la valeur précédente du menu select
$("#magasin-select").on("focus", function () {
    $(this).data("previous", $(this).val());
});



// Components init
document.addEventListener("DOMContentLoaded", function () {

    $("#productTableBody").sortable({
        placeholder: "ui-state-highlight",
        handle: "td:last-child .drag-btn",
    });
    $("#productTableBody, #productTableBody tr").disableSelection();

    insert_delete_btn();
    orderLignes();
});

// Functions

/**
 * @name delete_row
 * @description This function delete the row and do the calculations necessary
 * @param btn
 * @return void
 */
function delete_row(btn) {
    if (!deleting) {
        deleting = true;
        btn.closest("tr").remove();
        insert_delete_btn();
        deleting = false;
        orderLignes();
    }
}

/**
 * @name getRow
 * @description This function returns new row (__row should be declared in blade)
 * @returns object
 */
function getRow() {
    return $(__row);
}

/**
 * @name addRow
 * @description This function inserts new row inside table and initialize all components inside the row
 * @return void
 */
function addRow() {
    let newRow = getRow();
    table_body.append(newRow);
    orderLignes();
    newRow.hide();
    table_body.find("tr:last-child").fadeIn("slow");
    insert_delete_btn();
}

/**
 * @name insert_delete_btn
 * @description This function inserts or delete the row delete button for all the rows
 * @return void
 */
function insert_delete_btn() {
    if (table_body.find("tr").length > 1) {
        table_body.find("tr").each(function (index) {
            $(this)
                .find("td:last-child")
                .html(
                    '<button type="button" class="btn btn-sm btn-soft-danger delete-row" ><i class="fa fa-trash-alt" ></i></button> <a class=" mt-2 btn btn-sm drag-btn btn-soft-purple"><i class="fa fa-arrows-alt"></i></a>'
                );
        });
    } else {
        $(".delete-row, .drag-btn").remove();
    }
}

/**
 * @name calculateReduction
 * @description This function is important and used a lot it's the one
 * @return void
 */
function orderLignes() {
    table_body.find("tr").each(function (index) {
        $(this)
            .find("*[name]")
            .each(function () {
                if ($(this).attr("name").includes("lignes")) {
                    let name = $(this).attr("name").split("[");
                    name[1] = index + "]";
                    $(this).attr("name", name.join("["));
                }
            });
    });
}



