<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
        Renouvellement de l'abonnement N : {{ $abonnement->id }} </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="paiement_form" action="{{route('abonnements.renouveler')}}" autocomplete="off">
    @csrf
    <div class="modal-body">
            <input type="hidden" name="abonnement_id" value="{{ $abonnement->id }}"/>
        <div class="col-12 mt-3">
            <label for="date_paiement" class="form-label required">Date de renouvellement</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true" type="text"
                       name="date_renouvellement" id="date_paiement" value="{{\Carbon\Carbon::parse($abonnement->date_expiration)->format('d/m/Y')}}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-12 mt-3">
            <label for="date_paiement" class="form-label required">Date d'expiration</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true" type="text"
                       name="date_expiration" id="date_paiement" value="{{\Carbon\Carbon::parse($abonnement->date_expiration)->addYear()->format('d/m/Y')}}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-12 mt-3 ">
            <label for="montant" class="form-label required">Montant de Renouvelement</label>
            <div class="input-group">
                <input required class="form-control" step="0.01" min="0"  type="number"
                       value="" name="montant" id="montant">
                <span class="input-group-text">MAD</span>
            </div>
        </div>

        <div class="col-12 mt-3 __variable">
            <label for="document_reference" class="form-label">Référence de document</label>
            <input  class="form-control" type="text" name="document_reference" id="document_reference">
        </div>
        <div class="col-12 mt-3">
            <label for="i_note" class="form-label">Note</label>
            <textarea name="note" id="i_note" cols="30" rows="3" class="form-control"></textarea>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-info">Sauvegarder</button>
    </div>
</form>
<script>

    $('.datupickeru').datepicker({    language:'fr',  })


    var submit_process = !1;
    $('#paiement_form').submit(function (e) {
        e.preventDefault();
        if(!submit_process){
            let spinner = $(__spinner_element);
            let  btn =$('#paiement_form').find('.btn-info');
            btn.attr('disabled','').prepend(spinner)
            submit_process = !0;
            $.ajax({
                url:$('#paiement_form').attr('action'),
                method:'POST',
                data: $(this).serialize(),
                headers:{
                    'X-CSRF-Token':__csrf_token
                },
                success: function (response) {
                    btn.removeAttr('disabled');
                    submit_process = 0;
                    spinner.remove();
                    toastr.success(response);
                    location.reload()
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    btn.removeAttr('disabled');
                    submit_process = !1;
                    spinner.remove();
                    toastr.error(xhr.responseText);
                }
            })
        }
    })
</script>
