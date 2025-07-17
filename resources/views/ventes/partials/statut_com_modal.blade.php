<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title"> Changer statut de @lang('ventes.'.$o_vente->type_document) {{$o_vente->reference}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" class="needs-validation" enctype="multipart/form-data" action="{{route('ventes.changer_status',[$o_vente->type_document,$o_vente->id])}}" novalidate>
    @csrf
    <div class="modal-body">
        <!-- Champ "Convertir en" -->
        <div class="col-12">
            <label for="i_type" class="form-label">Marquer comme</label>
            <select class="form-control form-select" name="i_type" id="i_type">
                @foreach($statut_com as $type)
                    <option value="{{$type}}" {{ $type == $o_vente->statut_com ? 'selected' : '' }}>@lang('ventes.'.$type)</option>
                @endforeach
            </select>
        </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Changer statut</button>
    </div>
</form>

