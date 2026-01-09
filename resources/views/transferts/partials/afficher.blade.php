
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
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{$o_transfert->created_at->format('d/m/Y')}}</td>
                <td>{{$o_transfert->magasinSortie->nom}}</td>
                <td>{{$o_transfert->magasinEntree->nom}}</td>
            </tr>
        </tbody>

    </table>

   <table class="table table-stripped table table-bordered" style="border-collapse: collapse !important">
       <thead>
       <tr class="bg-primary ">
           <th class="text-white">Article</th>
           <th class="text-white">Quantite</th>
       </tr>
       </thead>
       <tbody>
       @foreach($o_transfert->lignes as $ligne)
           <tr>
               <td>{{$ligne->article->designation}}</td>
               <td>{{$ligne->qte}}</td>
           </tr>
       @endforeach
       </tbody>
   </table>
</div>
<div class="modal-footer">
    @can('transfert.controler')
        @if($is_controled && $o_transfert->is_controled)
            <button class="btn btn-soft-dark mx-1" disabled>
                <i class="fa fa-check-circle"></i> Contrôlée
            </button>
        @elseif($is_controled)
            <button data-href="{{ route('transferts.controle', [$o_transfert->id]) }}"
                    id="controle-btn" class="btn btn-soft-success mx-1">
                <i class="fa fa-check-circle"></i> Contrôler
            </button>
        @endif
    @endcan
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
</div>

<script>
    $(document).off('click', '#controle-btn').on('click', '#controle-btn', function () {
        let url = $(this).data('href');
        Swal.fire({
            title: "Êtes-vous sûr?",
            text: "voulez-vous contrôler ce transfert ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Oui, contrôler!",
            buttonsStyling: false,
            customClass: {
                confirmButton: 'btn btn-success mx-2',
                cancelButton: 'btn btn-light mx-2',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message);
                            location.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function (xhr) {
                        toastr.error('Une erreur est survenue');
                    }
                });
            }
        });
    });
</script>

