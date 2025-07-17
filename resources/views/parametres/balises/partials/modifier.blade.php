<div class="modal-header">
    <h5 class="modal-title align-self-center" id="add-tag-modal-title">Modifier {{$tag->nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="edit-tag-form" action="{{route('balises.mettre_a_jour',$tag->id)}}" class="needs-validation" novalidate>
    @method('PUT')
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required" for="name-input">Nom</label>
                <input type="text" value="{{$tag->nom}}" required class="form-control" id="name-input" name="i_nom">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="couleur-input-edit">Couleur</label>
                <input type="text"  value="{{$tag->couleur}}" required class="form-control w-100" id="couleur-input-edit" name="i_couleur">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>
