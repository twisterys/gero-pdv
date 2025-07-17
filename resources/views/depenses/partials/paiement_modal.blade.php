<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
        Payer</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="paiement_form" action="{{route('depenses.payer',$o_depense->id)}}" autocomplete="off">
    @csrf
    <div class="modal-body">

        <div class="col-12 mt-3">
            <label for="date_paiement" class="form-label required">Date de paiement</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true" type="text"
                       name="i_date_paiement" id="date_paiement" value="{{\Carbon\Carbon::now()->format('d/m/Y')}}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-12">
            <label for="montant" class="form-label required">Montant de paiement</label>
            <div class="input-group">
                <input required class="form-control" step="0.01" min="1" max="{{$o_depense->solde}}" type="number"
                       value="{{$o_depense->solde}}" name="i_montant" id="montant">
                <span class="input-group-text">MAD</span>
            </div>
        </div>
        <div class="col-12 mt-3">
            <label for="compte-input" class="form-label required">Compte</label>
            <select required name="i_compte_id" class="form-control " style="width: 100%" id="compte-input">
                @foreach($comptes as $compte)
                    <option @selected($compte->principal) value="{{$compte->id}}">{{$compte->nom}}</option>
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




