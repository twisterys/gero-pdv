@extends('layouts.main')
@section('document-title','Utilisateurs')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <style>
        .last-col {
            width: 1%;
            white-space: nowrap;
        }
    </style>
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div class="d-flex switch-filter justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i class="mdi me-2 text-success mdi-account-group"></i>
                                </i>Liste des utilisateurs</h5>
                            <div class="page-title-right">
                                <a href="{{ route('utilisateurs.ajouter') }}">
                                    <button class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter</button>
                                </a>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <!-- #####--DataTable--##### -->
                    <div class="col-12">
                        <div class="table-responsive">
                            <table id="datatable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 20px">
                                        <input type="checkbox" class="form-check-input" id="select-all-row">
                                    </th>
                                    <th style="max-width: 150px">Référence</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th style="max-width: 180px">Actions</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{asset('js/form-validation.init.js')}}" ></script>
    <script>
        const client_select_ajax_link = '{{ route('clients.select') }}';
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'id', name: 'id'},
            {data: 'name', name: 'name'},
            {data: 'email', name: 'email'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('utilisateurs.liste')}}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {}
        const __dataTable_filter_trigger_button_id = '#search-btn';
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
@endpush
