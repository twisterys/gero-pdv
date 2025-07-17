<div class="card-title me-5   justify-content-between align-items-center">
    <h5>Valeurs d'achats</h5>
    <hr class="border">
    <div id="total_achats" class="text-danger">
        {{ $stock_achats }} DH
    </div>
</div>
<div class="card-title me-5 justify-content-between align-items-center">
    <h5>Valeurs de vente</h5>
    <hr class="border">
    <div id="total_ventes" class="text-success">
        {{ $stock_ventes }} DH
    </div>
</div>
<div class="card-title me-5 justify-content-between align-items-center">
    <h5>Bénéfice potentiel</h5>
    <hr class="border">
    <div id="total_benefice" class="text-warning">
        {{ $benifice }} DH
    </div>
</div>
<div class="card-title justify-content-between align-items-center">
    <h5>Profit Margin %</h5>
    <hr class="border">
    <div id="proft_margin" class="text-info">
        {{ $profit }}%
    </div>
</div>
