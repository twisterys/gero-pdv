import "../article_modal.js";

var deleting = false;

$("#magasin-select").select2({
    width: "100%",
    placeholder: "Sélectionnez un magasin",
});

$(document).on("click", ".delete-row", function () {
    delete_row($(this));
});

$("#addRowBtn,.add-row").on("click", function () {
    let name = $(this).data("name");
    let table_body = $(this).closest(".tt").find("tbody");
    addRow(name, table_body);
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
let currentRow;





function orderLignes(table_body) {
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

function getRowSortant() {
    return __row.replace(/\[name]/g, 'lignes_sortant');

}

function getRowEntrant() {
    return __row.replace(/\[name]/g, 'lignes_entrant');
}

function addRow(type, table_body) {
    let newRow;
    if (type === "sortant") {
        newRow = getRowSortant();
    } else {
        newRow = getRowEntrant();
    }
    table_body.append(newRow);
    orderLignes(table_body);
    insert_delete_btn(table_body)

}

function insert_delete_btn(table_body) {
    if (table_body.find("tr").length > 1) {
        table_body.find("tr").each(function (index) {
            $(this)
                .find("td:last-child")
                .html(
                    '<button type="button" class="btn btn-sm btn-soft-danger delete-row" ><i class="fa fa-trash-alt" ></i></button>'
                );
        });
    } else {
        $(".delete-row, .drag-btn").remove();
    }
}

function delete_row(btn) {
    if (!deleting) {
        let table_body = btn.closest("tbody");
        deleting = true;
        btn.closest("tr").remove();
        insert_delete_btn(table_body);
        deleting = false;
        orderLignes(table_body);
    }
}

document.addEventListener("DOMContentLoaded", function () {
    $('.tt').each(function () {
        let table_body = $(this).find("tbody");
        orderLignes(table_body);
        insert_delete_btn(table_body);

    })

    $("#date").datepicker({
        autoclose: true,
        language: "fr",
        changeYear: false,
        showButtonPanel: true,
        format: "dd/mm/yyyy",
    });
})
