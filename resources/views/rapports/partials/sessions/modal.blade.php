
<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Ventes de la session</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-stripped table table-bordered" style="border-collapse: collapse !important">
        <thead>
        <tr class="bg-primary ">


            <th class="text-white">Référence de vente</th>
            <th class="text-white">Type document</th>
            <th class="text-white">Date document</th>
            <th class="text-white">Montant TTC</th>
        </tr>
        </thead>
        <tbody>
        @foreach($o_session->ventes as $vente)
            <tr>
                <td>
                    <a target="_blank" href="{{ route('ventes.afficher', ['type' => $vente->type_document, 'id' => $vente->id]) }}" class="text-info text-decoration-underline">
                        {{ $vente->reference }}
                    </a>
                </td>
                <td>{{(__('ventes.'.$vente->type_document))}}</td>
                <td>{{$vente->date_document}}</td>
                <td>{{$vente->total_ttc}}</td>
            </tr>
        @endforeach

        </tbody>

    </table>

</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
</div>

