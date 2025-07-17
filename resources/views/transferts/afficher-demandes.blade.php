@extends('layouts.main')
@section('document-title','Demandes de stock')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}" rel="stylesheet" type="text/css">
    <style>

    </style>
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div  class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0">
                                    <a href="{{route('transferts.liste')}}"><i class="fa fa-arrow-left me-2"></i></a>
                                    <i class="fa  fas fa-boxes me-2 text-success"></i>
                                    Demandes de transferts</h5>

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
                                        <th>Référence</th>
                                        <th>Magasin de sortie</th>
                                        <th>Magasin d'entrée</th>
                                        <th>Date</th>
                                        <th>Statut</th>
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
    <div class="modal fade " id="show-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content ">

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}"></script>
    <script src="{{asset('js/form-validation.init.js')}}" ></script>
    <script>
        const __dataTable_columns =  [
            {data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell'},
            {data: 'reference', name: 'reference'},
            {data: 'magasin_sortie', name: 'magasin_sortie'},
            {data: 'magasin_entree', name: 'magasin_entree'},
            {data: 'created_at', name: 'created_at'},
            {data: 'statut', name: 'statut'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('transferts.afficher.demandes') }}";
        const __dataTable_id = "#datatable";
    </script>
    <script>
        $(document).on('click','.show-btn',function (){
            let btn = $(this);
            let html = btn.html();
            btn.html($(__spinner_element).removeClass('me-2'))
            $.ajax({
                url: btn.data('url'),
                success: function (response) {
                    btn.html(html)
                    $('#show-modal .modal-content').html(response);
                    $('#show-modal').modal('show');
                },
                error: function (){
                    btn.html($(__spinner_element).removeClass('me-2'))
                }
            })
        })
    </script>
    <script src="{{asset('js/dataTable_init.js')}}" ></script>
@endpush
