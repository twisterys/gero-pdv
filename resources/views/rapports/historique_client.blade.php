@extends('layouts.main')
@section('document-title', 'Rapport Historique Client')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
@endpush
@section('page')
    <div class="row align-items-center mb-3">
        <div class="col">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h2><i class="fa fa-history text-primary me-2"></i> Rapport Historique Client</h2>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label for="i_client" class="form-label">Client</label>
                    <select id="i_client" class="form-select" name="i_client">
                        <option value="" selected>Tous les clients</option>
                        @foreach($clients as $client)
                            <option value="{{$client->id}}">{{$client->nom}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="i_types" class="form-label">Documents inclus</label>
                    <select name="i_types" class="form-control" id="i_types" multiple>
                        @foreach($types as $type)
                            <option @if(isset($types_inclue) && in_array($type,$types_inclue)) selected @endif value="{{$type}}">@lang('ventes.'.$type)</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button id="i_search_button" class="btn btn-soft-secondary">Charger</button>
                </div>
                <div class="col-md-1 d-grid">
                    <button id="i_reset_button" class="btn btn-outline-secondary">Réinitialiser</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="card-title d-flex justify-content-between align-items-center">
                <h4>Historique annuel</h4>
                <hr>
            </div>
            <div class="table-responsive">
                <table id="datatable" class="table table-bordered table-striped w-100">
                    <thead>
                    <tr>
                        <th style="width:1%; white-space:nowrap"></th>
                        <th>Client</th>
                        <th>Année</th>
                        <th>CA</th>
                        <th>Encaissements</th>
                        @if($prix_revient)
                            <th>Prix de revient</th>
                        @endif
                        <th>Crédit de l'année</th>
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
        $('#i_client').select2({width: '100%', placeholder: 'Tous les clients', allowClear: true});
        const __dataTable_ajax_link = "{{ route('rapports.historique-client') }}";
        const __dataTable_id = '#datatable';
        const __dataTable_filter_inputs_id = { i_client: '#i_client', i_annee: '#i_annee', i_types: '#i_types' };
        const __dataTable_filter_trigger_button_id = '#i_search_button';
        const __sort_column = 2; // trier d'abord par Année (desc configuré dans init)
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'client'},
            {data: 'annee'},
            {data: 'ca'},
            {data: 'encaissements'},
            @if($prix_revient)
                {data: 'prix_revient'},
            @endif
            {data: 'credit_annee'}
        ];

        // Init select2 for types
        $('#i_types').select2({
            minimumResultsForSearch: -1,
            multiple: true,
            width: '100%'
        });

        // Réinitialiser: aucun client, aucune année, aucun type
        document.getElementById('i_reset_button').addEventListener('click', function() {
            $('#i_client').val(null).trigger('change');
            $('#i_annee').val('');
            $('#i_types').val(null).trigger('change');
            // Recharge DataTable
            if (typeof table !== 'undefined') { table.ajax.reload(); }
        });
    </script>
    <script src="{{ asset('js/dataTable_init.js') }}"></script>
@endpush
