<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
        Payer {{strtolower(@__('ventes.'.$type))}} {{$o_vente->reference}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="paiement_form" action="{{route('ventes.payer',[$type,$o_vente->id])}}" autocomplete="off">
    @csrf
    <div class="modal-body">
{{--                <table class="table table-bordered table-striped" >--}}
{{--                    <tr>--}}
{{--                        <th>Total TTC</th>--}}
{{--                        <th>{{$o_vente->total_ttc}} MAD</th>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <th>Total payé</th>--}}
{{--                        <th>{{$o_vente->encaisser}} MAD</th>--}}
{{--                    </tr>--}}
{{--                    <tr>--}}
{{--                        <th>Total à payé</th>--}}
{{--                        <th>{{$o_vente->solde}} MAD</th>--}}
{{--                    </tr>--}}
{{--                </table>--}}
        <div class="col-12 mt-3 @if ($magasins_count  <= 1) d-none @endif">
            <label for="magasin_id" class="form-label required d-block">
                Magasin
            </label>
            <select name="magasin_id" {{count($o_magasins) <=1 ? 'readonly':null }}
                class="form-control w-100" id="magasin-select">
                @foreach ($o_magasins as $o_magasin)
                    <option value="{{ $o_magasin->id }}" @if($o_vente->magasin_id == $o_magasin->id) selected @endif>{{ $o_magasin->text }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mt-3">
            <label for="date_paiement" class="form-label required">Date de paiement</label>
            <div class="input-group">
                @cannot('paiement.date')
                    <input type="text" class="form-control"
                           readonly
                           value="{{\Carbon\Carbon::now()->format('d/m/Y')}}"
                    >
                @endcannot
                <input required class="form-control datupickeru @cannot('paiement.date') d-none @endcannot " data-provide="datepicker" data-date-autoclose="true" type="text"
                       name="i_date_paiement" id="date_paiement" value="{{\Carbon\Carbon::now()->format('d/m/Y')}}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-12 mt-3">
            <label for="montant" class="form-label required">Montant de paiement</label>
            <div class="input-group">
                <input required class="form-control" step="0.001" min="1" max="{{$o_vente->solde}}" type="number"
                       value="{{$o_vente->solde}}" name="i_montant" id="montant">
                <span class="input-group-text">MAD</span>
            </div>
        </div>
        <div class="col-12 mt-3">
            <label for="compte-input" class="form-label required">Compte</label>
            <select required name="i_compte_id" class="form-control " style="width: 100%" id="compte-input">
                @foreach($comptes as $compte)
                    <option @if($compte->principal) selected @endif value="{{$compte->id}}">{{$compte->nom}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mt-3">
            <label for="method-input" class="form-label required">Méthode de paiement</label>
            <select required name="i_method_key" class="form-control " style="width: 100%" id="method-input">
                @foreach($methodes as $methode)
                    <option value="{{$methode->key}}">{{$methode->nom}}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mt-3 __variable">
            <label for="date" class="form-label required">Date prévu</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true" type="text"
                       name="i_date" id="date">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-12 mt-3 __variable">
            <label for="i_reference" class="form-label required">Référence de chéque</label>
            <input required class="form-control" type="text" name="i_reference" id="i_reference">
        </div>
        <div class="col-12 mt-3">
            <label for="i_note" class="form-label">Note</label>
            <textarea name="i_note" id="i_note" cols="30" rows="3" class="form-control"></textarea>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-info">Payer</button>
    </div>
</form>
<script>
    $('#compte-input,#method-input,#magasin-select').select2({
        minimumResultsForSearch: -1,
        width: '100%'
    })
    $('#method-input').on('change', function () {
        check()
    })
    check()
    function check() {
        let methods = ['cheque', 'lcn'];
        if (methods.indexOf($('#method-input').find('option:selected').val()) !== -1) {
            $('.__variable').removeClass('d-none').find('input').attr('required','')
        }else {
            $('.__variable').addClass('d-none').find('input').removeAttr('required')
        }
    }
    $('.datupickeru').datepicker({
        language:'fr',
        defaultViewDate: {year: '{{\Carbon\Carbon::createFromFormat('d/m/Y',$o_vente->date_emission)->format('Y')}}',month:'{{\Carbon\Carbon::createFromFormat('d/m/Y',$o_vente->date_emission)->format('m')}}'}
    })
    var submit_paiement = !1;
    $('#paiement_form').submit(function (e) {
        e.preventDefault();
        if(!submit_paiement){
            let spinner = $(__spinner_element);
            let  btn =$('#paiement_form').find('.btn-info');
            btn.attr('disabled','').prepend(spinner)
            submit_paiement = !0;
            $.ajax({
                url:$('#paiement_form').attr('action'),
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
                    location.reload()
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
