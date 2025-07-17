
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
            // Transforms the top-level key of the response object from 'items' to 'results'
            return {
                results: data,
            };
        },
    },
});

// Components init
document.addEventListener("DOMContentLoaded", function () {
    $("#date_debut,#date_fin").datepicker({
        autoclose: true,
        language: "fr",
        changeYear: false,
        showButtonPanel: true,
        format: "dd/mm/yyyy",
        startDate: __exercice_start_date,
        endDate: __exercice_end_date,
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
    newRow.find(".jalon_date").datepicker({
        autoclose: true,
        language: "fr",
        changeYear: false,
        showButtonPanel: true,
        format: "dd/mm/yyyy",
    });
    newRow.hide();
    table_body.find("tr:last-child").fadeIn("slow");
    insert_delete_btn();
}

tinymce.init({
    selector: ".summernote",
    oninit: "setPlainText",
    height: 130,
    menubar: !1,
    plugins: __tinymce_plugins,
    toolbar: __tinymce_toolbar,
    toolbar_mode: "floating",
    content_style:
        "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
});

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
                    '<button type="button" class="btn btn-sm btn-soft-danger delete-row" ><i class="fa fa-trash-alt" ></i></button>'
                );
        });

}
/**
 * @name calculateReduction
 * @description This function is just calculating the reduction of single row
 * @return number
 */

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

$(".jalon_date").datepicker({
    autoclose: true,
    language: "fr",
    changeYear: false,
    showButtonPanel: true,
    format: "dd/mm/yyyy",
})
