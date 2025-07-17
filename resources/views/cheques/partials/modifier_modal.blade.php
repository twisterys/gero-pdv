<form id="updateChequeModalForm" action="{{route('cheques.mettre_a_jour',$cheque->id)}}">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title" id="updateCheckLabel">Modifier le chèque {{$cheque->number}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if($cheque->type === 'encaissement')
            <div class="mb-3">
                <label for="update_client_id" class="form-label required">Client</label>
                <select required class="select2 form-control mb-3 custom-select" id="update_client_id"
                        name="update_client_id">
                    <option value="{{ $cheque->client_id }}" }>{{ $cheque->client->nom }}</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        @else
            <div class="mb-3">
                <label for="update_fournisseur_id" class="form-label required">Fournisseur</label>
                <select required class="select2 form-control mb-3 custom-select" id="update_fournisseur_id"
                        name="update_fournisseur_id">
                    <option value="{{ $cheque->fournisseur_id }}" }>{{ $cheque->fournisseur->nom }}</option>
                </select>
                <div class="invalid-feedback"></div>
            </div>
        @endif

        <div class="mb-3">
            <label for="update_date_emission" class="form-label required">Date d'émission</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true"
                       type="text" name="update_date_emission" id="update_date_emission"
                       value="{{ \Carbon\Carbon::make($cheque->date_emission )->format('d/m/Y')}}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="mb-3">
            <label for="update_date_echeance" class="form-label required">Date d'écheance</label>
            <div class="input-group">
                <input required class="form-control datupickeru" data-provide="datepicker" data-date-autoclose="true"
                       type="text" name="update_date_echeance" id="update_date_echeance"
                       value="{{\Carbon\Carbon::make( $cheque->date_echeance)->format('d/m/Y') }}">
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="mb-3">
            <label for="update_montant_encaisse" class="form-label">Montant de cheque</label>
            <input type="number" class="form-control" id="update_montant_encaisse" name="update_montant_encaisse"
                   value="{{ $cheque->montant }}" required>
            <div class="invalid-feedback"></div>
        </div>
        <div class="mb-3">
            <label for="update_i_compte_id" class="form-label">Compte bancaire</label>
            <select  name="update_i_compte_id" class="form-select" style="width: 100%" id="update_i_compte_id">
                <option value="">Compte bancaire</option>
                @foreach($comptes as $compte)
                    <option
                        value="{{ $compte->id }}" {{ $cheque->compte_id == $compte->id ? 'selected' : '' }}>{{ $compte->nom }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback"></div>
        </div>
        @if($cheque->type === 'encaissement')
            <div class="mb-3">
                <label for="update_banque" class="form-label required">Banque émettrice</label>
                <select name="update_banque" id="update_banque" class="form-select">
                    @foreach($banques as $banque)
                        <option value="{{ $banque->id }}"
                                {{ $cheque->banque_id == $banque->id ? 'selected' : '' }} data-img="{{ asset($banque->image) }}">{{ $banque->nom }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>

        @endif

        <div class="mb-3">
            <label for="update_note" class="form-label">Notes</label>
            <textarea class="form-control" id="update_note" name="update_note" rows="5">{{ $cheque->note }}</textarea>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
        <button type="submit" class="btn btn-primary">Mettre à jour</button>
    </div>
</form>
