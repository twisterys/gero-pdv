<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-uni-modal-title">Modification de la form juridique {{$o_forme_juridique->nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" action="{{route('formes_juridique.mettre_a_jour',$o_forme_juridique->id)}}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required" for="name-input">Forme juridique</label>
                <input type="text" value="{{$o_forme_juridique->nom}}" required class="form-control" id="name-input" name="nom">

            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="name-input">Nom sur facture</label>
                <input type="text" value="{{$o_forme_juridique->nom_sur_facture}}" required class="form-control" id="nom_sur_facture" name="nom_sur_facture">

            </div>

        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>
