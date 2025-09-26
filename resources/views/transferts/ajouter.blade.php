@extends('layouts.main')
@section('document-title', 'Transferts')
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
            90%{
                width: 110%;
            }
            100%{
                width: 100%;
            }
        }
    </style>
@endpush
@section('page')
    <form action="{{ route('transferts.sauvegarder') }}" method="POST" class="needs-validation"
          novalidate
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
                                    <h5 class="m-0 float-end ms-3">
                                        <a href="{{route('transferts.liste')}}">
                                            <i class="fa fa-arrow-left me-2"></i>
                                        </a>
                                        <i class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                        Ajouter un transfert
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
                            <div class="row col-md-12 ">

                                <div class="col-md-4 mb-3">
                                    <label for="reference" class="form-label required">
                                        Référence
                                    </label>
                                    <input type="text"
                                           class="form-control {{$errors->has('reference')?'is-invalid':''}}"
                                           id="reference" name="reference" value="{{$reference}}">
                                    @if ($errors->has('reference'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('reference') }}
                                        </div>
                                    @endif
                                 </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label required me-2" for="du_magasin">Du Magasin</label>
                                    <select class="form-select " id="magasin-select" name="magasin-select" data-parsley-multiple="groups" data-parsley-mincheck="1">
                                        @foreach($o_magasins as $o_magasin)
                                            <option value="{{$o_magasin->id}}" >{{ $o_magasin->reference }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label required me-2" for="au_magasin">Au Magasin</label>
                                    <select class="form-select " id="au_magasin" name="au_magasin" data-parsley-multiple="groups" data-parsley-mincheck="1">
                                        @foreach($o_all_magasins as $o_magasin)
                                            <option value="{{$o_magasin->id}}" >{{ $o_magasin->reference }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <!--### Lignes Table ###-->
                            <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped"
                                       id="table">
                                    <thead>
                                    <tr class="bg-primary text-white ">
                                        {{-- <th>Reference</th> --}}
                                        <th class="text-white ">Article</th>
                                        <th class="text-white" style="width: 1%;white-space: nowrap;">Quantité</th>
                                        <th class="text-white "
                                            style="width: 1%;white-space: nowrap;min-width: 55px"></th>
                                    </tr>
                                    </thead>
                                    <!-- The tbody tag will be populated by JavaScript -->
                                    <tbody id="productTableBody">
                                    @if(old('lignes'))
                                        @foreach(old('lignes') as $key=> $ligne)
                                            @include('transferts.partials.product_row_ajouter')
                                        @endforeach
                                    @else
                                        @include('transferts.partials.product_row_ajouter')
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <button type="button" id="addRowBtn"
                                        class="btn btn-sm  btn-soft-success add_row">
                                    <i class="fa-plus fa"></i> Ajouter une ligne
                                </button>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="modal fade " id="article-modal" tabindex="-1" aria-labelledby="article-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog  modal-dialog-centered position-relative " style="transform-style: preserve-3d;transition: all .7s ease 0s;">
            <div class="modal-content position-absolute" id="article-search-content" style="backface-visibility: hidden;-webkit-backface-visibility: hidden" >
            </div>
            <div class="modal-content position-absolute" id="article-add-content" style="backface-visibility: hidden;-webkit-backface-visibility: hidden;transform: rotateY(180deg)" ></div>
        </div>
    </div>
    <div class="modal fade " id="client-modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
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
        const __row = `@include('transferts.partials.product_blank_row')`;
        const __articles_modal_route = "{{ route('articles.article_select_modal',['type'=>'vente']) }}";

    </script>
    <script src="{{ asset('js/form-validation.init.js') }}"></script>
    @vite('resources/js/vente_create.js')
    <script>
        $(document).on('show.bs.modal','#article-modal',function (){
            $('#modal-magasin-select').parent().remove();
        })
        $("#au_magasin").select2({
            width: "100%",
            placeholder: "Sélectionnez un magasin",
        });
    </script>
@endpush
