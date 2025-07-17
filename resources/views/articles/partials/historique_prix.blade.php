<div class="modal-header">
    <h5 class="modal-title align-self-center">
        Historique de prix : {{$o_article->designation}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
   <table class="table table-striped table-bordered">
       <thead>
       <tr>
           <th>Vente</th>
           <th>Date</th>
           <th>Prix HT</th>
       </tr>
       </thead>
       <tbody>
       @foreach($ventes as $vente)
           <tr>
               <td>{{$vente->reference}}</td>
               <td>{{\Carbon\Carbon::make($vente->date_document)->format('d/m/Y')}}</td>
               <td>{{number_format($vente->ht,2,'.',' ')}} MAD</td>
           </tr>
       @endforeach
       </tbody>
   </table>
</div>
