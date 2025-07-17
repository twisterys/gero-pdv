<div class="modal-header">
    <h5 class="modal-title align-self-center">Modifier {{$event->titre}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<form method="post" id="event-edit" action="{{route('events.mettre_a_jour',$event->id)}}" class="needs-validation" novalidate>
    @csrf
    @method('PUT')
    <div class="modal-body">
        <div class="row">
            <div class="col-12 mb-3">
                <label class="form-label required" for="titre-edit-input">Titre</label>
                <input type="text" required class="form-control" value="{{$event->titre}}" id="titre-edit-input" name="titre">
                <div class="invalid-feedback"></div>

            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="type-edit-input">Type</label>
                <select name="type" id="type-edit-input">
                    @foreach($types_event as $key => $value)
                        <option @selected(old('type', $event->type) == $key) value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="date-edit-input">Date</label>
                <div class="input-group">
                    <input type="text" required class="form-control" autocomplete="off"
                           id="date-edit-input" name="date" value="{{\Carbon\Carbon::make($event->date)->format('d/m/Y')}}">
                    <span class="input-group-text">
                                        <span class="fa fa-calendar-alt"></span>
                                    </span>
                    <div class="invalid-feedback"></div>

                </div>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label required" for="time-edit-input">Heure</label>
                <div class="input-group">
                    <input type="text" required class="form-control" autocomplete="off"
                           id="debut-edit-input" name="debut" data-inputmask="'alias': 'datetime'"
                           placeholder="Début" value="{{$event->debut}}">
                    <input type="text" required class="form-control" autocomplete="off"
                           id="fin-edit-input" name="fin" data-inputmask="'alias': 'datetime'"
                           placeholder="Fine" value="{{$event->fin}}">
                    <div class="invalid-feedback"></div>
                </div>
            </div>
            <div class="col-12 mb-3">
                <label class="form-label" for="description-edit-input">Description</label>
                <textarea name="description" class="form-control" id="description-edit-input" cols="30" rows="5">{{$event->description}}</textarea>
                <div class="invalid-feedback"></div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
        <button class="btn btn-primary">Enregistrer</button>
    </div>
</form>
<script>
    $('#type-edit-input').select2({
        width:'100%',
        minimumResultsForSearch:-1
    })
</script>
