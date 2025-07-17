<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-light rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total TTC :</span>
    <span class="ms-3 text-dark">{{ $totalTTC }}  DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-success rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total HT :</span>
    <span class="ms-3 text-dark">{{ $totalHT }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-light rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Achats payés :</span>
    <span class="ms-3 text-dark">{{ $totalCredit }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-success rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Achat dû :</span>
    <span class="ms-3 text-dark">{{ $totalTTC - $totalCredit }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-light rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Retour d'achat :</span>
    <span class="ms-3 text-dark">{{ $totalAva }} DH</span>
</div>
