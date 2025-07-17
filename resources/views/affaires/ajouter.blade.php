@extends('layouts.main')
@section('document-title', 'Affaire')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.structure.min.css') }}" rel="stylesheet">
    <style>
        .ui-sortable-placeholder.ui-state-highlight {
            height: 200px;
            background-color: #f8f9fa;
            overflow: hidden;
        }

        .table {
            border-collapse: separate !important;
        }

        .table tr:first-child th:first-child {
            border-top-left-radius: var(--bs-border-radius) !important;
        }

        .table tr:first-child th:last-child {
            border-top-right-radius: var(--bs-border-radius) !important;
        }

        .table tbody tr:last-child td:last-child {
            border-bottom-right-radius: var(--bs-border-radius) !important;
        }

        .table tbody tr:last-child td:first-child {
            border-bottom-left-radius: var(--bs-border-radius) !important;
        }

        .ui-sortable-placeholder.ui-state-highlight td {
            border-top: 2px dashed rgba(162, 159, 157, 0.24);
            border-bottom: 2px dashed rgba(162, 159, 157, 0.24);
        }

        .ui-sortable-placeholder.ui-state-highlight td:last-child {
            border-right: 2px dashed rgba(162, 159, 157, 0.24);
        }

        .ui-sortable-placeholder.ui-state-highlight td:first-child {
            border-left: 2px dashed rgba(162, 159, 157, 0.24);
        }

        .ui-sortable-placeholder.ui-state-highlight td:first-child {
            border-top-left-radius: 10px !important;
        }

        .ui-sortable-placeholder.ui-state-highlight td:last-child {
            border-top-right-radius: 10px !important;
        }

        .ui-sortable-placeholder.ui-state-highlight td:first-child {
            border-bottom-left-radius: 10px !important;
        }

        .ui-sortable-placeholder.ui-state-highlight td:last-child {
            border-bottom-right-radius: 10px !important;
        }

        @keyframes splash {
            0% {
                width: 100%;
            }

            90% {
                width: 110%;
            }

            100% {
                width: 100%;
            }
        }
    </style>
@endpush
@section('page')
    <form action="{{route('affaire.sauvegarder')}}" method="POST" class="needs-validation" novalidate
          autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('affaire.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i
                                            class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                        Ajouter une affaire
                                    </h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        <!-- ####--Inputs--#### -->
                        <div class="row px-3 align-items-start ">


                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="i_reference" class="form-label required">
                                    Référence
                                </label>
                                <input type="text"
                                       class="form-control {{ $errors->has('i_reference') ? 'is-invalid' : '' }}"
                                       id="i_reference" name="i_reference"
                                       value="{{ old('i_reference', 'AFF-' . \Carbon\Carbon::now()->format('YmdHis')) }}">
                                @if ($errors->has('i_reference'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('i_reference') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="client_select" class="form-label required">Client</label>
                                <div class="input-group d-grid" style="grid-template-columns:9fr 1fr ">
                                    <select required
                                            class="select2 form-control mb-3 custom-select {{ $errors->has('client_id') ? 'is-invalid' : '' }} "
                                            id="client_select" name="client_id">
                                    </select>
                                    <button type="button" id="ajout-client" data-url="{{ route('clients.ajouter') }}"
                                            class="btn btn-soft-secondary input-group-append ">+
                                    </button>
                                    @if ($errors->has('client_id'))
                                        <div class="invalid-feedback flex-fill">
                                            {{ $errors->first('client_id') }}
                                        </div>
                                    @endif
                                </div>

                            </div>

                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3 d-none">
                                <label for="cycle_facturation" class="form-label">
                                    Cycle de facturation
                                </label>
                                <div class="input-group mb-3">
                                    <input type="number"
                                           class="form-control {{ $errors->has('cycle_duree') ? 'is-invalid' : '' }}"
                                           id="cycle_duree"
                                           name="cycle_duree"
                                           value="{{ old('cycle_duree') }}">
                                    <select class="form-select {{ $errors->has('cycle_type') ? 'is-invalid' : '' }}"
                                            id="cycle_type"
                                            name="cycle_type" style="max-width: 100px;">
                                        <option value="jour" {{ old('cycle_type') == 'jour' ? 'selected' : '' }}>Jour</option>
                                        <option value="mois" {{ old('cycle_type') == 'mois' ? 'selected' : '' }}>Mois</option>
                                    </select>
                                </div>
                                @if ($errors->has('cycle_duree'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('cycle_duree') }}
                                    </div>
                                @endif
                                @if ($errors->has('cycle_type'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('cycle_type') }}
                                    </div>
                                @endif
                            </div>



                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="date_debut" class="form-label required">
                                    Date de début
                                </label>
                                <input type="text"
                                       class="form-control {{ $errors->has('date_debut') ? 'is-invalid' : '' }}"
                                       id="date_debut" name="date_debut" required readonly
                                       value="{{ old('date_debut', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
                                @if ($errors->has('date_debut'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('date_debut') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="date_fin" class="form-label ">
                                    Date de fin
                                </label>
                                <input type="text"
                                       class="form-control {{ $errors->has('date_fin') ? 'is-invalid' : '' }}"
                                       id="date_fin" name="date_fin" readonly
                                       value="{{ old('date_fin', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
                                @if ($errors->has('date_fin'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('date_fin') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 col-lg-6 col-md-4 mb-3">
                                <label for="titre" class="form-label required">
                                    Titre
                                </label>
                                <input type="text"
                                       class="form-control {{ $errors->has('titre') ? 'is-invalid' : '' }}" id="titre"
                                       name="titre" value="{{ old('titre') }}">
                                @if ($errors->has('titre'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('titre') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="budget_estimatif" class="form-label">
                                    Budget estimatif
                                </label>
                                <input type="number"
                                       class="form-control {{ $errors->has('budget_estimatif') ? 'is-invalid' : '' }}"
                                       id="budget_estimatif"
                                       name="budget_estimatif"
                                       value="{{ old('budget_estimatif') }}">
                                @if ($errors->has('budget_estimatif'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('budget_estimatif') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="ca_global" class="form-label">
                                    CA global
                                </label>
                                <input type="number"
                                       class="form-control {{ $errors->has('ca_global') ? 'is-invalid' : '' }}"
                                       id="ca_global"
                                       name="ca_global"
                                       value="{{ old('ca_global') }}">
                                @if ($errors->has('ca_global'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('ca_global') }}
                                    </div>
                                @endif
                            </div>

{{--                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">--}}
{{--                                <label for="description" class="form-label">--}}
{{--                                    Description--}}
{{--                                </label>--}}
{{--                                <input type="text"--}}
{{--                                       class="form-control {{ $errors->has('description') ? 'is-invalid' : '' }}"--}}
{{--                                       id="description"--}}
{{--                                       name="description" value="{{ old('description') }}">--}}
{{--                                @if ($errors->has('description'))--}}
{{--                                    <div class="invalid-feedback">--}}
{{--                                        {{ $errors->first('description') }}--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
                            <div class=" col-12 col-lg-6 col-md-4 mb-6">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description"
                                          class="form-control summernote {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                          id="description">{{ old('description') }}</textarea>
                                @if ($errors->has('description'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('description') }}
                                    </div>
                                @endif
                            </div>

                        <!--### Lignes Table ###-->
                            <div class="col-12 col-lg-6 col-md-4 mb-6">

                            <label for="description" class="form-label">
                                Jalons
                            </label>
                        <div  data-simplebar="init" class="table-responsive col-12 mt-3">
                            <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                <thead>
                                <tr class="bg-primary text-white ">
                                    <th class="text-white"  style="width: 20%;">Nom jalon</th>
                                    <th class="text-white" style="width: 20%;">Date</th>
                                    <th class="text-white " style="width: 1%;white-space: nowrap;min-width: 55px">
                                        <button type="button" id="addRowBtn" class="btn btn-sm  btn-soft-success add_row">
                                            <i class="fa-plus fa"></i>
                                        </button>
                                    </th>
                                </tr>
                                </thead>
                                <!-- The tbody tag will be populated by JavaScript -->
                                <tbody id="productTableBody">
                                @if (old('lignes'))
                                    @foreach (old('lignes') as $key => $ligne)
                                        @include('affaires.partials.jalon_row_ajouter')
                                    @endforeach
{{--                                @else--}}
{{--                                    @include('affaires.partials.jalon_row_ajouter')--}}
                                @endif
                                </tbody>
                            </table>
                        </div>
                        </div>
{{--                        <div class="text-center">--}}
{{--                            <button type="button" id="addRowBtn" class="btn btn-sm  btn-soft-success add_row">--}}
{{--                                <i class="fa-plus fa"></i> Ajouter une ligne--}}
{{--                            </button>--}}
{{--                        </div>--}}
                    </div>

                </div>
            </div>
        </div>
        </div>
    </form>
    <div class="modal fade " id="client-modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content ">

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/tinymce/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        const __row = `@include('affaires.partials.jalon_blank_row')`;
        @if (old('client_id'))
        $.ajax({
            url: '{{ route('clients.afficher_ajax', old('client_id')) }}',
            success: function (response) {
                $('#client_select').append(
                    `<option value="{{ old('client_id') }}">${response.nom}</option>`).trigger(
                    'change')
            },
            error: function (xhr) {

            }
        })
        @endif
    </script>
    <script src="{{ asset('js/form-validation.init.js') }}"></script>
    @vite('resources/js/affaires_create.js')
@endpush
