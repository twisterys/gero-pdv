<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Relancer le client {{$o_vente->client->nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation" action="{{route('ventes.relancer',[$type,$o_vente->id])}}" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <h3> @lang('ventes.'.$type) {{$o_vente->reference ?? null}} </h3>
        <p class="mt-2">Vous êtes entrain de envoyer un email de relance pour @lang('ventes.'.$type) <b>{{$o_vente->reference ?? null}}</b> , Êtes-vous sûr de continuer ?</p>
    </div>

    <div class="modal-footer">
        <button data-href="{{route('ventes.edit_template_relancer_modal', [$type,$o_vente->id])}}"  type="button" class="btn btn-soft-info me-auto show-template-btn">Modifier</button>
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-success"  >Envoyer</button>
    </div>
</form>

<script>
    $('.show-template-btn').click(function (e) {
        if (relancer_process === 0) {
            relancer_process = 1;
            $(this).find('>i').addClass('d-none');
            let spinner = $(__spinner_element);
            let btn = $(this);
            $(this).attr('disabled', '').prepend(spinner);
            $('#relancer-modal').modal('hide');

            $.ajax({
                url: $(this).data('href'),
                success: function (response) {
                    $('#edit-relancer-modal').find('.modal-content').html(response);
                    $('#edit-relancer-modal').modal("show");
                    btn.find('>i').removeClass('d-none');
                    btn.removeAttr('disabled');
                    relancer_process = 0;
                    spinner.remove();
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    btn.find('>i').removeClass('d-none');
                    btn.removeAttr('disabled');
                    relancer_process = 0;
                    spinner.remove();
                    if (xhr.status != undefined) {
                        if (xhr.status === 403) {
                            toastr.warning("Vous n'avez pas l'autorisation nécessaire pour effectuer cette action");
                            return;
                        }
                    }
                    toastr.error(xhr.responseText);
                }
            });
        }
    });
</script>


