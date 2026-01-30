<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-primary rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total des achats TTC
 :</span>
    <span class="ms-3 text-dark">{{ number_format($achat_ca, 3, '.',' ') }}  DH</span>
</div>

<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-info rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total des achats HT
 :</span>
    <span class="ms-3 text-dark">{{ number_format($achat_ca_ht, 3, '.', ' ') }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-success rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Total décaissements achats TTC:</span>
    <span class="ms-3 text-dark">{{ number_format($achat_recette, 3, '.', ' ') }} DH</span>
</div>
<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-warning rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Dettes TTC
:</span>
    <span class="ms-3 text-dark">{{ number_format($achat_creance, 3, '.', ' ') }} DH</span>
</div>

<div class="ms-6 my-4 pe-5 d-flex justify-content-between bg-soft-danger rounded p-3">
    <span class="font-weight-bolder text-dark font-size-sm">Dettes cumulées avant le {{ \Carbon\Carbon::parse($first_day_of_the_year)->format('d/m/Y') }}
:</span>
    <span class="ms-3 text-dark">{{ number_format($cumulated_achat_creance, 3, '.', ' ') }} DH</span>
</div>
