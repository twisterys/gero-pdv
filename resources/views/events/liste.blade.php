@extends('layouts.main')
@section('document-title', 'Avtivités')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
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
                        <div  class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i
                                    class="mdi mdi-bell me-2 text-success"></i> Activités
                            </h5>
                            <div class="page-title-right">
                                <button class="btn btn-soft-success" data-bs-target="#event-add-modal" data-bs-toggle="modal" ><i class="mdi mdi-plus"></i> Ajouter</button>
                                <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer
                                </button>
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
                            <label class="form-label" for="client-select">Client</label>
                            <select class="select2 form-control mb-3 custom-select" name="client_id" id="client-select">
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="type-select">Type</label>
                            <select class="select2 form-control mb-3 custom-select"  id="type-select">
                                <option value="">Tous</option>
                                @foreach($types as $key=> $type)
                                    <option value="{{$key}}">{{$type}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="date-input">Date</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="date-input" >
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                            </button>
                        </div>
                    </div>                    <!-- #####--DataTable--##### -->
                    <div class="row px-3">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Liste des activités</h5>
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
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Titre</th>
                                        <th>Client</th>
                                        <th>Début</th>
                                        <th>Fin</th>
                                        <th>Durée</th>
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
    </div>
    <div class="modal fade" id="event-add-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center">Ajouter une activité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="event-add" action="{{route('events.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class=" col-12 mb-3">
                                <label class="form-label" for="client-add-select">Client</label>
                                <select class="select2 form-control mb-3 custom-select" name="client_id" id="client-add-select">
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="titre-add-input">Titre</label>
                                <input type="text" required class="form-control" id="titre-add-input" name="titre">
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="type-add-input">Type</label>
                                <select name="type" id="type-add-input">
                                    @foreach($types as $key => $value)
                                        <option value="{{$key}}">{{$value}}</option>
                                    @endforeach
                                </select>
                                <div class="invalid-feedback"></div>

                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="date-add-input">Date</label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="date-add-input" name="date">
                                    <span class="input-group-text">
                                        <span class="fa fa-calendar-alt"></span>
                                    </span>
                                    <div class="invalid-feedback"></div>

                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="time-add-input">Heure</label>
                                <div class="input-group">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="debut-add-input" name="debut" data-inputmask="'alias': 'datetime'"
                                           placeholder="Début">
                                    <input type="text" required class="form-control" autocomplete="off"
                                           id="fin-add-input" name="fin" data-inputmask="'alias': 'datetime'"
                                           placeholder="Fin">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label" for="description-add-input">Description</label>
                                <textarea name="description" class="form-control" id="description-add-input" cols="30" rows="5"></textarea>
                                <div class="invalid-feedback"></div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="edit-modal" tabindex="-1"
         aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

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
            {data: 'date', name: 'date'},
            {data: 'type', name: 'type'},
            {data: 'titre', name: 'titre'},
            {data: 'client_id', name: 'client_id',orderable: false},
            {data: 'debut', name: 'debut'},
            {data: 'fin', name: 'fin'},
            {data: 'dure', name: 'dure',orderable: false},
            {
                data: 'actions', name: 'actions', orderable: false,
            },
        ];
        const __dataTable_ajax_link = "{{ route('events.liste') }}";
        const __dataTable_id = "#datatable";
        
        const __dataTable_filter_inputs_id = {
            client_id: '#client-select',
            date: '#date-input',
            type: '#type-select',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
        $('.filter-btn').click(e => {
            $('.switch-filter').toggleClass('d-none')
        })
        $('#date-input').datepicker({
            format:'dd/mm/yyyy'
        })
        $('#client-select').select2({
            width: '100%',
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: !0,
            ajax: {
                url: __client_select2_route,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
                cache: false,
            },
            minimumInputLength: 3
        })

        $('#client-add-select').select2({
            width: '100%',
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: !0,
            dropdownParent: $('#event-add-modal'),
            ajax: {
                url: __client_select2_route,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: data,
                    };
                },
                cache: false,
            },
            minimumInputLength: 3
        });
        $('#type-select').select2({
            width: '100%',
            placeholder: {
                id: '',
                text: 'Tous'
            },
            allowClear: !0,
            minimumResultsForSearch:-1
        })
        $('#date-add-input').datepicker({
            format: 'dd/mm/yyyy'
        })
        $('#debut-add-input,#fin-add-input').inputmask({
            inputFormat: "HH:MM"
        })
        $('#type-add-input').select2({
            width:'100%',
            minimumResultsForSearch:-1
        })
        $(document).on('submit','#event-add,#event-edit',function (e){
            e.preventDefault();
            let btn =  $(this).find('button').attr('disabled',"true")
            let form = $(this)
            form.find('.is-invalid').removeClass('is-invalid')
            $.ajax({
                url:form.attr('action'),
                method:'post',
                data:form.serialize(),
                success:function (response){
                    form.trigger('reset');
                    btn.removeAttr('disabled');
                    toastr.success(response);
                    $('#event-add-modal,#event-edit-modal').modal('hide');
                    location.reload()
                },
                error: function (xhr){
                    btn.removeAttr('disabled')

                    if(xhr.status === 422){
                        let errors = xhr.responseJSON.errors;
                        for (const [key,value] of Object.entries(errors)){
                            form.find('[name="'+key+'"]').addClass('is-invalid')
                            form.find('[name="'+key+'"]').siblings('.invalid-feedback').html(value)
                        }

                    }else{
                        toastr.error(xhr.responseText);
                    }
                }
            })
        })
    </script>
    <script src="{{asset('js/dataTable_init.js')}}"></script>
@endpush
