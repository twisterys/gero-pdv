<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Conversion des @lang('ventes.'.$type.'s')</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation"  action="{{route('ventes.convertir_multi',$type)}}" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <label for="" class="form-label mt-2">Convertir en</label>
        <select class="form-control form-select" name="i_type" >
            @foreach($types as $type)
                <option value="{{$type}}">@lang('ventes.'.$type)</option>
            @endforeach
        </select>
        <input type="hidden" value="{{$ventes_ids}}" name="ids">
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary"  >Convertir</button>
    </div>
</form>
