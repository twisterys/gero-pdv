@php use Illuminate\Support\Carbon; @endphp
@php
    $table_height = 800;
    $table_height-= $o_template->image_en_tete_hauteur;
    $table_height-= $o_template->image_en_bas_hauteur;
    if ($o_achat->note != null){
        $table_height-= 70;
    }
@endphp
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
<div id="bg"></div>
<div id="footer-bg">
</div>
<div id="header-bg"></div>
<div class="content " style="padding-top: 2rem;width:100%;">
    <div class="">
        <div class="to_address">
            <br>
            <br>
            <table style="width: 100%; padding-left:0px;">
                <tr>
                    <td style="white-space: nowrap; vertical-align: top">Fournisseur:</td>
                    <td style="text-align: right;font-weight: bold;text-transform: capitalize">{{$o_achat->fournisseur->nom}}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; vertical-align: top">ICE:</td>
                    <td style="text-align: right">{{$o_achat->fournisseur->ice}}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; vertical-align: top">Adresse:</td>
                    <td style="text-align: right"> {{$o_achat->fournisseur->adresse}}</td>
                </tr>
            </table>
        </div>
        <div class="side-left">
            <div><h1 style="margin: 0">@lang('achats.'.$type)</h1></div>
            <table style="width: 50%">
                <tr>
                    <td style="width: 100%">Référence:</td>
                    @if($type ==='bca')
                        <td style="text-align:right;">{{$o_achat->reference_interne ?? 'Brouillon'}}</td>
                    @else
                        <td style="text-align:right;">{{$o_achat->reference ?? 'Brouillon'}}</td>
                    @endif
                </tr>
                <tr>
                    <td>@lang('achats.'.$type.'.date_emission')</td>
                    <td style="text-align:right;">{{$o_achat->date_emission}}</td>
                </tr>
                @if(in_array($type,['dva','faa','fpa','bca']))
                    <tr>
                        <td>@lang('achats.'.$type.'.date_expiration')</td>
                        <td style="text-align:right;">{{$o_achat->date_expiration}}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
    <div style="clear: both"></div>
    <div class="col-md-12 "
         style="overflow: clip; margin-top: 1rem;margin-bottom: 1rem">
        <table id="table-articles" style="height: {{$table_height}}px;max-height: {{$table_height}}px;"
               class="table w-100">
            <thead>
            <tr class="text-white thead" style="background-color: {{$o_template->couleur}}">
                @if( $o_achat['total_reduction'] == 0)
                <th colspan="2" class="text-white " style="border-bottom-left-radius: .25rem;border-top-left-radius: 0.25rem">
                    Désignation
                </th>
            @endif
            @if( $o_achat['total_reduction'] != 0)
            <th class="text-white " style="border-bottom-left-radius: .25rem;border-top-left-radius: 0.25rem">
                Désignation
            </th>
            @endif

                <th>Nombre</th>
                <th>Prix (HT)</th>



            @if( $o_achat['total_reduction'] != 0)
            <th>Réduction</th>
            @endif
                <th>TVA (%)</th>
                <th style="width: 100px !important; border-bottom-right-radius: .25rem;border-top-right-radius: 0.25rem"    >
                    Total TTC
                </th>
            </tr>
            </thead>
            <tbody id="items">
            @foreach($o_achat->lignes as $ligne)
                @php
                    if ($ligne['mode_reduction'] === 'fixe') {
                            $reduction = $ligne['reduction'];
                        } else if ($ligne['mode_reduction'] === 'pourcentage') {
                            $reduction = $ligne['ht'] * (($ligne['reduction'] ?? 0) / 100);
                        }
                @endphp
                <tr class="items" style="width:100%; margin:0px;">

                    @if( $o_achat['total_reduction'] == 0)
                    <td colspan="2" style='vertical-align: top; word-wrap: break-word;'>
                        <b style="">{{$ligne->nom_article}}</b>

                        <div style='vertical-align: top; word-wrap: break-word; max-width: 100% ;'>
                            {!! htmlspecialchars_decode(nl2br(e($ligne->description)),ENT_QUOTES) !!}
                        </div>
                    </td>
                    @endif
                    @if( $o_achat['total_reduction'] != 0)
                    <td style='vertical-align: top; word-wrap: break-word;'>
                        <b style="">{{$ligne->nom_article}}</b>

                        <div style='vertical-align: top; word-wrap: break-word; max-width: 100% ;'>
                            {!! htmlspecialchars_decode(nl2br(e($ligne->description)),ENT_QUOTES) !!}
                        </div>
                    </td>
                    @endif


                    <td style='vertical-align: top;text-align: center;'>
                        {{$ligne->quantite}} {{$ligne->unite->nom}}
                    </td>
                    <td style='vertical-align: top;text-align: center;'>{{number_format($ligne->ht,2,'.',' ')}} </td>



                    @if( $o_achat['total_reduction'] != 0)
                    <td style='vertical-align: top;text-align: center;'>{{number_format($ligne->reduction,2,'.',' ')}} {{$ligne->mode_reduction === 'fixe' ? ' ':'%'}}</td>
                    @endif
                    <td style='vertical-align: top;text-align: center;'>{{$ligne->taxe}} %</td>
                    <td style="padding-right:1.2cm;vertical-align: top;">{{number_format(  (($ligne->ht-$reduction) *$ligne->quantite) + (($ligne->ht-$reduction) *$ligne->quantite * $ligne->taxe/100)  ,2,'.',' ')}} </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="6" style="height: {{160 + ($o_achat->note ? 70 : 0)}}px;">
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <div style="clear: both"></div>
    <div class="col-md-12 "
         style="page-break-inside: avoid; position: fixed; bottom: {{$o_template->image_en_bas_hauteur-100-($o_achat->note ? 0 : 40)}}px; height: {{310 + ($o_achat->note ? 70 : 0)}}px;width: 100%">
        <div class="total-container " style="page-break-inside: avoid;">
            <div class="total-line">
                                <h5>Total HT</h5> <h5>{{number_format($o_achat->total_ht,2,'.',' ')}} MAD</h5>

            </div><hr>
            @if( $o_achat['total_reduction'] == 0)
            @endif
            @if( $o_achat['total_reduction'] != 0)
                <div class="total-line">
                <h5>Réduction</h5> <h5>{{number_format($o_achat->total_reduction,2,'.',' ')}} MAD</h5>
            </div><hr>
            @endif
            <div class="total-line">
                <h5>Total TVA</h5> <h5>{{number_format($o_achat->total_tva,2,'.',' ')}} MAD</h5>
            </div><hr>
            <div class="total-line">
                <h5>Total TTC</h5>
                <h2>{{number_format($o_achat->total_ttc,2,'.',' ')}} MAD</h2>
            </div>
        </div>
        @if($o_template['afficher_total_en_chiffre'])
            <div class="col-6" style="">
                @php
                    $formatter = new NumberFormatter('fr_FR', NumberFormatter::SPELLOUT);

        // Format the number in French words
        $inFrenchWords = $formatter->format($o_achat->total_ttc);

                @endphp
                <p>Veuillez arrêter la présente {{strtolower(__('achats.'.$type))}} à la somme en lettres de
                    <b>{{$inFrenchWords}} Dirhams.</b> <br>
                    Toutes taxes comprises en chiffres <b>({{$o_achat->total_ttc}} MAD TTC)</b>.</p>
            </div>
        @endif
        <div style="clear: both"></div>
        @if($o_achat->note)
            <div style="page-break-inside: avoid;height: 70px; overflow:clip;">
                <h3 style="margin-bottom: 0">Note</h3>
                <hr>
                <p>{!! htmlspecialchars_decode(nl2br(e($o_achat->note)),ENT_QUOTES) !!}</p>
            </div>
        @endif
    </div>

    <table class="signature" style="page-break-inside: avoid; position: fixed; bottom: {{$o_template->image_en_bas_hauteur-100-($o_achat->note ? 0 : 40)}}px; height: {{160 + ($o_achat->note ? 70 : 0)}}px;width: 100%">
        <tr>
            <th><u>Signature de client</u></th>
            <th><u>Cachet & Signature</u></th>
        </tr>

    </table>
</div>
</body>
</html>
<style>
    :root {
        font-size: 11px;
    }

    @page {
        margin: {{$o_template->image_en_tete_hauteur ? $o_template->image_en_tete_hauteur+20 : 0}}px 1.5cm {{$o_template->image_en_bas_hauteur ??0}}px 1.5cm;
        size: A4;
    }


    body {
        font-family: 'Rubik', sans-serif;
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
    }

    #bg {
        position: fixed;
        top: -{{$o_template->image_en_tete_hauteur ? $o_template->image_en_tete_hauteur+20 : 0}}px;
        left: -1.5cm;
        right: -1.5cm;
        z-index: -1;
        bottom: -{{$o_template->image_en_bas_hauteur ??0}}px;
        @if($o_template['image_arriere_plan'] !='')
            background-image: url('{{$images['image_arriere_plan']}}');
        @endif
        background-size: cover;
        background-repeat: no-repeat;
    }

    #footer-bg {
        position: fixed;
        left: -1.5cm;
        right: -1.5cm;
        z-index: -1;
        height: {{$o_template->image_en_bas_hauteur??0}}px;
        bottom: -{{$o_template->image_en_bas_hauteur??0}}px;
        @if($o_template['image_en_bas'] !='' )
            background-image: url('{{$images['image_en_bas']}}');
        @endif
            background-size: cover;
        background-repeat: no-repeat;
    }

    #header-bg {
        position: fixed;
        left: -1.5cm;
        right: -1.5cm;
        z-index: -1;
        height: {{$o_template->image_en_tete_hauteur ?? 0}}px;
        top: -{{$o_template->image_en_tete_hauteur ? $o_template->image_en_tete_hauteur+20 : 0}}px;
        @if($o_template['image_en_tete'] !='')
            background-image: url('{{$images['image_en_tete']}}');
        @endif
            background-size: cover;
        background-repeat: no-repeat;
    }

    p {
        margin: 0 !important;
    }

    table {
        border-spacing: 0;
        border-collapse: collapse;
        position: relative;
        z-index: 2;
    }

    .to_address {
        float: right;
        width: 47%;
    }


    .capitalize {
        text-transform: uppercase
    }

    .content {
        padding: 0rem;
        width: 100%;
    }
</style>
<style>
    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 0;
    }

    #table-articles {
        border-radius: 0.25rem;
        position: relative;
        border-collapse: collapse;
        overflow: hidden;
        width: 100%;
    }

    #table-articles td {
        position: relative;
    }

    thead tr th{
        border: 1px solid white;
    }
    hr {
        border: none;
        border-top: 1px solid rgba(0, 0, 0, .1);
    }


    #table-articles th {
        background-color: {{$o_template->couleur}};
        color: rgb(255, 255, 255);
        font-weight: 400;
        padding: 0.75rem;
    }

    #table-articles th:nth-child(2) {
        padding: 0 1rem;
        width: 1% !important;
        white-space: nowrap !important;
    }

    #table-articles th:nth-child(3) {
        padding: 0 1rem;
        width: 1% !important;
        white-space: nowrap !important;
    }

    #table-articles th:nth-child(4) {
        padding: 0 1rem;
        width: 1% !important;
        white-space: nowrap !important;
    }

    #table-articles th:nth-child(5) {
        padding: 0 1rem;
        width: 1% !important;
        white-space: nowrap !important;
    }

    #table-articles th:nth-child(6) {
        padding: 0 1rem;
        width: 1% !important;
        white-space: nowrap !important;
    }

    #table-articles > tbody > tr > * {
        padding: 0.75rem;
        color: #495057;
        border-bottom-width: 1px;
    }

    #table-articles > tfoot > tr:nth-of-type(odd) > * {
        background-color: #f8f9fa;
    }

    #table-articles > tfoot > tr > * {
        background-color: #fff;
        padding: 0.75rem;
        color: #495057;
        border-bottom-width: 1px;
    }

    #table-articles > tbody > tr > td:nth-child(6) {
        text-align: right;
    }

    #table-articles > tbody > tr > td:nth-child(5) {
        text-align: right;
    }

    #table-articles > tbody > tr > td:nth-child(4) {
        text-align: right;
    }

    #table-articles > tbody > tr > td:nth-child(3) {
        text-align: right;
    }

    #table-articles > tbody > tr > td:nth-child(2) {
        text-align: right;
    }

    #total-group {
        margin-top: 2rem;
        display: flex;
        justify-content: space-between;
        padding: 1.2rem;
    }

    #total-content {
        display: flex;
        margin: 0;
        background-color: {{$o_template->couleur}};
        padding: 2rem;
        border-radius: .25rem;
        color: white !important;
        page-break-inside: avoid;
        position: absolute;
    }
</style>
<style>
    .total-container {
        border:1px solid {{$o_template->couleur}};
        overflow: hidden;
        border-radius: .25rem;
        max-width: 500px !important;
        float: right;
        width: 45%;
        color:black;
    }


    .total-line {
        /* margin-bottom: .25rem; */
        padding: .25rem .5rem;
    }
    .total-line:last-child{
        background-color: {{$o_template->couleur}};
        color : #fff;
    }


    .total-line:last-child {
        margin: 0;

        line-height: 14px;
        padding-top: 7px;

    }

    .total-line h5,.total-line:last-child h2 {
        display: inline-block;
        color: #000000;
        margin: 0;
        font-weight: 400;
        line-height: 14px;
        padding-top: 7px;

    }

    .total-line h5:last-child {
        float: right
    }

    .total-line h2 {
        float: right;
        margin: 0;
        color: #fff;

    }

    .total-line:last-child h5 {
        margin-bottom:10px;
    }
    .total-line:last-child * {
        color:#fff
    }
    h5{
        font-size: 14px;
        line-height: 14px;
        padding-top: 7px;
    }
    hr{
        margin-bottom: 0px;
        padding-bottom: 0px;
    }
</style>

