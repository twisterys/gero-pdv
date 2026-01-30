@extends('layouts.main')
@section('document-title', 'Rapport des créances')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
@endpush
@section('page')
    <div class="row align-items-center">
        <div class="col">
            <div class="card-title justify-content-between align-items-center">
                <h2><i class="fa fa-file-invoice-dollar me-2 text-primary"></i> Rapport des créances</h2>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-md-4 col-sm-6 mb-2 p-1">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-3">Total général des créances</h4>
                        <span class="badge bg-soft-primary text-primary py-1 px-2 fs-6">Tous</span>
                    </div>
                    <div class="h3 mt-1">{{ number_format($total_general, 3, '.', ' ') }} <small class="fs-6">MAD</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-2 p-1">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-3">Créances année :</h4>
                        <span class="badge bg-soft-success text-success py-1 px-2 fs-6">{{ $exercice }}</span>
                    </div>
                    <div class="h3 mt-1">{{ number_format($total_n, 3, '.', ' ') }} <small class="fs-6">MAD</small></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-2 p-1">
            <div class="card h-100 shadow-sm">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-3">Créances années :</h4>
                        <span class="badge bg-soft-warning text-warning py-1 px-2 fs-6">Avant {{ $exercice }}</span>
                    </div>
                    <div class="h3 mt-1">{{ number_format($total_prev, 3, '.', ' ') }} <small class="fs-6">MAD</small></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <div class="card-title justify-content-between align-items-center">
                <h4>Liste des clients et leurs créances</h4>
                <hr>
            </div>
            <div class="row align-items-end gx-3 mb-3">
                <div class="col-md-2 col-sm-6">
                    <label for="i_search" class="form-label">Recherche de client</label>
                    <div class="input-group">
                        <span class="input-group-text text-primary"><i class="fas fa-search"></i></span>
                        <input type="text" id="i_search" name="i_search" class="form-control" placeholder="Nom ou référence">
                    </div>
                </div>
                <div class="col-md-2 col-sm-6">
                    <label for="i_critere" class="form-label">Critère de filtrage</label>
                    <select id="i_critere" name="i_critere" class="form-select">
                        <option value="total" selected>Total des créances</option>
                        <option value="current">Créances actuelles ( {{ \Carbon\Carbon::now()->year }} )</option>
                        <option value="previous">Créances précédentes ( avant {{ \Carbon\Carbon::now()->year }} )</option>
                    </select>
                </div>
                <div class="col-md-2 col-sm-4">
                    <label for="i_min" class="form-label">Montant min</label>
                    <input type="number" step="0.001" min="0" id="i_min" name="i_min" class="form-control" placeholder="Ex: 1000">
                </div>
                <div class="col-md-2 col-sm-4">
                    <label for="i_max" class="form-label">Montant max</label>
                    <input type="number" step="0.001" min="0" id="i_max" name="i_max" class="form-control" placeholder="Ex: 10000">
                </div>
                <div class="col-md-2 col-sm-6 mt-2 mt-md-0">
                    <label for="i_types" class="form-label">Documents inclus</label>
                    <select name="i_types" class="form-control" id="i_types" multiple>
                        @foreach($types as $type)
                            <option @if(in_array($type,$types_inclue)) selected @endif value="{{$type}}">@lang('ventes.'.$type)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 col-sm-4 d-grid">
                    <button id="i_search_button" class="btn btn-soft-secondary mt-md-4">Appliquer</button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped w-100">
                    <thead>
                    <tr>
                        <th style="width:1%; white-space:nowrap"></th>
                        <th>Client</th>
                        <th>Téléphone</th>
                        <th>Total créances</th>
                        <th>Créances {{ \Carbon\Carbon::now()->year }}</th>
                        <th>Créance avant {{ \Carbon\Carbon::now()->year }}</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @include('rapports.partials.rapport_help')
@endsection

@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/select2/js/select2.min.js') }}"></script>
    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'nom'},
            {data: 'telephone'},
            {data: 'total_credit'},
            {data: 'credit_n'},
            {data: 'credit_prev'},
        ];
        const __dataTable_ajax_link = "{{ route('rapports.rapport-creances') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            i_search: '#i_search',
            i_critere: '#i_critere',
            i_min: '#i_min',
            i_max: '#i_max',
            i_types: '#i_types',
        };
        const __dataTable_filter_trigger_button_id = '#i_search_button';
        const __sort_column = 3; // Tri par défaut sur Total créances (desc)
        $('#i_types').select2({
            minimumResultsForSearch: -1,
            multiple: true,
            width: '100%'
        });
    </script>
    <script src="{{ asset('js/dataTable_init.js') }}"></script>
@endpush
