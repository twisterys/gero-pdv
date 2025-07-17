<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-ref-modal-title">Modification de la référence {{$o_reference->nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="POST" action="{{ route('references.mettre_a_jour', $o_reference->id) }}" class="needs-validation" novalidate autocomplete="off">
    @csrf
    @method('PUT')
    <div class="modal-body">
        <h6>Mots clés:</h6>
        <ul>
            <li>[a]: Année à 2 chiffres</li>
            <li>[A]: Année complète</li>
            <li>[m]: Mois à 2 chiffres</li>
            <li>[j]: Jour à 2 chiffres</li>
            <li>[n]: Numérotation</li>
        </ul>
        <div class="row">
            <div class="col-12 mb-3 position-relative">
                <label class="form-label required" for="format">Format</label>
                <input type="text" class="form-control format-autocomplete {{$errors->has('format')?'is-invalid':null}}" id="format" value="{{old('format',$o_reference->template)}}" name="format" >
                <div class="autocomplete-popup"></div>
                @if($errors->has('format'))
                    <div class="invalid-feedback">
                        {{ $errors->first('format') }}
                    </div>
                @endif
            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="longueur_compteur">Longueur du compteur</label>
                <select required class="select2 form-control mb-3 custom-select {{$errors->has('longueur_compteur')? 'is-invalid':null}} " id="longueur_compteur" name="longueur_compteur">
                    <option value="1" {{ $o_reference->longueur_compteur === 1 ? 'selected' : '' }}>1</option>
                    <option value="2" {{ $o_reference->longueur_compteur === 2 ? 'selected' : '' }}>2</option>
                    <option value="3" {{ $o_reference->longueur_compteur === 3 ? 'selected' : '' }}>3</option>
                    <option value="4" {{ $o_reference->longueur_compteur === 4 ? 'selected' : '' }}>4</option>
                    <option value="5" {{ $o_reference->longueur_compteur === 5 ? 'selected' : '' }}>5</option>
                </select>
                @if($errors->has('longueur_compteur'))
                    <div class="invalid-feedback">
                        {{ $errors->first('longueur_compteur') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>

