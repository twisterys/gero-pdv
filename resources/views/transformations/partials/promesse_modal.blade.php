<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
        Nouvelle Promesse </h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="promesse-form" action="{{route('ventes.promesse',[$type,$o_vente->id])}}" autocomplete="off">
    @csrf
    <div class="modal-body">
        <div class="col-12">
            <label for="type" class="form-label required">Type</label>
            <select name="i_type" class="form-control w-100" id="type">
                <option value="promesse">Promesse</option>
                <option value="prevision">Prévision</option>
            </select>
        </div>
        <div class="col-12 mt-3">
            <label for="montant" class="form-label required">Montant de promesse</label>
            <div class="input-group">
                <input required class="form-control" step="0.01" min="1" max="{{$o_vente->solde}}" type="number"
                       value="{{$o_vente->solde}}" name="i_montant" id="montant">
                <span class="input-group-text">MAD</span>
            </div>
        </div>
        <div class="col-12 mt-3 __variable">
            <label for="date" class="form-label required">Date</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true" type="text"
                       name="i_date" id="date" value="{{\Carbon\Carbon::now()->addDays(15)->format('d/m/Y')}}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Ajouter</button>
    </div>
</form>
<script>
    $('#type').select2({
        minimumResultsForSearch: -1,
        width:'100%'
    })

    $('.datupickeru').datepicker({
        language:'fr',
        defaultViewDate: {year: '{{\Carbon\Carbon::createFromFormat('d/m/Y',$o_vente->date_emission)->format('Y')}}',month:'{{\Carbon\Carbon::createFromFormat('d/m/Y',$o_vente->date_emission)->format('m')}}'}
    })
    var submit_paiement = !1;
    $('#promesse-form').submit(function (e) {
        e.preventDefault();
        if(!submit_paiement){
            let spinner = $(__spinner_element);
            let  btn =$('#promesse-form').find('.btn-info');
            btn.attr('disabled','').prepend(spinner)
            submit_paiement = !0;
            $.ajax({
                url:$('#promesse-form').attr('action'),
                method:'POST',
                data: $(this).serialize(),
                headers:{
                    'X-CSRF-Token':__csrf_token
                },
                success: function (response) {
                    btn.removeAttr('disabled');
                    submit_paiement = 0;
                    spinner.remove();
                    toastr.success(response);
                    setTimeout(()=>{
                        location.reload()
                    },200)
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    btn.removeAttr('disabled');
                    submit_paiement = !1;
                    spinner.remove();
                    toastr.error(xhr.responseText);
                }
            })
        }
    })
</script>
