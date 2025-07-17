<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Valider {{strtolower(__('ventes.'.$type))}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation" action="{{route('ventes.valider',[$type,$o_vente->id])}}" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <h3> {{$reference}} </h3>
        {{--        <p class="mt-2">Une fois que vous aurez validé @lang('ventes.'.$type), il prendra la référence <b>{{$reference}}</b> et il n'y aura aucun moyen de revenir après.</p>--}}
        <p class="mt-2">Vous êtes entrain de valider @lang('ventes.'.$type) <b>{{$reference}}</b> , Êtes-vous sûr de continuer ?</p>


    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-success"  >Valider</button>
    </div>
</form>
