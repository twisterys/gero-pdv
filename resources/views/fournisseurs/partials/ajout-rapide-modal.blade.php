<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Ajout rapide de fournisseur</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('fournisseurs.sauvegarder') }}" method="POST" class="needs-validation"
      autocomplete="off">         <!-- #####--Card Title--##### -->
    @csrf
    <div class="row px-3 align-items-start pt-4">
            <div class="col-12 mb-3">
                <label for="reference " class="form-label required">
                    Référence
                </label>
                <input type="text" class="form-control  @error('reference') is-invalid @enderror"
                       id="reference" name="reference"
                       maxlength="20" value="{{$fournisseur_reference}}" readonly required>
                <div class="invalid-feedback">
                </div>
            </div>
            <div class="col-12  mb-3">
                <label for="form-juridique-select"
                       class="form-label required">Forme juridique</label>
                <select
                    class="select2 form-control  mb-3 custom-select"
                    id="form-juridique-select"
                    name="forme_juridique">
                    @foreach ($form_juridique_types as $type)
                        <option id="{{$type->nom_sur_facture}}" value="{{ $type->id }}">{{ $type->nom }}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback">
                </div>
            </div>
            <div class="col-12  mb-3">
                <label for="nom"
                       class="form-label required"
                       id="dynamic_label">
                    Dénomination
                </label>
                <input type="text" class="form-control " id="nom" name="nom" required>
                <div class="invalid-feedback">
                </div>
            </div>
            <div class="col-12 mb-3">
                <label for="ice" class="form-label">
                    ICE
                </label>
                <input type="text"
                       class="form-control" max="15" id="ice" name="ice">
                <div class="invalid-feedback">
                </div>
            </div>
            <div class="col-12 mb-3">
                <label for="telephone" class="form-label">
                    Téléphone
                </label>
                <input type="tel"
                       class="form-control"
                       id="telephone" name="telephone">
                <div class="invalid-feedback">
                </div>
            </div>
        </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button type="button" id="add-btn-fournisseur" class="btn btn-primary">Enregistrer</button>
    </div>
</form>

