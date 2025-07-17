import "./article_modal.js";

// Variables
var table_body = $("#table tbody");
var deleting = false;

// Event Listners
$(document).on("click", ".delete-row", function () {
    delete_row($(this));
});
$(document).on(
    "input",
    ".taxe, .prix_ht, .quantite, .reduction, .reduction_mode",
    function () {
        calculate_rows();
    }
);
$("#default-switcher").change(function () {
    checkDétails();
});
// Event listener for the "Add Row" button
$("#addRowBtn,.add-row").on("click", function () {
    addRow();
});
$("#commercial_id").on("change", function () {
    // Get the selected option
    var selectedOption = $(this).find(":selected");

    // Update the value of the objectif input with the data-commission attribute
    $("#commission_par_defaut").val(selectedOption.data("commission") || "");
    // Trigger change event on objectif input (useful if you have other logic listening for this input)
    $("#commission_par_defaut").trigger("change");
});
// Components init
document.addEventListener("DOMContentLoaded", function () {
    checkDétails();
    $("#balises-select").select2({
        width: "100%",
        placeholder: "Sélectionnez une balise",
        minimumInputLength: 1, // Specify the ajax options for loading the product data
        ajax: {
            // The URL of your server endpoint that returns the product data
            url: __balises_select2_route,
            cache: true, // The type of request, GET or POST
            type: "GET",
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data,
                };
            },
        },
    });
    $("#date_emission").datepicker({
        autoclose: true,
        changeYear: false,
        language: "fr",
        showButtonPanel: true,
        format: "dd/mm/yyyy",
        startDate: __exercice_start_date,
        endDate: __exercice_end_date,
    });
    $("#date_expiration").datepicker({
        autoclose: true,
        language: "fr",
        changeYear: true,
        showButtonPanel: true,
        format: "dd/mm/yyyy",
        startDate: __exercice_start_date,
    });
    $("#fournisseur_select").select2({
        width: "100%",
        placeholder: "Sélectionnez un fournisseur",
        minimumInputLength: 3, // Specify the ajax options for loading the product data
        ajax: {
            // The URL of your server endpoint that returns the product data
            url: __fournisseur_select2_route,
            cache: true, // The type of request, GET or POST
            type: "GET",
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data,
                };
            },
        },
    });
    $("#magasin-select").select2({
        width: "100%",
        placeholder: "Sélectionnez un magasin",
    });
    $("#fichier_document").dropify({
        messages: {
            default: "Glissez-déposez un fichier ici ou cliquez",
            replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
            remove: "Supprimer",
            error: "Désolé, le fichier trop volumineux",
        },
    });
    tinymce.init({
        selector: ".summernote",
        height: 130,
        menubar: !1,
        plugins: __tinymce_plugins,
        toolbar: __tinymce_toolbar,
        toolbar_mode: "floating",
        content_style:
            "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
    });
    tinymce.init({
        selector: "#i_note",
        height: 250,
        menubar: !0,
        plugins: __tinymce_plugins,
        toolbar: __tinymce_toolbar,
        toolbar_mode: "floating",
        content_style:
            "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
    });
    // Add new classes to the element
    $(".row_select2").select2({
        minimumResultsForSearch: -1,
    });
    $("#productTableBody").sortable({
        placeholder: "ui-state-highlight",
        handle: "td:last-child .drag-btn",
        start: function (e, ui) {
            $(this)
                .find(".summernote")
                .each(function () {
                    tinyMCE.triggerSave();
                });
        },
        stop: function (e, ui) {
            $(this)
                .find(".summernote")
                .each(function () {
                    tinyMCE.execCommand(
                        "mceRemoveEditor",
                        false,
                        $(this).attr("id")
                    );
                    tinyMCE.execCommand(
                        "mceAddEditor",
                        false,
                        $(this).attr("id")
                    );
                });
            orderLignes();
        },
    });
    $("#productTableBody, #productTableBody tr").disableSelection();
    insert_delete_btn();
    orderLignes();
    calculate_rows();
});
var flmnjjw_process = false;

$(document).on("click", "#ajout-fournisseur", function () {
    if (!flmnjjw_process) {
        flmnjjw_process = !flmnjjw_process;
        let btn = $(this);
        let btn_html = $(this).html();
        let spinner = $(__spinner_element).removeClass("me-2");
        btn.html(spinner).attr("disabled", "");
        $.ajax({
            url: btn.data("url"),
            success: function (response) {
                $("#fournisseur-modal .modal-content").html(response);
                $("#fournisseur-modal").modal("show");
                $("#form-juridique-select").select2({
                    width: "100%",
                    placeholder: "Selectioner un type",
                });
                $("#form-juridique-select").trigger("change");
                btn.html(btn_html).removeAttr("disabled");
                flmnjjw_process = !flmnjjw_process;
            },
            error: function (xhr, status, error) {
                toastr.error("Une erreur s'est produite");
                btn.html(btn_html).removeAttr("disabled");
                flmnjjw_process = !flmnjjw_process;
            },
        });
    }
});
var fljqw_process = false;
$(document).on("click", "#add-btn-fournisseur", function () {
    if (!fljqw_process) {
        fljqw_process = !fljqw_process;
        let btn = $(this);
        let btn_html = $(this).html();
        let spinner = $(__spinner_element).removeClass("me-2");
        btn.html(spinner).attr("disabled", "");
        let form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            method: "POST",
            headers: {
                "X-CSRF-Token": __csrf_token,
            },
            data: form.serialize(),
            success: (response) => {
                $("#fournisseur_select")
                    .append(
                        `<option selected value="${response.id}">${response.text}</option>`
                    )
                    .trigger("change");
                btn.html(btn_html).removeAttr("disabled");
                fljqw_process = !fljqw_process;
                form.closest(".modal").modal("hide");
            },
            error: function (xhr, status, error) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (const [key, value] of Object.entries(errors)) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key)
                            .siblings(".invalid-feedback")
                            .html(value);
                    }
                } else {
                    toastr.error(xhr.responseText);
                }
                btn.html(btn_html).removeAttr("disabled");
                fljqw_process = !fljqw_process;
            },
        });
    }
});

$(document).on("change", "#form-juridique-select", function () {
    var dynamicLabel = document.getElementById("dynamic_label");
    var selectedId = $("#form-juridique-select option:selected").attr("id");
    if (selectedId === "PP" || selectedId === "AE") {
        dynamicLabel.innerText = "Dénomination";
    } else {
        dynamicLabel.innerText = "Dénomination";
    }
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
        calculate_rows();
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
    newRow.find(".row_select2").select2({
        minimumResultsForSearch: -1,
    });
    table_body.append(newRow);
    orderLignes();
    tinymce.init({
        selector: "#table tbody tr:last-child .summernote",
        height: 130,
        menubar: !1,
        plugins: __tinymce_plugins,
        toolbar: __tinymce_toolbar,
        toolbar_mode: "floating",
        content_style:
            "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
    });
    newRow.hide();
    table_body.find("tr:last-child").fadeIn("slow");
    insert_delete_btn();
    calculate_rows();
}

/**
 * @name insert_delete_btn
 * @description This function inserts or delete the row delete button for all the rows
 * @return void
 */
function insert_delete_btn() {
    table_body.find("tr").each(function (index) {
        $(this)
            .find("td:last-child")
            .html(
                '<button type="button" class="btn btn-sm btn-soft-danger delete-row" ><i class="fa fa-trash-alt" ></i></button> <a class=" mt-2 btn btn-sm drag-btn btn-soft-purple"><i class="fa fa-arrows-alt"></i></a>'
            );
    });
}
/**
 * @name calculateReduction
 * @description This function is just calculating the reduction of single row
 * @return number
 */
function calculateReduction(row) {
    let reducType = row.find(".reduction_mode").val();
    if (reducType === "fixe") {
        return (
            Math.round((+row.find(".reduction").val() + Number.EPSILON) * 100) /
            100
        );
    } else {
        return (
            Math.round(
                (+row.find(".prix_ht").val() *
                    (+row.find(".reduction").val() / 100) +
                    Number.EPSILON) *
                    100
            ) / 100
        );
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
function calculate_rows() {
    let total_ht = 0;
    let total_reduction = 0;
    let total_tva = 0;
    let total_ttc = 0;
    table_body.find("tr").each(function () {
        let row = $(this);
        let ht = +row.find(".prix_ht").val();
        let quantity = +row.find(".quantite").val();
        let tvaRate = +row.find(".taxe").val();
        let reducHT = calculateReduction($(this));
        let totalHT = ht * quantity;
        let totalTTC = (ht - reducHT) * ("1." + tvaRate) * quantity;
        row.children("td")
            .eq(5)
            .text(totalTTC.toFixed(2) + " MAD");
        total_ht += totalHT;
        total_tva += (ht - reducHT) * ("0." + tvaRate) * quantity;
        total_reduction += reducHT * quantity;
        total_ttc += totalTTC;
    });
    $("#total-ht-text").text(total_ht.toFixed(2) + " MAD");
    $("#total-reduction-text").text(total_reduction.toFixed(2) + " MAD");
    $("#total-tva-text").text(total_tva.toFixed(2) + " MAD");
    $("#total-ttc-text").text(total_ttc.toFixed(2) + " MAD");
}

function checkDétails() {
    if ($("#default-switcher").is(":checked")) {
        $("#collapse").collapse("show");
        $("#collapse *").removeAttr("disabled", "");
        $("#total_ht_input,#total_ttc_input")
            .parent()
            .parent()
            .addClass("d-none")
            .find("*")
            .attr("disabled", "");
    } else {
        $("#collapse").collapse("hide");
        $("#collapse *").attr("disabled", "");
        $("#total_ht_input,#total_ttc_input")
            .parent()
            .parent()
            .removeClass("d-none")
            .find("*")
            .removeAttr("disabled");
    }
}
