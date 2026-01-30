<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        *{
            font-family: Tahoma !important;
        }
    </style>
</head>
<body>
<p style="text-align: center;">{{$pos_session->magasin->nom}}</p>
<p  style="text-align: center;">{{$pos_session->created_at->format('d/m/Y H:i:s')}}</p>
<p  style="text-align: center;">{{\Carbon\Carbon::now()->format('d/m/Y H:i:s')}}</p>
<div style="width: 100%; white-space: nowrap; overflow: hidden;">======================================================================================================</div>
<br>
{{--<table style="width: 100%">--}}
{{--    <thead>--}}
{{--    <tr>--}}
{{--        <th style="text-align:left" >Réf</th>--}}
{{--        <th style="text-align:left">Heu</th>--}}
{{--        <th style="text-align:right">Total</th>--}}
{{--    </tr>--}}
{{--    </thead>--}}
{{--    @foreach($pos_session->ventes as $vente)--}}
{{--        <tr>--}}
{{--            <td style="text-align:left">{{$vente->reference}}</td>--}}
{{--            <td style="text-align:left">{{$vente->created_at->format('H:i:s')}}</td>--}}
{{--            <td style="text-align:right">{{number_format($vente->total_ttc,3,'.',' ')}}</td>--}}
{{--        </tr>--}}
{{--    @endforeach--}}
{{--</table>--}}
<table style="border-collapse: collapse; width: 100%; height: 66px;" border="0">
    <thead>
    <tr>
        <th></th>
        <th style="text-align: left">Qte</th>
        <th style="text-align: right">Totals</th>
    </tr>
    </thead>
    <tbody>
    <tr style="padding: 10px;">
        <td style="text-align: left">Total Ventes</td>
        <td style="text-align: left">{{$count_vente}}</td>
        <td style=" text-align: right;">{{number_format($total_vente,3,'.',' ')}}</td>
    </tr>
    <tr style="padding: 10px;">
        <td style=" text-align: left">Total Retour</td>
        <td style="text-align: left">{{$count_retour}}</td>
        <td style=" text-align: right;">{{number_format($total_retour,3,'.',' ')}}</td>
    </tr>
    <tr style="padding: 10px;" >
        <td style=" text-align: left">Total Dépenses</td>
        <td style="text-align: left">{{$count_depense}}</td>
        <td style=" text-align: right;">{{number_format($total_depense,3,'.',' ')}}</td>
    </tr>
    <tr style="padding: 10px;">
        <td style=" text-align: left">Total</td>
        <td style="text-align: left">{{$count_total}}</td>
        <td style=" text-align: right;">{{number_format($total,3,'.',' ')}}</td>
    </tr>
    </tbody>
</table>
<br>
<br>
<br>
<hr>
</body>
</html>
