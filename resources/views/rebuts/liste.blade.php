@extends('layouts.main')
@section('document-title','Rebut')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div  class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i
                                    class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i> Rebut
                            </h5>
                            <div class="page-title-right">
                                <a href="{{ route('rebuts.ajouter') }}">
                                    <button class="btn btn-soft-info"><i class="mdi mdi-plus"></i>Ajouter un rebut</button>
                                </a>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <!-- #####--DataTable--##### -->
                    <div class="row px-3">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Liste des rebuuts</h5>
                            </div>
                            <hr class="border">
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 20px">
                                            <input type="checkbox" class="form-check-input" id="select-all-row">
                                        </th>
                                        <th style="max-width: 150px">Référence</th>
                                        <th>Date</th>
                                        <th>Magasin</th>
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
    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script>
        const __dataTable_columns = [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {
                data: 'date_operation', name: 'date_operation'
            },
            {
                data: 'magasin.nom',name:'magasin.nom'
            },
            {
                data: 'actions',name:'actions'
            },
        ];
        const __dataTable_ajax_link = "{{ route('rebuts.liste') }}";
        const __dataTable_id = "#datatable";

    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
    @vite('resources/js/ventes_liste.js')
@endpush
