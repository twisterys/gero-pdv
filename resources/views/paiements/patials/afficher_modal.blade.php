<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
        Paiement de {{ $o_paiement->payable->reference ?? '---' }}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <div class=" col-sm-6 d-flex align-items-center mt-3">
            <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                 style="width: 49px">
                <i class="fa fa-calendar-alt fa-2x"></i>
            </div>
            <div class="ms-3 ">
                <span class="font-weight-bolder font-size-sm">Date</span>
                <p class="mb-0 h5 text-black">{{$o_paiement->date_paiement}}</p>
            </div>
        </div>
        <div class=" col-sm-6 d-flex align-items-center mt-3">
            <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
                 style="width: 49px">
                <i class="fa fa-building fa-2x"></i>
            </div>
            <div class="ms-3 ">
                <span class="font-weight-bolder font-size-sm">Compte</span>
                <p class="mb-0 h5 text-black">{{$o_paiement->compte->nom}}</p>
            </div>
        </div>
        <div class=" col-sm-6 d-flex align-items-center mt-3">
            <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
                 style="width: 49px">
                <i class="fa fa-credit-card fa-2x"></i>
            </div>
            <div class="ms-3 ">
                <span class="font-weight-bolder font-size-sm">Méthode de paiement</span>
                <p class="mb-0 h5 text-black">{{$o_paiement->methodePaiement->nom}}</p>
            </div>
        </div>
        <div class=" col-sm-6 d-flex align-items-center mt-3">
            <div class="rounded bg-soft-primary  p-2 d-flex align-items-center justify-content-center"
                 style="width: 49px">
                <i class="fa fa-cash-register fa-2x"></i>
            </div>
            <div class="ms-3 ">
                <span class="font-weight-bolder font-size-sm">Montant</span>
                <p class="mb-0 h5 text-black">{{number_format(abs($o_paiement->encaisser - $o_paiement->decaisser),3,'.',' ')}}</p>
            </div>
        </div>
        @if($o_paiement->methodePaiement->key === "cheque" || $o_paiement->methodePaiement->key === "lcn")
            <div class=" col-sm-6 d-flex align-items-center mt-3">
                <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
                     style="width: 49px">
                    <i class="fa fa-money-check fa-2x"></i>
                </div>
                <div class="ms-3 ">
                    <span class="font-weight-bolder font-size-sm">Référence de chèque</span>
                    <p class="mb-0 h5 text-black">{{$o_paiement->cheque_lcn_reference}}</p>
                </div>
            </div>
        @endif
    </div>
    <h5 class="mt-4" >Note:</h5>
    <hr >
    <div class="mt-2 bg-light rounded p-2">
        {{$o_paiement->note ?? '--'}}
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
</div>
