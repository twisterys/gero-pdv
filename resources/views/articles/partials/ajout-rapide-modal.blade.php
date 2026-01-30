<div class="modal-header">
    <div class="d-flex align-items-center">
        <span id="return_to_article" style="cursor: pointer"><i class="fa fa-chevron-left me-2"></i></span>
        <h5 class="modal-title align-self-center">Ajout rapide d'article</h5>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form action="{{ route('articles.sauvegarder') }}" method="POST" class="needs-validation" autocomplete="off">
    <!-- #####--Card Title--##### -->
    @csrf
    <div class="row px-3 align-items-start pt-4">
        <div class=" col-12 mb-3 ">
            <label class="form-label required" for="reference-input">Référence</label>
            <input required type="text" class="form-control" id="reference-input" placeholder="" name="i_reference"
                value="{{ $article_reference }}">
            <div class="invalid-feedback">
            </div>
        </div>
        <div class="col-12  mb-3 ">
            <label class="form-label   required" for="designation-input">Désignation</label>
            <input required type="text" class="form-control" id="designation-input" placeholder=""
                name="i_designation" value="">
            <div class="invalid-feedback">
            </div>
        </div>
        <div class="col-12 mb-3 ">
            <label class="form-label required" for="unity-select">Unité</label>
            <select required class="select2 form-control mb-3 custom-select" name="i_unite" id="unity-select">
                @if ($unites)
                    @foreach ($unites as $unite)
                        <option value="{{ $unite['id'] }}">{{ $unite['nom'] }}</option>
                    @endforeach
                @endif
            </select>
            <div class="invalid-feedback">
            </div>
        </div>
        <div class="col-12 mb-3 ">
            <label class="form-label required " for="tax-select">Taxe</label>
            <select required class="select2 form-control mb-3 custom-select " name="i_taxe" id="tax-select">
                @if ($taxes)
                    @foreach ($taxes as $taxe)
                        <option value="{{ $taxe['valeur'] }}">{{ $taxe['nom'] }}</option>
                    @endforeach
                @endif
            </select>
            <div class="invalid-feedback">
            </div>
        </div>
        <div class="col-12 mb-3 ">
            <label class="form-label   required" for="vente-input">Prix de vente</label>
            <div class="input-group">
                <input required type="number" step="0.001" class="form-control" id="vente-input" min="0"
                    name="i_vente_prix">
                <span class="input-group-text">MAD</span>
                <div class="invalid-feedback">
                </div>
            </div>
        </div>
        <div class="col-12 mb-3 ">
            <label class="form-label" for="achat-input">Prix d'achat</label>
            <div class="input-group">
                <input required type="number" step="0.001" class="form-control" id="achat-input" min="0"
                    name="i_achat_prix">
                <span class="input-group-text">MAD</span>
                <div class="invalid-feedback">
                </div>
            </div>
        </div>
        <div class="col-12  mb-3 ">
            <label class="form-label " for="designation-input">Code barre</label>
            <input type="text" class="form-control" id="designation-input" placeholder=""
                   name="i_code_barre" value="">
            <div class="invalid-feedback">
            </div>
        </div>
        <div class="col-12 mb-3 ">
            <div class="d-flex align-items-center">
                <label for="i_stockable" class="form-check-label me-2">Stockable</label>
                <div class="form-check-inline " style="height: 24px">
                    <input name="i_stockable" value="1" type="checkbox" id="i_stockable"
                           switch="bool"
                           checked  >
                    <label for="i_stockable" data-on-label="Oui"
                           data-off-label="Non"></label>
                </div>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button type="button" id="save-btn-article" class="btn btn-primary">Enregistrer</button>
    </div>
</form>
