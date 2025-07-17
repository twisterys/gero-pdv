<div class="modal-header">
    <h5 class="modal-title align-self-center">{{$event->titre}}</h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
    <div class="row">
        <table class="table table-striped table-bordered">
            <tr >
                <th>Titre</th>
                <td>{{$event->titre}}</td>
            </tr>
            <tr >
                <th>Type</th>
                <td>{{$types_event[$event->type]}}</td>
            </tr>
            <tr >
                <th>Date</th>
                <td>{{\Carbon\Carbon::make($event->date)->format('d/m/Y')}}</td>
            </tr>
            <tr >
                <th>Début</th>
                <td>{{$event->debut}}</td>
            </tr>
            <tr >
                <th>Fin</th>
                <td>{{$event->fin}}</td>
            </tr>
        </table>
        <div class="mt-3">
            <h6>Description</h6>
            <div class="bg-body p-3 rounded">
                {{$event->description ?? "---"}}
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
</div>
