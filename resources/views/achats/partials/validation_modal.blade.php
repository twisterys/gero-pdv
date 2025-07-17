<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Valider {{strtolower(__('achats.'.$type))}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation" action="{{route('achats.valider',[$type,$o_achat->id])}}" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <h3> {{$o_achat->reference}} </h3>
        {{--        <p class="mt-2">Une fois que vous aurez validé @lang('achats.'.$type), il prendra la référence <b>{{$reference}}</b> et il n'y aura aucun moyen de revenir après.</p>--}}
        <p class="mt-2">Vous êtes entrain de valider @lang('achats.'.$type) <b>{{$o_achat->reference}}</b> , Êtes-vous sûr de continuer ?</p>
        <p class="mt-1">Reference interne: {{$reference}}</p>

        {{--        <table class="table table-striped table-bordered rounded mt-2">--}}
        {{--            <tr>--}}
        {{--                <td>Reference:</td>--}}
        {{--                <td>{{$reference}}</td>--}}
        {{--            </tr>--}}
        {{--            <tr class="bg-soft-light" >--}}
        {{--                <td>Date de validation:</td>--}}
        {{--                <td>{{now()->format('d/m/Y')}}</td>--}}
        {{--            </tr>--}}
        {{--        </table>--}}
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-success"  >Valider</button>
    </div>
</form>
