<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-primary rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total des ventes TTC
 :</span>
    <span class="ms-3 text-dark">{{ number_format($vente_ca, 2, '.', ' ') }} DH</span>
</div>

<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-info rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total des ventes HT
 :</span>
    <span class="ms-3 text-dark">{{ number_format($vente_ca_ht, 2, '.', ' ') }} DH</span>
</div>


<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-success rounded p-3">
    <span class="font-weight-bolder text-dark text-dark font-size-sm">Total encaissements ventes TTC:</span>
    <span class="ms-3 text-dark">{{ number_format($vente_recette, 2, '.', ' ') }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-warning rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Créances TTC :</span>
    <span class="ms-3 text-dark">{{ number_format($vente_creance, 2, '.', ' ') }} DH</span>
</div>

<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-danger rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Créances cumulées avant le {{ \Carbon\Carbon::parse($first_day_of_the_year)->format('d/m/Y') }}  :</span>
    <span class="ms-3 text-dark">{{ number_format($cumulated_vente_creance, 2, '.', ' ') }} DH</span>
</div>

