
<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">{{$o_transfert->reference}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>

<div class="modal-body">
    <table class="table table-stripped table table-bordered" style="border-collapse: collapse !important">
        <thead>
        <tr class="bg-primary ">
            <th class="text-white">Date</th>
            <th class="text-white">Magasin de sortie</th>
            <th class="text-white">Magasin d'entrée</th>
            <th class="text-white">Statut</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$o_transfert->created_at->format('d/m/Y')}}</td>
                <td>{{$o_transfert->magasin_sortie->nom}}</td>
                <td>{{$o_transfert->magasin_entree->nom}}</td>
                <td>{{$o_transfert->statut}}</td>
            </tr>
        </tbody>

    </table>

   <table class="table table-stripped table table-bordered" style="border-collapse: collapse !important">
       <thead>
       <tr class="bg-primary ">
           <th class="text-white">Article</th>
           <th class="text-white">Quantite demandée</th>
           <th class="text-white">Quantite livrée</th>
       </tr>
       </thead>
       <tbody>
       @foreach($o_transfert->lignes as $ligne)
           <tr>
               <td>{{$ligne->article->designation}}</td>
               <td>{{$ligne->quantite_demande}}</td>
               <td>{{$ligne->quantite_livre ?? '--' }} </td>
           </tr>
       @endforeach
       </tbody>
   </table>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
</div>

