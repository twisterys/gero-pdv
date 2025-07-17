<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Dévalider {{$o_achat->reference}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation" enctype="multipart/form-data" action="{{route('achats.devalider',[$type,$o_achat->id])}}" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        {{--        <h3> {{$o_vente->reference}}</h3>--}}
        <p class="mt-2">Vous êtes sur le point de dévalider {{strtolower(__('achats.'.$type))}} <b>{{$o_achat->reference}}</b>, Êtes-vous sûr de  continuer ?</p>
        <p class="mt-2"><strong>Note :</strong>Si {{strtoupper(__('achats.'.$type))}} a un impact sur le stock, une inversion du stock sera appliquée.</p>

        {{--        <label for="i_image"--}}
        {{--               class="form-label">Pièce jointe</label>--}}
        {{--        <input name="i_piece_jointe" type="file" id="i_piece_jointe" accept="application/pdf">--}}
    </div>
    <div class="modal-footer ">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-success"  >Dévalider</button>
    </div>


</form>
{{--<script>--}}
{{--    $("#i_piece_jointe").dropify({--}}
{{--        messages: {--}}
{{--            default: "Glissez-déposez un fichier ici ou cliquez",--}}
{{--            replace: "Glissez-déposez un fichier ou cliquez pour remplacer",--}}
{{--            remove: "Supprimer",--}}
{{--            error: "Désolé, le fichier trop volumineux"--}}
{{--        },--}}
{{--    });--}}
{{--</script>--}}
