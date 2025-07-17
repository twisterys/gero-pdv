<style>
    .task-card {
    .task-list {
        position: relative;

    &:after {
         content: '';
         position: absolute;
         background: $border-color;
         height: 100%;
         width: 2px;
         top: 0;
         left: 10px;
         z-index: 1;
     }

    &:before {
         content: '';
         position: absolute;
         background: $border-color;
         height: 15px;
         width: 15px;
         bottom: -14px;
         left: 3px;
         z-index: 2;
         border-radius: 50%;
     }

    li {
        margin-bottom: 30px;
        padding-left: 30px;
        position: relative;

    .task-icon {
        position: absolute;
        left: 3px;
        top: 1px;
        border-radius: 50%;
        padding: 2px;
        color: #fff;
        min-width: 15px;
        min-height: 15px;
        z-index: 2;
    }
    }
    }
    }
</style>@isset($activities)
    @if ($activities->isNotEmpty())
        <div class="card task-card">
            <div class="card-body">
                @php
                    $colors = ['bg-success', 'bg-danger', 'bg-warning', 'bg-info', 'bg-primary'];
                    shuffle($colors); // Shuffle the array to randomize the order
                @endphp

                @foreach($activities as $index => $activitie)
                    @php
                        // Assign a color sequentially, wrap around if there are more activities than colors
                        $colorIndex = $index % count($colors);
                        $assignedColor = $colors[$colorIndex];
                    @endphp
                    <ul class="list-unstyled task-list">
                        <li>
                            <i class="feather icon-check f-w-600 task-icon {{ $assignedColor }}" style="min-width: 10px; min-height: 10px; top: 5px; left: 13px;"></i>
                            <div class="d-flex justify-content-between">
                                <p class="m-b-5"><strong>{{ $activitie->created_at->format('d/m/Y') }}</strong> {{ $activitie->created_at->format('H:i:s') }}</p>
                                <p class="m-b-5"><span>({{ $activitie->causer->name }})</span></p>
                            </div>
                            <h5 class="text-muted">{{ $activitie->description }}</h5>
                        </li>
                    </ul>
                @endforeach
            </div>
        </div>
    @else

    @endif
@endisset





