<div class="row m-0 px-2 mb-3">
    <div class=" col-xl-3 col-md-6  my-2    d-flex align-items-center">
        <div class="rounded bg-soft-warning  p-2 d-flex align-items-center justify-content-center"
             style="width: 49px">
            <i class="fa fa-money-bill text-warning fa-2x"></i>
        </div>
        <div class="ms-3 ">
            <span class="font-weight-bolder font-size-sm">Solde d'ouverture</span>
            <p class="mb-0 h5 text-black">{{number_format($total_ouverture,2,'.',' ')}} MAD</p>
        </div>
    </div>
    <div class=" col-xl-3 col-md-6  my-2   d-flex align-items-center">
        <div class="rounded bg-soft-info  p-2 d-flex align-items-center justify-content-center"
             style="width: 49px">
            <i class="fa fa-dollar-sign text-info fa-2x"></i>
        </div>
        <div class="ms-3 ">
            <span class="font-weight-bolder font-size-sm">Solde de clôture</span>
            <p class="mb-0 h5 text-black">{{number_format($total_actuel,2,'.',' ')}} MAD</p>
        </div>
    </div>
    <div class=" col-xl-3 col-md-6  my-2   d-flex align-items-center">
        <div class="rounded bg-soft-success  p-2 d-flex align-items-center justify-content-center"
             style="width: 49px">
            <i class="fa fa-credit-card text-success fa-2x"></i>
        </div>
        <div class="ms-3 ">
            <span class="font-weight-bolder font-size-sm">Total encaissé</span>
            <p class="mb-0 h5 text-black">{{number_format($total_encaisser,2,'.',' ')}} MAD</p>
        </div>
    </div>
    <div class=" col-xl-3 col-md-6  my-2   d-flex align-items-center">
        <div class="rounded bg-soft-danger  p-2 d-flex align-items-center justify-content-center"
             style="width: 49px">
            <i class="fa fa-credit-card text-danger fa-2x"></i>
        </div>
        <div class="ms-3 ">
            <span class="font-weight-bolder font-size-sm">Total décaissé</span>
            <p class="mb-0 h5 text-black">{{number_format($total_decaisser,2,'.',' ')}} MAD</p>
        </div>
    </div>
</div>
