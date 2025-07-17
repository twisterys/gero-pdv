@extends('layouts.main')
@section('document-title','comptes')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('libs/daterangepicker/css/daterangepicker.min.css')}}" rel="stylesheet">
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
                            <h5 class="m-0"> <i class="fa  fas fa-university me-2 text-success" ></i> Liste des comptes</h5>
                            <div class="page-title-right">
                                <a href="{{ route('comptes.ajouter') }}">
                                    <button class="btn btn-soft-success"><i class="mdi mdi-plus"></i> Ajouter</button>
                                </a>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <!-- #####--DataTable--##### -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 20px" >
                                            <input type="checkbox" class="form-check-input" id="select-all-row">
                                        </th>
                                        <th>Nom de compte</th>
                                        <th>Type</th>
                                        <th>Nature</th>
                                        <th>Nom de la banque</th>
                                        <th>RIB</th>
                                        <th>Adresse</th>
                                        <th>Principal</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'nom', name: 'nom'},
            {data: 'type', name: 'type'},
            {data: 'statut', name: 'statut'},
            {data: 'banque', name: 'banque'},
            {data: 'rib', name: 'rib'},
            {data: 'adresse', name: 'adresse'},
            {data: 'principal', name: 'principal'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{route('comptes.liste')}}";
        const __dataTable_id = "#datatable";
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>

@endpush
