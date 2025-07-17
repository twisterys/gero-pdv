<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Cloner @lang('ventes.'.$o_vente->type_document) {{$o_vente->reference}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation" enctype="multipart/form-data" action="{{route('ventes.cloner',[$o_vente->type_document,$o_vente->id])}}" novalidate>
    @csrf
    <div class="modal-body">
        <div class="col-12 ">
            <label for="date_emission" class="form-label required">Date d'émission</label>
            <div class="input-group">
                <input class="form-control datupickeru @error('date_emission') is-invalid @enderror"
                       data-provide="datepicker"
                       data-date-autoclose="true"
                       type="text"
                       name="date_emission"
                       value="{{ old('date_reception', now()->format('d/m/Y')) }}"
                       id="date_emission" required>
                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
            </div>
        </div>

    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary"  >Cloner</button>
    </div>
</form>

<script>



        $('.datupickeru').datepicker({    language:'fr',  })
</script>
