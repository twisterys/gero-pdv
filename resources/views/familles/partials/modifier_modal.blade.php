<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Modification de famille {{$name}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" action="{{route('familles.mettre_a_jour',$id)}}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required" for="name-input">Nom</label>
                <input type="text" value="{{$name}}" required class="form-control" id="name-input" name="i_nom">
                <div class="invalid-feedback">Veuillez d'abord entrer un nom</div>
            </div>
            <div class="col-12 mb-3">
                <label for="color-input" class="form-label">Couleur</label>
                <input type="text" class="form-control" name="i_couleur" value="{{$color}}" id="color-input">
            </div>
            <div class="col-12 mb-3">
                <div class="form-check-inline d-flex align-items-center">
                    <label for="" class="form-check-label me-2" >Active</label>
                    <input name="i_actif" value="1" type="checkbox" id="active-input-edit" switch="bool" @if($actif == 1) checked="" @endif>
                    <label for="active-input-edit" data-on-label="Oui" data-off-label="Non"></label>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>
