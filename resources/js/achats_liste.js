$(".filter-btn").click((e) => {
    $(".switch-filter").toggleClass("d-none");
});
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
$("#fournisseur-select").select2({
    width: "100%",
    placeholder: {
        id: "",
        text: "Tous",
    },
    allowClear: !0,
    ajax: {
        url: __fournisseur_select2_route,
        dataType: "json",
        delay: 250,
        data: function (params) {
            return {
                term: params.term,
            };
        },
        processResults: function (data) {
            return {
                results: data,
            };
        },
        cache: false,
    },
    minimumInputLength: 3,
});
$("#statut-select,#statut-paiement-select").select2({
    width: "100%",
    placeholder: {
        id: "",
        text: "Tous",
    },
    allowClear: !0,
    minimumResultsForSearch: -1,
    selectOnClose: false,
});
$("#created_by-select").select2({
    width: "100%",
});
$(".datepicker").daterangepicker({
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
            "Décembre",
        ],
        firstDay: 1,
    },
    startDate: __datepicker_start_date,
    endDate: __datepicker_end_date,
    minDate: __datepicker_min_date,
    maxDate: __datepicker_max_date,
});

$(".datepicker-expired").daterangepicker({
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
            "Décembre",
        ],
        firstDay: 1,
    },
    startDate: __datepicker_start_date,
    endDate: __datepicker_end_date_expired,
    minDate: __datepicker_min_date,
    maxDate: __datepicker_max_date_expired,
});

var conversion_modal_process = !1;
$(document).on('click', '#clone-btn', function (e) {
    if (!conversion_modal_process) {
        conversion_modal_process = !0;
        let spinner = $(__spinner_element);
        $(this).prepend(spinner).find('i').addClass('d-none');
        $.ajax({
            url: $(this).data('href'),
            method: 'GET',
            success: function (response) {
                conversion_modal_process = !1;
                spinner.parent().find('.d-none').removeClass('d-none')
                spinner.remove();
                $('#clone-modal .modal-content').html(response);
                $('#clone-modal').modal('show')
            },
            error: function (xhr, ajaxOptions, thrownError) {
                conversion_modal_process = !1;
                spinner.parent().find('.d-none').removeClass('d-none')
                spinner.remove();
                if (xhr.status != undefined) {
                    if (xhr.status === 403) {
                        toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                        return
                    }
                }
                toastr.error(xhr.responseText);
            }
        })
    }
});

//
// $(document).on('click', '.clone-btn', function (e) {
//     e.preventDefault()
//     let form = $(this).closest('form');
//     Swal.fire({
//         title: "Est-vous sûr?",
//         text: "voulez-vous cloner ce document ?",
//         icon: "warning",
//         showCancelButton: true,
//         confirmButtonText: "Oui, cloner!",
//         buttonsStyling: false,
//         customClass: {
//             confirmButton: 'btn btn-primary mx-2',
//             cancelButton: 'btn btn-light mx-2',
//         },
//         didOpen: () => {
//             $('.btn').blur()
//         },
//         preConfirm: async () => {
//             form.submit()
//         }
//     })
// });
