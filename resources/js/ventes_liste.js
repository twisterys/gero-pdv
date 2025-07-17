$('.filter-btn').click(e => {
    $('.switch-filter').toggleClass('d-none')
})
$('#balises-select').select2({
    width: '100%',
    placeholder: 'Sélectionnez une balise',
    minimumInputLength: 1, // Specify the ajax options for loading the product data
    ajax: {
        // The URL of your server endpoint that returns the product data
        url: __balises_select2_route, cache: true, // The type of request, GET or POST
        type: 'GET', processResults: function (data) {
            // Transforms the top-level key of the response object from 'items' to 'results'
            return {
                results: data
            };
        },
    }
});
$('#client-select').select2({
    width: '100%',
    placeholder: {
        id: '',
        text: 'Tous'
    },
    allowClear: !0,
    ajax: {
        url: __client_select2_route,
        dataType: 'json',
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
    minimumInputLength: 3
})
$('#commercial-select').select2({
    width: '100%',
    placeholder: {
        id: '',
        text: 'Tous'
    },
    allowClear: !0,
    ajax: {
        url: __comercial_select2_route,
        dataType: 'json',
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
    minimumInputLength: 3
})
$('#livraison-select').select2({
    width: '100%',
    placeholder: {
        id: '',
        text: 'Tous'
    },
    allowClear: !0,
    ajax: {
        url: __livraison_select2_route,
        dataType: 'json',
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
    minimumInputLength: 0
})
$('#statut-select,#statut-paiement-select,#statut-controle-select').select2({
    width: '100%',
    placeholder: {
        id: '',
        text: 'Tous'
    },
    allowClear: !0,
    minimumResultsForSearch: -1,
    selectOnClose: false
})
$('#created_by-select').select2({
    width: '100%',
})
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
$(document).on('change', '.row-select', function () {

    let selected_rows = getSelectedRows();
    let selected_clients =selected_rows.map(e=>{
        return table.row($('.row-select[value="' + e + '"]').closest('tr')).data().client.id;
    });
    if (selected_rows.length > 0) {
        if ($('#convert-all').length === 0){
            $('.page-title-right').append('<button data-href="'+__multiconvert_route+'" id="convert-all" class="btn  btn-soft-purple w-auto "><i class="fa fa-sync"></i> Convertir</button>');
        }
        if(table.row($(this).closest('tr')).data().statut === "Brouillon" || new Set(selected_clients).size > 1){
            $('#convert-all').attr('disabled', '');
        }else {
            $('#convert-all').removeAttr('disabled')
        }
    } else {
        $('#convert-all').remove()
    }
})
var conversion_modal_process = !1;

$(document).on('click', '#convert-all', function () {
    if (!conversion_modal_process) {
        conversion_modal_process = !0;
        let spinner = $(__spinner_element);
        $(this).prepend(spinner).find('i').addClass('d-none');
        $.ajax({
            url: $(this).data('href'),
            method: 'post',
            headers:{
                'X-CSRF-TOKEN':__csrf_token
            },
            data: {
                ids:getSelectedRows()
            },
            success: function (response) {
                conversion_modal_process = !1;
                spinner.parent().find('.d-none').removeClass('d-none')
                spinner.remove();
                $('#conversion-modal .modal-content').html(response);
                $('#conversion-modal').modal('show')
            },
            error: function (xhr, ajaxOptions, thrownError) {
                conversion_modal_process = !1;
                spinner.parent().find('.d-none').removeClass('d-none')
                spinner.remove();
                if (xhr.status === 403) {
                    toastr.info(xhr.responseText);
                } else {
                    toastr.error(xhr.responseText);
                }
            }
        })
    }
})
