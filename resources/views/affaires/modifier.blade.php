@extends('layouts.main')
@section('document-title', 'Modifier Affaire')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.structure.min.css') }}" rel="stylesheet">
@endpush
@section('page')


    <form action="{{ route('affaire.mettre_a_jour', ['id' => $affaire->id]) }}" method="POST"
          class="needs-validation" novalidate autocomplete="off">
        @csrf
        @method('PUT')
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
                                        Modifier une affaire
                                    </h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>

                            <hr class="border">
                        </div>

                        <div class="row px-3 align-items-start ">
                            <div class="row col-md-12 ">
                                @if($affaire->reference)
                                    <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                        <label for="reference" class="form-label required">Référence</label>
                                        <input type="text" name="reference" id="reference"
                                               class="form-control @error('reference') is-invalid  @enderror"
                                               value="{{ old('i_reference', $affaire->reference) }}">
                                        @error('reference')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                @endif
                                <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                    <label for="Client" class="form-label required">Client</label>
                                    <div class="input-group d-grid" style="grid-template-columns: 9fr 1fr">
                                        <select required
                                                class="select2 form-control mb-3 custom-select @error('client_id') is-invalid @enderror"
                                                id="client_select" name="client_id">
                                            @if ($affaire?->client_id)
                                                <option selected value="{{ $affaire->client_id }}">
                                                    {{ $affaire->client->nom }}</option>
                                            @endif
                                        </select>
                                        <button type="button" id="ajout-client" data-url="{{ route('clients.ajouter') }}"
                                                class="btn btn-soft-secondary input-group-append ">+</button>
                                        @error('client_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>


                                <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                    <label for="date_debut" class="form-label required">
                                        Date de début
                                    </label>
                                    <input type="text" class="form-control  @error('date_debut') is-invalid @enderror"
                                           id="date_debut" name="date_debut" required
                                           value="{{ old('date_debut', $affaire?->date_debut) }}">
                                    @error('date_debut')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                    <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                    <label for="date_fin" class="form-label required">
                                        Date de fin
                                    </label>
                                    <input type="text" class="form-control  @error('date_fin') is-invalid @enderror"
                                           id="date_fin" name="date_fin" required
                                           value="{{ old('date_fin', $affaire->date_fin ) }}">
                                    @error('date_fin')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                    <div class="col-12 col-lg-6 col-md-4 mb-3">
                                        <label for="titre" class="form-label required">
                                            Titre
                                        </label>
                                        <input type="text" class="form-control  @error('titre') is-invalid @enderror"
                                               id="titre" name="titre" required
                                               value="{{ old('titre', $affaire?->titre) }}">
                                        @error('titre')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                <!-- Object -->
                                <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                    <label for="budget_estimatif" class="form-label">
                                        Budget estimatif
                                    </label>
                                    <input type="number" class="form-control @error('budget_estimatif') is-invalid @enderror"
                                           id="budget_estimatif" name="budget_estimatif" value="{{ old('budget_estimatif', $affaire?->budget_estimatif) }}">
                                    @error('budget_estimatif')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                    <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                    <label for="ca_global" class="form-label">
                                        CA global
                                    </label>
                                    <input type="number" class="form-control @error('ca_global') is-invalid @enderror"
                                           id="ca_global" name="ca_global" value="{{ old('ca_global', $affaire?->ca_global) }}">
                                    @error('ca_global')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                    <div class="col-12 col-lg-6 col-md-4 mb-6">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea name="description"
                                                  class="form-control summernote {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                                  id="description" >{{old('description', $affaire?->descripton)}}</textarea>
                                        @if ($errors->has('description'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('description') }}
                                            </div>
                                        @endif
                                    </div>
                            <div class="col-12 col-lg-6 col-md-4 mb-3">
                            <div  data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                    <thead>
                                    <tr class="bg-primary text-white ">
                                        <th class="text-white"  style="width: 20%;">Jalon</th>
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
                                    @foreach (old('lignes', $affaire->jalons->sortby('position')) as $key => $o_ligne)
                                        @include('affaires.partials.jalon_row_modifier')
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            </div>
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
        @if (old('client_id', $affaire->client_id))
        $.ajax({
            url: '{{ route('clients.afficher_ajax', old('client_id', $affaire->client_id)) }}',
            success: function(response) {
                $('#client_select').append(`<option value="${response.id}">${response.nom}</option>`)
                    .trigger('change')
            },
            error: function(xhr) {

            }
        })
        @endif

    </script>
    <script src="{{ asset('js/form-validation.init.js') }}"></script>
    @vite('resources/js/affaires_create.js')
@endpush
