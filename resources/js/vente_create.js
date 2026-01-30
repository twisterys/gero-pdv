import "./article_modal.js";
import "./historique_prix.js"

// Variables
var table_body = $("#table tbody");
var deleting = false;
var client_remise = 0;

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
// Event listener for the "Add Row" button
$("#addRowBtn,.add-row").on("click", function () {
    addRow();
});
$("#magasin-select").select2({
    width: "100%",
    placeholder: "Sélectionnez un magasin",
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
    $("#balises").select2({
        width: "100%",
        multiple: !0,
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
        language: "fr",
        changeYear: false,
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
    $("#client_select").select2({
        width: "100%",
        placeholder: "Sélectionnez un client",
        minimumInputLength: 3, // Specify the ajax options for loading the product data
        ajax: {
            // The URL of your server endpoint that returns the product data
            url: __client_select2_route,
            cache: true, // The type of request, GET or POST
            type: "GET",
            processResults: function (data) {
                return {
                    results: data.map(item => ({
                        id: item.id,
                        text: item.text,
                        remise_par_defaut: item.remise_par_defaut // Add the extra attribute here
                    }))
                };
            }
        },
    });
    $("#commercial_id")
        .select2({
            width: "65%",
            placeholder: "Sélectionnez un commercial",
            ajax: {
                // The URL of your server endpoint that returns the product data
                url: __comercial_select2_route,
                cache: true, // The type of request, GET or POST
                type: "GET",
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
            },
        })
        .on("select2:select", function (e) {
            $("#commission_par_defaut").val(
                e.params.data["commission_par_defaut"]
            );
        });
    tinymce.init({
        selector: "#tinymceEditor",
        oninit: "setPlainText",
        height: 400,
        menubar: !1,
        plugins: __tinymce_plugins,
        toolbar: __tinymce_toolbar,
        toolbar_mode: "floating",
        content_style:
            "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
    });
    tinymce.init({
        selector: "#i_note",
        height: 300,
        menubar: !0,
        oninit: "setPlainText",
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
        stop: function (e, ui) {
            orderLignes();
        },
    });
    $("#productTableBody, #productTableBody tr").disableSelection();
    //--------------- Quick add client ---------------
    var clmeknla_process = false;
    $(document).on("click", "#ajout-client", function () {
        if (!clmeknla_process) {
            clmeknla_process = !clmeknla_process;
            let btn = $(this);
            let btn_html = $(this).html();
            let spinner = $(__spinner_element).removeClass("me-2");
            btn.html(spinner).attr("disabled", "");
            $.ajax({
                url: btn.data("url"),
                success: function (response) {
                    $("#client-modal .modal-content").html(response);
                    $("#client-modal").modal("show");
                    $("#form-juridique-select").select2({
                        width: "100%",
                        placeholder: "Selectioner un type",
                    });
                    btn.html(btn_html).removeAttr("disabled");
                    clmeknla_process = !clmeknla_process;
                },
                error: function (xhr, status, error) {
                    toastr.error("Une erreur s'est produite");
                    btn.html(btn_html).removeAttr("disabled");
                    clmeknla_process = !clmeknla_process;
                },
            });
        }
    });
    var sKls_process = false;
    $(document).on("click", "#add-btn-client", function () {
        if (!sKls_process) {
            sKls_process = !sKls_process;
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
                    $("#client_select")
                        .append(
                            `<option selected value="${response.id}">${response.text}</option>`
                        )
                        .trigger("change");
                    btn.html(btn_html).removeAttr("disabled");
                    sKls_process = !sKls_process;
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
                    sKls_process = !sKls_process;
                },
            });
        }
    });
    $(document).on("change", "#form-juridique-select", function () {
        var dynamicLabel = document.getElementById("dynamic_label");
        if ($(this).val() === "sarl" || $(this).val() === "sa") {
            dynamicLabel.innerText = "Dénomination";
        } else {
            dynamicLabel.innerText = "Dénomination";
        }
    });
    //--------------- --------------- ---------------

    // --------------- Client remise par defaut ---------------
    $("#client_select").on("select2:select", function (e) {
        var selectedOption = e.params.data;
        client_remise = selectedOption.remise_par_defaut ?? 0;
        apply_client_remise();
    });


    insert_delete_btn();
    orderLignes();
    calculate_rows();
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
    newRow.find(".reduction_mode").val("pourcentage").trigger('change');
    newRow.find(".reduction").val(client_remise);
    table_body.append(newRow);
    orderLignes();
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
            .eq(6)
            .text(totalTTC.toFixed(3) + " MAD");
        total_ht += totalHT;
        total_tva += (ht - reducHT) * ("0." + tvaRate) * quantity;
        total_reduction += reducHT * quantity;
        total_ttc += totalTTC;
    });
    $("#total-ht-text").text(total_ht.toFixed(3) + " MAD");
    $("#total-reduction-text").text(total_reduction.toFixed(3) + " MAD");
    $("#total-tva-text").text(total_tva.toFixed(3) + " MAD");
    $("#total-ttc-text").text(total_ttc.toFixed(3) + " MAD");
}

function apply_client_remise() {
    table_body.find("tr").each(function () {
        let row = $(this);
        row.find(".reduction_mode").val("pourcentage").trigger('change');
        row.find(".reduction").val(client_remise)
    });
    calculate_rows();
}


let currentRow; // To keep track of the clicked row

// Function to update the button state based on the description
function updateButtonState(row) {
    const description = row.find('textarea.description-line').val();
    const addButton = row.find('.add-description');
    if (description) {
        addButton.removeClass('btn-soft-success').addClass('btn-soft-primary');
        addButton.html('<i class="fa fa-edit"></i> Modifier la description');
    } else {
        addButton.removeClass('btn-soft-primary').addClass('btn-soft-success');
        addButton.html('<i class="fa fa-plus"></i> Ajouter une description');
    }
}

// Check button state on page load
$('tr').each(function () {
    updateButtonState($(this));
});

// Open modal on button click
$(document).on('click', '.add-description', function () {
    currentRow = $(this).closest('tr'); // Get the row where the button was clicked
    const description = currentRow.find('textarea.description-line').val(); // Get existing description
    tinymce.get('tinymceEditor').setContent(description || ''); // Populate TinyMCE editor with description
    $('#descriptionModal').modal('show'); // Open modal
});

// Save description on modal save button click
$('#saveDescription').click(function () {
    const updatedDescription = tinymce.get('tinymceEditor').getContent(); // Get content from TinyMCE editor
    currentRow.find('textarea.description-line').val(updatedDescription); // Set the updated content in the hidden textarea
    currentRow.find('.description').html(updatedDescription); // Optionally display it in the row
    updateButtonState(currentRow); // Update the button state
    $('#descriptionModal').modal('hide'); // Close modal
});
