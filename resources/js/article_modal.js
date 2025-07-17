// --------------- Modal Article ---------------
let artclfeld_process = false;
$(document).on("click", ".article_btn", function () {
    if (!artclfeld_process) {
        artclfeld_process = !artclfeld_process;
        $(".__row_selected").removeClass("__row_selected");
        $(this).closest("tr").addClass("__row_selected");
        let btn = $(this);
        let btn_html = $(this).html();
        let spinner = $(__spinner_element).removeClass("me-2");
        btn.html(spinner).attr("disabled", "");
        $.ajax({
            url: __articles_modal_route + "/" + $("#magasin-select").val(),
            method: "GET",
            success: function (response) {
                $("#article-modal #article-search-content").html(response);
                $("#article-modal").modal("show");
                $("#modal-magasin-select").select2({
                    width: "100%",
                    minimumResultsForSearch: -1,
                });
                btn.html(btn_html).removeAttr("disabled");
                artclfeld_process = !artclfeld_process;
            },
            error: function (xhr, status, error) {
                let errorMessage = "Une erreur s'est produite"; // Message d'erreur générique par défaut

                if (xhr.status === 404 && xhr.responseText) {
                    errorMessage = xhr.responseText; // Utilise le message d'erreur personnalisé si disponible
                }

                toastr.error(errorMessage); // Affiche le message d'erreur
                btn.html(btn_html).removeAttr("disabled");
                artclfeld_process = !artclfeld_process;
            },
        });
    }
});

var delayTimer;
function doSearch() {
    clearTimeout(delayTimer);
    delayTimer = setTimeout(function () {
        $("#__result").html(
            '<div class="w-100 text-center" style="min-height: 200px">' +
                __spinner_element_lg +
                "</div>"
        );
        $.ajax({
            url: $("#article-search-form").attr("action"),
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": __csrf_token,
            },
            data: $("#article-search-form").serialize(),
            success: function (response) {
                $("#__result").html(response);
            },
        });
    }, 200);
}
$(document).on("click", ".article-card", function () {
    $(".article-card").removeClass("selected");
    $(this).addClass("selected");
});
$(document).on("input", "#article_modal_search_input", function () {
    doSearch();
});
$(document).on("change", "#modal-magasin-select", function () {
    doSearch();
});
$(document).on("click", "#confirm_article", function () {
    if ($(".article-card.selected").length > 0) {
        let selected = $(".article-card.selected");
        console.log(selected.data());
        $(".__row_selected").find(".prix_ht ").val(selected.data("ht"));
        $(".__row_selected")
            .find(".taxe")
            .val(selected.data("taxe"))
            .trigger("change");
        $(".__row_selected")
            .find(".unite")
            .val(selected.data("unite"))
            .trigger("change");
        $(".__row_selected")
            .find("td:first-of-type input")
            .val(selected.data("nom"));
        $(".__row_selected")
            .find("td:first-of-type .input-group")
            .find(".input-group-text")
            .remove();
        $(".__row_selected")
            .find("td:first-of-type .input-group")
            .prepend(
                `<span class="input-group-text">${selected.data(
                    "reference"
                )}</span>`
            );
        $(".__row_selected").find(".article_id").val(selected.data("id"));
        $(".__row_selected")
            .find(".article_reference")
            .val(selected.data("reference"));
        $(".__row_selected").find(".quantite").val(1).trigger("input");
        $(".__row_selected").find(".quantite_stock").val(selected.data("quantite-stock")).trigger("input");

        $("#article-modal").modal("hide");
    }
});

let __yykkfpw_process = false;
$(document).on("click", ".add-article-btn", function () {
    if (!__yykkfpw_process) {
        __yykkfpw_process = !__yykkfpw_process;
        let btn = $(this);
        let btn_html = $(this).html();
        let spinner = $(__spinner_element).removeClass("me-2");
        btn.html(spinner).attr("disabled", "");
        $.ajax({
            url: btn.data("url"),
            success: function (response) {
                $("#article-modal #article-add-content").html(response);
                $(
                    "#article-modal #unity-select,#article-modal #tax-select"
                ).select2();
                $("#article-modal .modal-dialog").css(
                    "transform",
                    "rotateY(180deg)"
                );
                btn.html(btn_html).removeAttr("disabled");
                __yykkfpw_process = !__yykkfpw_process;
            },
            error: function (xhr, status, error) {
                toastr.error("Une erreur s'est produite");
                btn.html(btn_html).removeAttr("disabled");
                __yykkfpw_process = !__yykkfpw_process;
            },
        });
    }
});
var __apclnsiwx_process = false;
$(document).on("click", "#save-btn-article", function () {
    if (!__apclnsiwx_process) {
        __apclnsiwx_process = !__apclnsiwx_process;
        let btn = $(this);
        let btn_html = btn.html();
        btn.html(__spinner_element).attr("disabled", "");
        let form = $(this).closest("form");
        $.ajax({
            url: form.attr("action"),
            method: "POST",
            headers: {
                "X-CSRF-Token": __csrf_token,
            },
            data: form.serialize(),
            success: (response) => {
                let selected = $(".article-card.selected");
                if ($("#fournisseur_select").length > 0) {
                    $(".__row_selected")
                        .find(".prix_ht ")
                        .val(response.prix_achat ?? 0);
                } else {
                    $(".__row_selected")
                        .find(".prix_ht ")
                        .val(response.prix_vente ?? 0);
                }
                $(".__row_selected")
                    .find(".taxe")
                    .val(response.taxe ?? 0)
                    .trigger("change");
                $(".__row_selected")
                    .find("td:first-of-type input")
                    .val(response.designation);
                $(".__row_selected")
                    .find("td:first-of-type .input-group")
                    .find(".input-group-text")
                    .remove();
                $(".__row_selected")
                    .find("td:first-of-type .input-group")
                    .prepend(
                        `<span class="input-group-text">${response.reference}</span>`
                    );
                $(".__row_selected").find(".article_id").val(response.id);
                $(".__row_selected")
                    .find(".article_reference")
                    .val(response.reference);
                $(".__row_selected").find(".quantite").val(1).trigger("input");
                $("#article-modal").modal("hide");
                btn.html(btn_html).removeAttr("disabled");
                __apclnsiwx_process = !__apclnsiwx_process;
            },
            error: (xhr) => {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (const [key, value] of Object.entries(errors)) {
                        $('input[name="' + key + '"]').addClass("is-invalid");
                        $('input[name="' + key + '"]')
                            .siblings(".invalid-feedback")
                            .html(value);
                    }
                }
                toastr.error("Erreur");
                btn.html(btn_html).removeAttr("disabled");
                __apclnsiwx_process = !__apclnsiwx_process;
            },
        });
    }
});
$(document).on("hidden.bs.modal", "#article-modal", function () {
    $("#article-modal .modal-dialog").css("transform", "rotate(0deg)");
});
$(document).on("click", "#return_to_article", function () {
    $("#article-modal .modal-dialog").css("transform", "rotateY(0)");
});


$(document).on("submit","#article-search-form",function (event){
    event.preventDefault();
})
