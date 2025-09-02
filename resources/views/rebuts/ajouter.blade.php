@extends('layouts.main')

@section('document-title',__('Rebut'))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">

@endpush
@section('page')
    <form action="{{ route("rebuts.sauvegarder") }}" method="POST" class="needs-validation" novalidate
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
                                    <a href="{{ route('rebuts.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i
                                            class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>Rebut
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
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="i_reference" class="form-label required">
                                        Référence
                                    </label>
                                    <input type="text"
                                           class="form-control {{$errors->has('i_reference')?'is-invalid':''}}"
                                           id="i_reference" name="i_reference"
                                           value="RBT-{{ \Carbon\Carbon::now()->format('YmdHis') }}">
                                    @if ($errors->has('i_reference'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('i_reference') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-3 col-md-4 mb-3 @if (count($o_magasins)  <= 1) d-none @endif">
                                    <label for="magasin_id" class="form-label required">
                                        Magasin
                                    </label>

                                    <select name="magasin_id"
                                            class="form-select {{ $errors->has('magasin_id') ? 'is-invalid' : '' }}"
                                            id="magasin-select">
                                        @foreach ($o_magasins as $o_magasin)
                                            <option value="{{ $o_magasin->id }}" {{ old('magasin_id') == $o_magasin->id ? 'selected' : '' }}>
                                                {{ $o_magasin->reference }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('magasin_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                            </div>
                            <!--### Lignes Table ###-->
                            <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                    <thead>
                                    <tr class="bg-primary text-white ">
                                        <th class="text-white " >Article</th>
                                        <th class="text-white" style="width: 20%;white-space: nowrap;">Quantité de rebuts</th>
                                        <th class="text-white " style="width: 1%;white-space: nowrap;min-width: 55px">
                                        </th>
                                    </tr>
                                    </thead>
                                    <!-- The tbody tag will be populated by JavaScript -->
                                    <tbody id="productTableBody">
                                    @if (old('lignes'))
                                        @foreach (old('lignes') as $key => $ligne)
                                            @include('rebuts.partials.product_row_ajouter')
                                        @endforeach
                                    @else
                                        @include('rebuts.partials.product_row_ajouter')
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <button type="button" id="addRowBtn" class="btn btn-sm  btn-soft-success add_row">
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
        <div class="modal-dialog  modal-dialog-centered position-relative "
             style="transform-style: preserve-3d;transition: all .7s ease 0s;">
            <div class="modal-content position-absolute" id="article-search-content"
                 style="backface-visibility: hidden;-webkit-backface-visibility: hidden">
            </div>
            <div class="modal-content position-absolute" id="article-add-content"
                 style="backface-visibility: hidden;-webkit-backface-visibility: hidden;transform: rotateY(180deg)"></div>
        </div>
    </div>
@endsection


@push('scripts')

    <script>
        const __row = `@include('rebuts.partials.product_blank_row')`;
        const __articles_modal_route = "{{ route('articles.article_select_modal', ['type' => 'rebuts']) }}";
    </script>
{{--    @vite('resources/js/inventaire_create.js')--}}
    @vite('resources/js/rebuts_create.js')

@endpush
