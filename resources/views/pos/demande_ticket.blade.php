<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * {
            font-family: Tahoma !important;
        }
    </style>
</head>

<body style="background-color: transparent">
<br>
<div class="content">
    <div class="">
        <div class="to_address">
            <br>
            <br>
            <table  style="width: 100%">
                <tr>
                    <td style="white-space: nowrap; vertical-align: top" >Demander par :</td>
                    <td style=" text-align: right;font-weight: bold;text-transform: capitalize">{{$o_demande_transfert->magasin_entree->nom}}</td>
                </tr>
                <tr>
                    <td style="white-space: nowrap; vertical-align: top" >Demander de :</td>
                    <td style="text-align: right">{{$o_demande_transfert->magasin_sortie->nom}}</td>
                </tr>
            </table>
        </div>
        <div>
            <div><h1 style="margin: 0">Demande de transfert</h1></div>
            <table >
                <tr>
                    <td style="width: 40%">Référence</td>
                    <td>{{$o_demande_transfert->reference}}</td>
                </tr>
                <tr>
                    <td style="width: 40%">Date de livration</td>
                    <td>{{now()->format('d/m/Y H:i:s')}}</td>
                </tr>

            </table>
        </div>
    </div>
    <div style="clear: both"></div>
    <br>
    <br>
    <table id="table-articles"
           class="table w-100">
        <thead>
        <tr class="text-white" style="background-color: #495057">
            <th class="text-white">Produit</th>
            <th class="text-white">Quantité demandée</th>
            <th class="text-white">Quantité livré</th>
        </tr>
        </thead>
        <tbody id="items">
            @foreach($o_demande_transfert->lignes as $ligne)
                <tr class="items">
                    <td>{{$ligne->article->reference}} | {{$ligne->article->designation}}</td>
                    <td>{{$ligne->quantite_demande}}</td>
                    <td>{{$ligne->quantite_livre}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br>
<br>
</body>
</html>

<style>
    :root {
        font-size: 13px;
    }

    @page {
        margin:  1.5cm;
        size: A4;
    }

    body {
        overflow-x: hidden;
        overflow-y: auto;
        position: relative;
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


    hr {
        border: none;
        border-top: 1px solid rgba(0, 0, 0, .1);
    }
    #table-articles th {
        background-color: #495057;
        color: rgb(255, 255, 255);
        font-weight: 400;
        padding: 0.75rem;
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

</style>
