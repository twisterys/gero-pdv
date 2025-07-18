<div class="modal-header">
    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Cloner @lang('achats.'.$o_achat->type_document) {{$o_achat->reference}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post"  class="needs-validation" enctype="multipart/form-data" action="{{route('achats.cloner',[$o_achat->type_document,$o_achat->id])}}" novalidate>
    @csrf
    <div class="modal-body">
        <div class="col-12 ">
            <label for="date_emission" class="form-label required">Date d'émission</label>
            <div class="input-group">
                @cannot('achat.date')
                    <input type="text" class="form-control"
                           readonly
                           value="{{ old('date_reception', now()->format('d/m/Y')) }}"
                    >
                @endcannot
                <input class="form-control datupickeru @cannot('achat.date') d-none @endcannot  @error('date_emission') is-invalid @enderror"
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
