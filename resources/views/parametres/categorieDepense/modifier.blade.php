<div class="modal-header">
  <h5 class="modal-title align-self-center" id="edit-uni-modal-title">Modification de catégorie {{$nom}}</h5>
  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" action="{{route('categories.mettre_a_jour',$id)}}" class="needs-validation" novalidate>
  @csrf
  @method('PUT')
  <div class="modal-body">
      <div class="row">
          <div class="col-12 mb-3">
              <label class="form-label required" for="name-input">Categorie</label>
              <input type="text" value="{{$nom}}" required class="form-control" id="name-input" name="i_nom">
              <div class="invalid-feedback">Veuillez d'abord entrer un nom</div>
          </div>

      </div>
  </div>
  <div class="modal-footer">
      <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
      <button class="btn btn-primary">Enregistrer</button>
  </div>
</form>
