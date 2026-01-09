@extends('layouts.main')
@section('document-title','Transferts de stock')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}" rel="stylesheet" type="text/css">
    <style>
        /*.last-col {*/
        /*    width: 1%;*/
        /*    white-space: nowrap;*/
        /*}*/
    </style>
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div   class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0"> <i class="fa  fas fa-boxes me-2 text-success"></i>  Liste des transferts</h5>
                                <div class="page-title-right">
                                    <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer</button>
                                    <a href="{{route('transferts.afficher.demandes')}}" class="btn btn-soft-info"><i class="mdi mdi-eye"></i> <span class="d-none d-sm-inline" > Demandes de transferts </span>
                                    </a>
                                    <a href="{{route('transferts.ajouter')}}" class="btn btn-soft-success"><i class="mdi mdi-plus"></i> <span class="d-none d-sm-inline" > Ajouter </span>
                                    </a>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        <!-- #####--Filters--##### -->
                        <div class="switch-filter row px-3 d-none mt-2 mb-4">
                            <div class="card-title col-12">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="m-0">Filtres</h5>
                                </div>
                                <hr class="border">
                            </div>
                            <div class="col-sm-3 col-12 mb-3">
                                <label class="form-label" for="statut-controle-select">Statut de contrôle</label>
                                <select class="select2 form-control mb-3 custom-select" id="statut-controle-select">
                                    <option value=""></option>
                                    <option value="Tous">Tous</option>
                                    <option value="controle">Contrôlé</option>
                                    <option value="non_controle">Non contrôlé</option>
                                </select>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                                </button>
                            </div>
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
                                        <th>Contrôle</th>
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
            {data: 'is_controled', name: 'is_controled'},
            {data: 'actions', name: 'actions', orderable: false,},
        ];
        const __dataTable_ajax_link = "{{ route('transferts.liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            statut_controle: '#statut-controle-select',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
    </script>
    <script>
        $('.filter-btn').click(e => {
            $('.switch-filter').toggleClass('d-none')
        })
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
        $("#statut-controle-select").select2({
            width: "100%",
            placeholder: {
                id: "",
                text: "Tous",
            },
            allowClear: !0,
            minimumResultsForSearch: -1,
            selectOnClose: false,
        });
    </script>
    <script src="{{asset('js/dataTable_init.js')}}" ></script>
@endpush
