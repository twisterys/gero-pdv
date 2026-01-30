<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
        Modification de paiement de {{ $o_paiement->payable->reference ?? '---' }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="paiement_form_edit" action="{{ route('paiement.mettre_a_jour', $o_paiement->id) }}"
    autocomplete="off">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="col-12 mt-3">
            <label for="date_paiement" class="form-label required">Date de paiement</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true"
                    type="text" name="i_date_paiement" id="date_paiement" value="{{ $o_paiement->date_paiement }}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-12 mt-3">
            <label for="montant" class="form-label required">Montant de paiement</label>
            <div class="input-group">
                <input required readonly class="form-control" step="0.001" min="1" type="number"
                    value="{{ $o_paiement->decaisser + $o_paiement->encaisser }}" name="i_montant" id="montant">
                <span class="input-group-text">MAD</span>
            </div>
        </div>
        <div class="col-12 mt-3">
            <label for="compte-input" class="form-label required">Compte</label>
            <select required name="i_compte_id" class="form-control " style="width: 100%" id="compte-input">
                @foreach ($comptes as $compte)
                    <option @selected(old('i_compte_id', $o_paiement->compte_id) == $compte->id) value="{{ $compte->id }}">{{ $compte->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mt-3">
            <label for="method-input" class="form-label required">Méthode de paiement</label>
            <select required name="i_method_key" class="form-control " style="width: 100%" id="method-input">
                @foreach ($methodes as $methode)
                    <option @selected(old('i_method_key', $o_paiement->methode_paiement_key) == $methode->key) value="{{ $methode->key }}">{{ $methode->nom }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12 mt-3 __variable">
            <label for="date" class="form-label required">Date prévu</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true"
                    type="text" name="i_date" id="date"
                    value="{{ old('i_date', $o_paiement->cheque_lcn_date) }}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>
        <div class="col-12 mt-3 __variable">
            <label for="i_reference" class="form-label required">Référence de chéque</label>
            <input class="form-control" type="text" name="i_reference" id="i_reference"
                value="{{ old('i_reference', $o_paiement->cheque_lcn_reference) }}">
        </div>
        <div class="col-12 mt-3">
            <label for="i_note" class="form-label">Note</label>
            <textarea name="i_note" id="i_note" cols="30" rows="3" class="form-control">{{ old('i_note', $o_paiement->note) }}</textarea>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-info">Modifier</button>
    </div>
</form>
<script>
    $('#paiement_form_edit select').select2({
        minimumInputLength: -1,
        minimumResultsForSearch: -1,
    })
    checkModal()
</script>
