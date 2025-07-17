var historique_prix = false;
$(document).on('click','.historique_prix_btn',function (){
    if (!historique_prix){
        historique_prix = !historique_prix
        let btn = $(this);
        let html = btn.html();
        btn.html(__spinner_element);
        $.ajax({
            url:window.origin+'/articles/historique-prix',
            method: 'post',
            data:{
                client_id: $('#client_select').val(),
                article_id: btn.closest('tr').find('.article_id').val()
            },
            headers:{
              'X-CSRF-TOKEN' : __csrf_token
            },
            success: function (response){
                $('#historique_prix_modal .modal-content').html(response);
                $('#historique_prix_modal').modal('show');
                btn.html(html)
                historique_prix = !historique_prix
            },
            error: function (xhr){
                historique_prix = !historique_prix
                btn.html(html)
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (const [key, value] of Object.entries(errors)) {
                        toastr.error(value)
                    }
                }

            }
        })
    }
})
