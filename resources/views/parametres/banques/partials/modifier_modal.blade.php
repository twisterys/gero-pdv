<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-modal-title">Modifier {{$banque->nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="edit-form" action="{{route('banques.mettre_a_jour',$banque->id)}}" enctype="multipart/form-data" class="needs-validation" novalidate>
    @method('PUT')
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required" for="name-input">Nom</label>
                <input type="text" value="{{$banque->nom}}" required class="form-control" id="name-input" name="nom">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-12 mb-3">
                <label for="i_image"
                       class="form-label ">Logo</label>
                <input class="i_image" name="i_image" type="file" id="i_image" accept="image/*"  data-default-file="{{asset($banque->image)}}">
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>
