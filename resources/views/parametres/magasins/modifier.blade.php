<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-uni-modal-title">Modification du magasin {{$nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" action="{{route('magasin.mettre_a_jour',$id)}}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required " for="reference">Référence</label>
                <input type="text" value="{{$reference}}" required class="form-control" id="reference" name="reference">
                {{--                                <div class="invalid-feedback">Veuillez d'abord entrer un méthode de paiement</div>--}}
            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="name-input">Nom</label>
                <input type="text" value="{{$nom}}"  required class="form-control" id="name-input" name="nom">
                {{--                <div class="invalid-feedback">Veuillez d'abord entrer un méthode de paiement</div>--}}
            </div>
            <div class="col-12 mb-3">
                <label class="form-label required " for="adresse">Adresse</label>
                <input type="text" value="{{$adresse}}" required class="form-control" id="adresse" name="adresse">
                {{--<div class="invalid-feedback">Veuillez d'abord entrer un méthode de paiement</div>--}}
            </div>

            <div class="col-12 mb-3">
                <label class="form-label required" for="type_local">Type Local</label>
                <select class="form-select" id="type_local" name="type_local" data-parsley-multiple="groups" data-parsley-mincheck="1">
                    <option value="1" {{ $type_local == 1 ? 'selected' : '' }}>Point de vente & dépôt</option>
                    <option value="2" {{ $type_local == 2 ? 'selected' : '' }}>Dépôt seulement</option>
                </select>
            </div>


        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>
