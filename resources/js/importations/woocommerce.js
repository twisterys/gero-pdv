
document.querySelector("#products-import").addEventListener("click", async (e) => {
    e.preventDefault();
    Swal.fire({
        title: "Vous voulez importer les produits ?",
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Oui, importer!",
        buttonsStyling: false,
        customClass: {
            confirmButton: 'btn btn-soft-warning mx-2', cancelButton: 'btn btn-soft-secondary mx-2',
        },
        didOpen: () => {
            $('.btn').blur()
        },
        preConfirm: async () => {
            Swal.showLoading();
            try {
                const [response] = await Promise.all([new Promise((resolve, reject) => {
                    $.ajax({
                        url: $('#products-import').data('url'), method: 'POST', headers: {
                            'X-CSRF-TOKEN': __csrf_token
                        }, success: resolve, error: (_, jqXHR) => reject(_)
                    });
                })]);

                return response;
            } catch (jqXHR) {
                let errorMessage = "Une erreur s'est produite lors de la demande.";
                if(jqXHR.status !== undefined) {
                    if (jqXHR.status === 404) {
                        errorMessage = "La ressource n'a pas été trouvée.";
                    }
                    if (jqXHR.status === 403) {
                        errorMessage = "Vous n'avez pas l'autorisation nécessaire pour effectuer cette action";
                    }
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
                    if (typeof table != 'undefined') {
                        table.ajax.reload();
                    } else {
                        location.reload();
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

const handleValidationErrors = (errors, form) => {
    form.querySelectorAll('.is-invalid').forEach(input => input.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback').forEach(error => error.remove());

    Object.keys(errors).forEach((key) => {
        const fixedKey = dotNotationToBracketNotation(key);
        const input = form.querySelector(`[name="${fixedKey}"]`);
        if (input) {
            input.classList.add('is-invalid');

            const error = document.createElement('div');
            error.classList.add('invalid-feedback');
            error.innerHTML = errors[key][0];
            input.insertAdjacentElement('afterend', error);
        }
    });
};

function dotNotationToBracketNotation(str) {
    return str.replace(/\.(\d+)|\.(\w+)/g, (_, number, word) =>
        number !== undefined ? `[${number}]` : `[${word}]`
    );
}

$(document).on('submit', '#orders-form', async function (e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const url = form.getAttribute('action');
    const method = form.getAttribute('method');
    const button = form.querySelector('button[type="submit"]');
    button.disabled = true;
    const btnText = button.innerHTML;
    button.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> En cours...';

    try {
        $.ajax({
            url: url,
            method: method,
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': __csrf_token
            },
            success: (response) => {
                button.innerHTML = btnText;
                button.disabled = false;
                toastr.success(response);
                $('#import-orders').modal('hide');
                form.trigger('reset');
            },
            error: (_, jqXHR) => {
                button.innerHTML = btnText;
                button.disabled = false;
                if (_.status === 422) {
                    handleValidationErrors(_.responseJSON.errors, form);
                } else {
                    toastr.error(_.responseText);
                }
            }
        });


    } catch (jqXHR) {
        if (jqXHR.status === 422) {
            handleValidationErrors(jqXHR.responseJSON.errors, form);
        } else {
            toastr.error(jqXHR.responseText);
        }
    }
})
