@php use Illuminate\Support\Carbon; @endphp
@php
    $table_height = 100;
    $table_height-= $o_template->image_en_tete_hauteur;
    $table_height-= $o_template->image_en_bas_hauteur;
    if ($o_vente->note != null){
        $table_height-= 70;
    }
@endphp
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $o_vente->client->nom }} {{ $o_vente->date_emission }} {{ $o_vente->reference }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
</head>
<body>
<div id="bg"></div>
<div id="footer-bg">
</div>
<div id="header-bg"></div>
<div class="content" style="padding-top: 2rem">
    <div class="">
        <div class="to_address">
            <br>
            <br>
            <table style="width: 100%">
                <tr>
                    <td style="white-space: nowrap; vertical-align: top">Adressé à :</td>
                    <td style=" text-align: right;font-weight: bold;text-transform: capitalize">{{$o_vente->client->nom}}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; vertical-align: top">ICE:</td>
                    <td style="text-align: right">{{$o_vente->client->ice}}</td>
                </tr>

                <tr>
                    <td style="vertical-align: top">Adresse:</td>
                    <td style="text-align: right; vertical-align: top"> {{$o_vente->client->adresse}}</td>
                </tr>
            </table>
        </div>
        <div>
            @if($type == 'fp')
                <h1 style="margin: 0">@lang('ventes.fa')</h1>
            @else
                <h1 style="margin: 0">@lang('ventes.' . $type)</h1>
            @endif
            <table style="width: 100%">
                <tr>
                    <td style="width: 40%">Référence</td>
                    <td>{{$o_vente->reference ?? 'Brouillon'}}</td>
                </tr>
                <tr>
                    <td>@lang('ventes.'.$type.'.date_emission')</td>
                    <td>{{$o_vente->date_emission}}</td>
                </tr>
                @if(in_array($type,['dv','fa','fp','bc']))
                    <tr>
                        <td>@lang('ventes.'.$type.'.date_expiration')</td>
                        <td>{{$o_vente->date_expiration}}</td>
                    </tr>
                @endif
            </table>
        </div>
    </div>
    <div style="clear: both"></div>
    <div class="col-md-12 "
         style="overflow: clip; margin-top: 1rem;margin-bottom: 1rem">
        <table id="table-articles"
               style="height: {{$table_height}}px;max-height: {{$table_height}}px"
               class="table w-100">
            <thead>
            <tr class="text-white" style="background-color: {{$o_template->couleur}}">
                <th class="text-white " style="border-bottom-left-radius: .25rem;border-top-left-radius: 0.25rem">
                    Article
                </th>
                <th>Quantité</th>
                <th>Prix HT (MAD)</th>
                <th> Réduction HT</th>
                <th>TVA (%)</th>
                <th style="width: 100px !important; border-bottom-right-radius: .25rem;border-top-right-radius: 0.25rem">
                    Total HT
                </th>
            </tr>
            </thead>
            <tbody id="items">
            @foreach($o_vente->lignes as $ligne)
                @php
                    if ($ligne['mode_reduction'] === 'fixe') {
                            $reduction = $ligne['reduction'];
                        } else if ($ligne['mode_reduction'] === 'pourcentage') {
                            $reduction = $ligne['ht'] * (($ligne['reduction'] ?? 0) / 100);
                        }
                @endphp
                <tr class="items">
                    <td style='vertical-align: top; word-wrap: break-word;'>
                        <b>{{$ligne->nom_article}}</b>
                        <div style='vertical-align: top; word-wrap: break-word; max-width: 100%'>
                            {!! htmlspecialchars_decode(e($ligne->description),ENT_QUOTES) !!}
                        </div>
                    </td>
                    <td style="white-space: nowrap">{{$ligne->quantite}} {{$ligne->unite->nom}}</td>
                    <td style="white-space: nowrap">{{number_format($ligne->ht,3,'.',' ')}} MAD</td>
                    <td style="white-space: nowrap">{{number_format($ligne->reduction,3,'.',' ')}} {{$ligne->mode_reduction === 'fixe' ? 'MAD':'%'}}</td>
                    <td>{{$ligne->taxe}} %</td>
                    <td style="white-space: nowrap">{{number_format(($ligne->ht-$reduction) *$ligne->quantite,3,'.',' ')}}
                        MAD
                    </td>
                </tr>
            @endforeach
{{--            <tr>--}}
{{--                <td colspan="6" style="height: {{160 + ($o_vente->note ? 70 : 0)}}px;">--}}
{{--                </td>--}}
{{--            </tr>--}}
            </tbody>
        </table>
    </div>
    <div style="clear: both"></div>
    <div class="col-md-12 "
         style="page-break-inside: avoid;
          width: 100%">
        <div class="total-container " style="page-break-inside: avoid">
            <div class="total-line">
                <h5>Total HT</h5> <h5>{{number_format($o_vente->total_ht,3,'.',' ')}} MAD</h5>

            </div>
            <div class="total-line">
                <h5>Total Réduction</h5> <h5>{{number_format($o_vente->total_reduction,3,'.',' ')}} MAD</h5>
            </div>
            <div class="total-line">
                <h5>Total TVA</h5> <h5>{{number_format($o_vente->total_tva,3,'.',' ')}} MAD</h5>
            </div>
            @if($o_vente->solde == 0 || $o_vente->encaisser <= 0)
                <div class="total-line">
                    <h5>Total TTC</h5>
                    <h2>{{ number_format($o_vente->total_ttc, 3, '.', ' ') }} MAD</h2>
                </div>
            @endif
            @if($o_vente->solde != 0 && $o_vente->encaisser> 0)
                <div class="total-line">
                    <h5>Total TTC</h5>
                    <h5>{{number_format($o_vente->total_ttc,3,'.',' ')}} MAD</h5>
                </div>
                <div class="total-line avance">
                    <h5>Avance</h5>
                    <h5>{{ number_format($o_vente->encaisser, 3, '.', ' ') }} MAD</h5>
                </div>
                <div class="total-line">
                    <h5>Net à payer</h5>
                    <h5>{{ number_format($o_vente->solde, 3, '.', ' ') }} MAD</h5>
                </div>
            @endif
        </div>
        @if($o_template['afficher_total_en_chiffre'])
            <div class="col-6">
                @php
                    $formatter = new NumberFormatter('fr_FR', NumberFormatter::SPELLOUT);

        // Format the number in French words
        $inFrenchWords = $formatter->format($o_vente->total_ttc);

                @endphp
                <p>Veuillez arrêter la présente {{strtolower(__('ventes.'.$type))}} à la somme en lettres de
                    <b>{{$inFrenchWords}} MAD.</b> <br>
                    Toutes taxes comprises en chiffres <b>({{$o_vente->total_ttc}} MAD TTC)</b>.</p>
            </div>
        @endif
        <div style="clear: both"></div>
        @if($o_vente->note)
            <div style="page-break-inside: avoid;height: 70px; overflow:clip;">
                <h3 style="margin-bottom: 0">Note</h3>
                <hr>
                <p>{!! htmlspecialchars_decode(nl2br(e($o_vente->note)),ENT_QUOTES) !!}</p>
            </div>
        @endif
    </div>
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
        top: -3cm;
        left: -1.5cm;
        right: -1.5cm;
        z-index: -1;
        bottom: -3cm;
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
        padding: 1rem;
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
    }

    #table-articles td {
        position: relative;
    }

    hr {
        border: none;
        border-top: 1px solid rgba(0, 0, 0, .1);
    }

    /*#table-articles tr:last-child  td::before {*/
    /*    content: '';*/
    /*    position: absolute;*/
    /*    top: 0; !* Adjust this value based on your row height *!*/
    /*    height: 10000px;*/
    /*    !*bottom: -500%; !* Adjust this value based on your row height *!*!*/
    /*    left: 0;*/
    /*    width: 1px;*/
    /*    background-color: rgba(0,0,0,.08);*/
    /*    transform: translateX(-50%);*/
    /*}*/

    /*#table-articles  td {*/
    /*    border-left: 1px solid rgba(0,0,0,.08);*/
    /*    border-right: 1px solid rgba(0,0,0,.08);*/
    /*}*/
    /*#table-articles  td:first-child{*/
    /*    border-left: 0;*/
    /*}*/
    /*#table-articles  td:last-child{*/
    /*    border-right: 0;*/
    /*}*/
    /*#table-articles tr:last-child td {*/
    /*    border-left: 0;*/
    /*    border-right: 0;*/
    /*}*/
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
        {{--bottom: {{700-((+$o_document_parametres['espace_en_tete']?? 0)*37)-((+$o_document_parametres['espace_pied_page']?? 0)*37)}};--}}
          position: absolute;
    }
</style>
<style>
    .total-container {
        background-color: {{$o_template->couleur}};
        padding: 1rem;
        overflow: hidden;
        border-radius: .25rem;
        max-width: 500px !important;
        float: right;
        width: 50%;
    }

    .total-line {
        margin-bottom: .25rem
    }

    .total-line:last-child {
        margin: 0
    }

    .total-line h5 {
        display: inline-block;
        color: #fff;
        margin: 0;
        font-weight: 400;

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
        margin-bottom: 10px;
    }
</style>

