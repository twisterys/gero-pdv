<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Relancer le client {{$o_vente->client->nom}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" class="needs-validation" action="{{route('ventes.relancer',[$type,$o_vente->id])}}" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <!-- Section des tags HTML -->
            <div class="col-md-4 col-sm-12 form-group mb-4">
                <label class="form-label mb-2">Tags HTML Disponibles</label>
                <div class="border rounded p-3 shadow-sm bg-light">
                    <h6 class="mb-3 text-primary">Utilisez ces tags dans l'objet et le contenu HTML :</h6>
                    <ul class="list-unstyled mb-0">
                        <li><strong>[CLIENT]</strong> : Nom du client</li>
                        <li><strong>[DATE_EXPIRATION]</strong> : Date d'expiration</li>
                        <li><strong>[DATE_EMISSION]</strong> : Date d'émission</li>
                        <li><strong>[TOTAL]</strong> : Total ttc</li>
                        <li><strong>[SOLDE]</strong> : Montant non payé</li>
                        <li><strong>[REFERENCE]</strong> : Référence du document</li>
                    </ul>
                </div>
            </div>

            <!-- Section d'édition du contenu -->
            <div class="col-md-8 col-sm-12">
                <!-- Champ pour le sujet -->
                <div class="form-group mb-3">
                    <label for="subject" class="form-label required">Objet</label>
                    <input type="text" name="subject" id="subject" class="form-control" value="{{ $template->subject ?? '' }}" required>
                </div>

                <!-- Champ pour le contenu -->
                <label for="html-input" class="form-label required">{{$template->name}}</label>
                <textarea name="content" id="content" class="form-control" rows="20">{{ $template->content }}</textarea>
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-success">Envoyer</button>
    </div>
</form>
<script>
    tinymce.init({
        selector: "#content",
        height: 500,
        menubar: !1,
        plugins: __tinymce_plugins,
        toolbar: __tinymce_toolbar,
        toolbar_mode: "floating",
        content_style:
            "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
    });

    $(document).on('hidden.bs.modal', '#edit-relancer-modal', function () {
        if (tinymce.get('content')) {
            tinymce.remove('#content');
        }
    });
</script>
