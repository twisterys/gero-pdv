@extends('layouts.main')
@section('document-title','Familles')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}" rel="stylesheet" type="text/css">
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
                        <div   class="d-flex switch-filter justify-content-between align-items-center">
                            <h5 class="m-0"> <i class="fa  fas fa-boxes me-2 text-success"></i>  Liste des familles</h5>
                            <div class="page-title-right">
                                <button class="btn btn-soft-success" data-bs-target="#add-cat-modal"
                                        data-bs-toggle="modal"><i class="mdi mdi-plus"></i> Ajouter
                                </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <!-- #####--DataTable--##### -->
                    <div class="row">
                        <div class="card-title switch-filter d-none col-12">
                            <hr class="border">
                        </div>
                        <div class="col-12">
                            <div >
                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                    <tr>
                                        <th style="width: 20px">
                                            <input type="checkbox" class="form-check-input" id="select-all-row">
                                        </th>
                                        <th>Nom de famille</th>
                                        <th>Couleur</th>
                                        <th>Actif</th>
                                        <th style="width: 100px">Actions</th>
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
    <div class="modal fade" id="add-cat-modal" tabindex="-1" aria-labelledby="add-cat-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-cat-modal-title">Ajouter une famille</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('familles.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required " for="nom-input">Nom</label>
                                <input type="text" required class="form-control" id="nom-input" name="i_nom">
                                <div class="invalid-feedback">Veuillez d'abord entrer un nom</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="color-input" class="form-label">Couleur</label>
                                <input type="text" name="i_couleur" class="form-control " value="#3b5461" id="couleur-input">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check-inline d-flex align-items-center">
                                <label for="" class="form-check-label me-2" >Active</label>
                                <input name="i_actif" value="1" type="checkbox" id="active-input" switch="bool" checked="">
                                <label for="active-input" data-on-label="Oui" data-off-label="Non"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit-cat-modal" tabindex="-1" aria-labelledby="edit-cat-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
    <script src="{{asset('js/form-validation.init.js')}}" ></script>
    <script>
        const __dataTable_columns =  [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'nom', name: 'nom'},
            {data: 'couleur', name: 'couleur'},
            {data: 'actif', name: 'actif'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('familles.liste') }}";
        const __dataTable_id = "#datatable";
    </script>
    <script src="{{asset('js/dataTable_init.js')}}" ></script>
    <script>
        $('#add-cat-modal').on('shown.bs.modal', function () {
            $("#couleur-input").spectrum()
        })
        $('#edit-cat-modal').on('shown.bs.modal', function () {
            $("#color-input").spectrum()
        })
    </script>
@endpush
