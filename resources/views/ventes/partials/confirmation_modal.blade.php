<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Confirmer {{$o_vente->reference}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation" enctype="multipart/form-data" action="{{route('ventes.confirmer',[$type,$o_vente->id])}}" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <h3> {{$o_vente->reference}}</h3>
        <p class="mt-2">une fois que vous aurez confirmé {{strtolower(__('ventes.'.$type))}} <b>{{$o_vente->reference}}</b>,  il sera verrouillé et ne pourra pas être modifié ou supprimé et il n'y aura aucun moyen de revenir après</p>
        <label for="i_image"
               class="form-label">Pièce jointe</label>
        <input name="i_piece_jointe" type="file" id="i_piece_jointe" accept="application/pdf">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-success"  >Confirmer</button>
    </div>
</form>
<script>
    $("#i_piece_jointe").dropify({
        messages: {
            default: "Glissez-déposez un fichier ici ou cliquez",
            replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
            remove: "Supprimer",
            error: "Désolé, le fichier trop volumineux"
        },
    });
</script>
