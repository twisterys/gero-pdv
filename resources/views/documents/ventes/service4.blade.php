@php use Illuminate\Support\Carbon; @endphp
@php
    $table_height = 800;
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
    <style>
        #info-table {
            width: 100%;
        }

        .reference-container {
            padding: 1rem;
            border: 1px solid #000;
            border-radius: .7rem;
            text-align: center;
            font-size: 1.4rem;
            font-weight: 500;
        }
        .info-container {
            padding: 1rem;
            border: 1px solid #000;
            border-radius: .7rem;
            font-size: 1.4rem;
            font-weight: 500;
        }
        #table-articles {
            width: 100%;
        }
        #table-articles thead tr th .border {
            border: 1px solid #000 !important;
            border-radius: 1rem !important;
            padding: .5rem;
        }
        #table-articles tbody {
            border-collapse: collapse;
        }
        #table-articles tbody tr td {
            padding: 0 .1rem !important;
            margin: 0 !important;
        }
        #table-articles tbody tr td .border{
            border-left: 1px solid #000 !important;
            border-right: 1px solid #000 !important;
            padding: .5rem;
        }
        #table-articles tbody tr:nth-last-child(2) td .border {
            border-bottom: 1px solid #000;
            border-bottom-left-radius: 10px ;
            border-bottom-right-radius: 10px ;
        }
        #table-articles tbody tr:first-child td .border {
            border-top: 1px solid #000;
            border-top-left-radius: 10px ;
            border-top-right-radius: 10px ;
        }
        #total-group {
            margin-top: 2rem;
            display: flex;
            justify-content: space-between;
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
        .total-container {
            border:1px solid  {{$o_template->couleur}};
            overflow: hidden;
            border-radius: .25rem;
            max-width: 500px !important;
            float: right;
            width: 50%;
        }



        .total-line {
            padding: 0.5rem
        }

        .total-line h5 ,.total-line h2{
            display: inline-block;
            color: #000;
            margin: 0;
            font-weight: 400;

        }

        .total-line h5:last-child {
            float: right;
        }
        .total-line:last-child{
            background-color: {{$o_template->couleur}};
            color:white;
        }

        .total-line h2 {
            float: right;
            margin: 0;
            color: #000000;
        }

        .total-line:last-child h5 {
            padding-bottom: 10px;
            color:rgb(0, 0, 0);
        }
    </style>
</head>
<body>
<div id="bg"></div>
<div id="logo"></div>
<div id="footer-bg">
</div>
<div id="header-bg"></div>
<table id="info-table"   >
    <tr>
        <td style="width: 50%;padding-right: 1rem;" >
            <p class="reference-container">
                @lang('ventes.'.$type) : {{$o_vente->reference ?? 'Brouillon'}}
            </p>
            <p style="padding: .5rem 0; text-align: center" >@lang('ventes.'.$type.'.date_emission'):{{$o_vente->date_emission}}/ @if(in_array($type,['dv','fa','fp','bc']))
                    @lang('ventes.'.$type.'.date_expiration')
                    {{$o_vente->date_expiration}}
                @endif</p>
        </td>
        <td style="width: 50%; padding-left: 1rem" >
            <div class="info-container">
                <p>Client: {{$o_vente->client->nom}}</p>
                <p>{{$o_vente->client->adresse}}</p>
                <p>ICE :{{$o_vente->client->ice}}</p>
                <p>Tél : {{$o_vente->client->telephone}}</p>
            </div>
        </td>
    </tr>
</table>
<div class="content" style="padding-top: 2rem">
    <div style="clear: both"></div>
    <div class="col-md-12 "
         style="overflow: clip; margin-top: 1rem;margin-bottom: 1rem">
        <table id="table-articles" style="height: {{$table_height}}px;max-height: {{$table_height}}px"
               class="table w-100">
            <thead>
            {{-- style="background-color: {{$o_template->couleur}}" --}}
            <tr>
                @if( $o_vente['total_reduction'] == 0)
                    <th colspan="2" class="text-white " style="border-bottom-left-radius: .25rem;border-top-left-radius: 0.25rem">
                        <div class='border'>
                            Désignation
                        </div>
                    </th>
                @endif
                @if( $o_vente['total_reduction'] != 0)
                    <th>
                        <div class='border'>
                            Désignation
                        </div>
                    </th>
                @endif

                <th><div class="border">Nombre</div> </th>
                <th><div class="border">Prix HT</div> </th>
                @if( $o_vente['total_reduction'] != 0)
                    <th><div class="border">Réduction</div> </th>

                @endif
                <th><div class="border">TVA (%)</div> </th>
                <th style="width: 100px !important;">
                    <div class="border">Total HT</div>
                </th>
            </tr>
            </thead>
            <tbody id="items">
            @foreach($o_vente->lignes as $index => $ligne)
                @php
                    if ($ligne['mode_reduction'] === 'fixe') {
                            $reduction = $ligne['reduction'];
                        } else if ($ligne['mode_reduction'] === 'pourcentage') {
                            $reduction = $ligne['ht'] * (($ligne['reduction'] ?? 0) / 100);
                        }


                @endphp

                <tr  style="width:100%;">
                    @if( $o_vente['total_reduction'] == 0)
                        <td colspan="2"  style='vertical-align: top; word-wrap: break-word ;text-align:center; '>
                            <div class="border" ><b> {{$ligne->nom_article}}</b>

                            </div>
                        </td>
                    @endif
                    @if( $o_vente['total_reduction'] != 0)
                        <td  style='vertical-align: top; word-wrap: break-word ;text-align:center; '>
                            <div class="border" ><b> {{$ligne->nom_article}}</b>

                            </div>
                        </td>
                    @endif

                    <td  style=";margin:0px;vertical-align: top; word-wrap: break-word ;">
                        <div class="border" >{{$ligne->quantite}} {{$ligne->unite->nom}}

                        </div>

                    </td>
                    <td  style="vertical-align: top; word-wrap: break-word ; white-space: nowrap"><div class="border" >{{number_format($ligne->ht,2,'.',' ')}} MAD

                        </div>
                    </td>
                    @if( $o_vente['total_reduction'] != 0)
                        <td  style="vertical-align: top; word-wrap: break-word ; white-space: nowrap"><div class="border" >{{number_format($ligne->reduction,2,'.',' ')}} {{$ligne->mode_reduction === 'fixe' ? 'MAD':'%'}}

                            </div></td>
                    @endif
                    <td  style="vertical-align: top; word-wrap: break-word ; ">
                        <div class="border" >{{$ligne->taxe}} %
                        </div></td>
                    <td  style="vertical-align: top; word-wrap: break-word ;white-space: nowrap "><div class="border" >{{number_format(($ligne->ht-$reduction) *$ligne->quantite,2,'.',' ')}}  MAD

                        </div></td>
                </tr>
            @endforeach
            <tr class="note">
                <td colspan="6" class="note" style="height: {{160 + ($o_vente->note ? 70 : 0)}}px;">
                </td>
            </tr>
            </tbody>
        </table>

    </div>
    <div style="clear: both"></div>
    <div class="col-md-12 total"
         style="page-break-inside: avoid; position: fixed; bottom: {{$o_template->image_en_bas_hauteur-100-($o_vente->note ? 0 : 150)}}px; height: {{310 + ($o_vente->note ? 70 : 60)}}px;width: 100%">
        <div class="total-container " style="page-break-inside: avoid">
            <div class="total-line">
                <h5>Total HT</h5> <h5>{{number_format($o_vente->total_ht ,2,'.',' ')}} MAD</h5>
            </div>
            <hr>
            @if( $o_vente['total_reduction'] == 0)
            @endif
            @if( $o_vente['total_reduction'] != 0)
                <div class="total-line">
                    <h5>Réduction</h5> <h5>{{$o_vente->total_reduction}} MAD</h5>
                </div><hr>

            @endif
            <div class="total-line">
                <h5>Total TVA</h5> <h5>{{$o_vente->total_tva}} MAD</h5>
            </div><hr>
            <div class="total-line">
                <h5 style="color: white">Total TTC</h5>
                <h2 style="color: #fff;">{{$o_vente->total_ttc}} MAD</h2>
            </div>
        </div>
        @if($o_template['afficher_total_en_chiffre'])
            <div class="col-6" >
                @php
                    $formatter = new NumberFormatter('fr_FR', NumberFormatter::SPELLOUT);

        // Format the number in French words
        $inFrenchWords = $formatter->format($o_vente->total_ttc);

                @endphp
                <p>Veuillez arrêter la présente {{strtolower(__('ventes.'.$type))}} à la somme en lettres de
                    <b>{{$inFrenchWords}} Dirhams.</b> <br>
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
    <table class="signature" style="page-break-inside: avoid; position: fixed; bottom: {{$o_template->image_en_bas_hauteur-100-($o_vente->note ? 0 : 150)}}px; height: {{160 + ($o_vente->note ? 70 : 80)}}px;width: 100%">
        <tr>
            <th><u>Signature de client</u></th>
            <th><u>Cachet & Signature</u></th>
        </tr>

    </table>
</div>
</body>
</html>

<style>
    hr{
        margin-bottom: 0px;
        padding-bottom: 0px;
        font-size: 10px;
    }
    :root {
        font-size: 11px;
    }

    @page {
        margin: {{$o_template->logo_hauteur ? $o_template->logo_hauteur+20 : 0}}px 1.5cm {{$o_template->image_en_bas_hauteur ??0}}px 1.5cm;
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
    #logo{
        position: fixed;
        margin-top:1.2cm;
        z-index: -1;
        height: {{$o_template->logo_hauteur ?? 0}}px;
        width:{{$o_template->logo_largeur ?? 0}}px;
        top: -{{$o_template->image_en_tete_hauteur ? $o_template->image_en_tete_hauteur+20 : 0}}px;
        @if($o_template['logo'] !='')
            background-image: url('{{$images['logo']}}');
        @endif
            background-size: contain;
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
    .capitalize {
        text-transform: uppercase
    }


</style>
<style>
    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 0;
    }
</style>
