<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-taxe-modal-title">Modification de famille {{$o_taxe->nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="tax-modal-edit" action="{{route('taxes.mettre_a_jour',$o_taxe->valeur)}}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required" for="name-input">Nom</label>
                <input type="text" value="{{$o_taxe->nom}}" required class="form-control" id="name-input" name="i_nom">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="valeur-input">Valeur</label>
                <div class="input-group">
                    <input type="text" value="{{$o_taxe->valeur}}" required class="form-control" id="valeur-input" name="i_valeur">
                    <span class="input-group-text" >%</span>
                </div>
            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>
<script>
    var submit_edit_taxe = !1;
    $('#tax-modal-edit').submit(function (e) {
        e.preventDefault();
        if(!submit_edit_taxe){
            let spinner = $(__spinner_element);
            let  btn =$('#tax-modal-edit').find('.btn-primary');
            btn.attr('disabled','').prepend(spinner)
            submit_edit_taxe = !0;
            $.ajax({
                url:$('#tax-modal-edit').attr('action'),
                method:'POST',
                data: $(this).serialize(),
                headers:{
                    'X-CSRF-Token':__csrf_token
                },
                success: function (response) {
                    btn.removeAttr('disabled');
                    submit_edit_taxe = 0;
                    spinner.remove();
                    toastr.success(response);
                    location.reload()
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    btn.removeAttr('disabled');
                    submit_edit_taxe = !1;
                    spinner.remove();
                    toastr.error(xhr.responseText);
                }
            })
        }
    })
</script>
