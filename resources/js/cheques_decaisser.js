$(".filter-btn").click((e) => {
    $(".switch-filter").toggleClass("d-none");
});
function handleSaveFormSubmit(e) {
    e.preventDefault();
    let form = $(this);
    let btn = form.find('button[type="submit"]');
    let html = btn.html();
    btn.html(__spinner_element);

    saveCheque(form, btn, html);
}

function handleUpdateFormSubmit(e) {
    e.preventDefault();
    let form = $(this);
    let btn = form.find('button[type="submit"]');
    let html = btn.html();
    btn.html(__spinner_element);

    updateCheque(form, btn, html);
}

function saveCheque(form, btn, html) {
    $.ajax({
        url: window.origin + '/cheques/sauvegarder/decaissement',
        method: 'post',
        data: form.serialize(),
        headers: {
            'X-CSRF-TOKEN': __csrf_token
        },
        success: function (response) {
            handleSuccess(response, form, btn, html, '#saveChequeModal');
        },
        error: function (xhr) {
            handleError(xhr, btn, html);
        }
    }).then(() => {
        table.ajax.reload();
    });
}

function updateCheque(form, btn, html) {
    $.ajax({
        url: form.attr('action'),
        method: 'put',
        data: form.serialize(),
        headers: {
            'X-CSRF-TOKEN': __csrf_token
        },
        success: function (response) {
            handleSuccess(response, form, btn, html, '#updateChequeModal');
        },
        error: function (xhr) {
            handleError(xhr, btn, html);
        }
    }).then(() => {
        table.ajax.reload();
    });
}

function handleSuccess(response, form, btn, html, modalId) {
    btn.html(html);
    form.trigger('reset');
    $(modalId).modal('hide');
    toastr.success(response);
}

function handleError(xhr, btn, html) {
    btn.html(html);
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
}

$(document).on('submit', '#saveChequeModalForm', handleSaveFormSubmit);
$(document).on('submit', '#updateChequeModalForm', handleUpdateFormSubmit);

$('#fournisseur_id').select2({
    width: '100%',
    placeholder: {
        id: '',
        text: 'Tous'
    },
    dropdownParent: $('#saveChequeModalForm'),
    allowClear: !0,
    ajax: {
        url: __fournisseur_select2_route,
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
});

$('#fournisseur_filters').select2({
    width: '100%',
    placeholder: {
        id: '',
        text: 'Tous'
    },
    allowClear: !0,
    ajax: {
        url: __fournisseur_select2_route,
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
});


$(document).on('click', '.cheque-edit', function () {
    let html = $(this).html();
    $(this).attr('disabled', '').html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>')
    let url = $(this).data('url');
    let target = '#' + $(this).data('target');
    $.ajax({
        url: url, method: 'GET', success: response => {
            $(this).removeAttr('disabled').html(html)
            $(target).find('.modal-content').html(response);
            $(target).modal('show');
            $(target).find('#update_fournisseur_id').select2({
                width: '100%',
                placeholder: {
                    id: '',
                    text: 'Tous'
                },
                dropdownParent: $('#updateChequeModalForm'),
                allowClear: !0,
                ajax: {
                    url: __fournisseur_select2_route,

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
        }, error: xhr => {
            $(this).removeAttr('disabled').html(html)
            if (xhr.status !== undefined) {
                if (xhr.status === 403) {
                    toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                    return
                }
            }
            toastr.error('Un erreur est produit')
        }
    })
});


$(document).on('click', '.cheque-decaisser', function () {
    let url = $(this).data('url');

    Swal.fire({
        title: "Êtes-vous sûr?",
        text: "Voulez-vous vraiment décaissé ce chèque?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, continuer",
        cancelButtonText: "Annuler",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-success mx-2',
            cancelButton: 'btn btn-soft-secondary mx-2',
        },
    }).then((willDecash) => {
        if (willDecash) {
            $.ajax({
                url:url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': __csrf_token
                },
                success: function (response) {
                    toastr.success(response);
                    table.ajax.reload();
                },
                error: function (xhr) {
                    if (xhr.status === 403) {
                        toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                    } else {
                        toastr.error('Une erreur est survenue');
                    }
                }
            });
        }
    });
});

$(document).on('click', '.cheque-annuler', function () {
    let url = $(this).data('url');

    Swal.fire({
        title: "Êtes-vous sûr?",
        text: "Voulez-vous vraiment annuler ce chèque?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, continuer",
        cancelButtonText: "Annuler",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-danger mx-2',
            cancelButton: 'btn btn-soft-secondary mx-2',
        },
    }).then((willAnnuler) => {
        if (willAnnuler) {
            $.ajax({
                url: url,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': __csrf_token
                },
                success: function (response) {
                    toastr.success(response);
                    table.ajax.reload();
                },
                error: function (xhr) {
                    if (xhr.status === 403) {
                        toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                    } else {
                        toastr.error('Une erreur est survenue');
                    }
                }
            });
        }
    });
});

