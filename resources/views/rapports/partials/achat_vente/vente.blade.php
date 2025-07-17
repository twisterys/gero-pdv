<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-light rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total TTC :</span>
    <span class="ms-3 text-dark">{{ $ventestotalTTC }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-success rounded p-3">
    <span class="font-weight-bolder text-dark text-dark font-size-sm">Total HT :</span>
    <span class="ms-3 text-dark">{{ $ventestotalHT }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-light rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Ventes payés :</span>
    <span class="ms-3 text-dark">{{ $ventetotalEncaisser }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-success rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Ventes dû :</span>
    <span class="ms-3 text-dark">{{ $ventestotalTTC - $ventetotalEncaisser }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-light rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Retour des ventes :</span>
    <span class="ms-3 text-dark">{{ $totalAv }} DH</span>
</div>
