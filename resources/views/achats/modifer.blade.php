@extends('layouts.main')
@section('document-title', __('achats.' . $type . '.edit.title'))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.structure.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('libs/dropify/css/dropify.min.css') }}">
@endpush
@section('page')
    <form action="{{ route('achats.mettre_a_jour', ['type' => $type, 'id' => $o_achat->id]) }}"
        enctype="multipart/form-data" method="POST" class="needs-validation" novalidate id="achat-form" autocomplete="off">
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
                                    <a href="{{ route('achats.liste', $type) }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi  mdi-shopping me-2 text-success"></i>
                                        @lang('achats.' . $type . '.edit.title')
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
                                <div class="col-12 col-lg-3 col-md-4 mb-3 @if ($magasins_count  <= 1) d-none @endif">
                                    <label for="magasin_id" class="form-label required">
                                        Magasin
                                    </label>
                                    <select name="magasin_id" {{count($o_magasins) <= 1 ? 'readonly' : null}}
                                        class="form-control {{ $errors->has('magasin_id') ? 'is-invalid' : '' }}"
                                        id="magasin-select">
                                        @foreach ($o_magasins as $o_magasin)
                                            <option @selected(old('magasin_id', $o_achat->magasin_id) == $o_magasin->id) value="{{ $o_magasin->id }}">{{ $o_magasin->text }}</option>
                                        @endforeach
                                    </select>
                                    @error('magasin_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                @if ($type !== 'bca')
                                    <div class="col-12 col-lg-3 col-md-4 mb-3">
                                        <label for="reference_externe" class="form-label required">
                                            Référence
                                        </label>
                                        <input type="text"
                                            class="form-control {{ $errors->has('reference') ? 'is-invalid' : '' }}"
                                            id="reference_externe" name="reference"
                                            value="{{ old('reference', $o_achat->reference) }}">
                                        @if ($errors->has('reference'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('reference') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                @if($o_achat->reference_interne)
                                    <div class="col-12 col-lg-3 col-md-4 mb-3">
                                        <label for="i_reference" class="form-label required">Référence Interne</label>
                                        <input type="text" @if(!$modifier_reference) readonly @endif name="reference_interne" id="i_reference"
                                            class="form-control @error('reference_interne') is-invalid  @enderror"
                                            value="{{ old('reference_interne', $o_achat->reference_interne) }}">
                                        @error('reference_interne')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                @endif
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="fournisseur_select" class="form-label required">Fournisseur</label>
                                    <div class="input-group d-grid" style="grid-template-columns:9fr 1fr ">
                                        <select required
                                            class="select2 form-control mb-3 custom-select {{ $errors->has('fournisseur_id') ? 'is-invalid' : '' }} "
                                            id="fournisseur_select" name="fournisseur_id">
                                            @if ($o_achat?->fournisseur_id)
                                                <option selected value="{{ $o_achat->fournisseur_id }}">
                                                    {{ $o_achat->fournisseur->nom }}</option>
                                            @endif
                                        </select>
                                        <button type="button" id="ajout-fournisseur"
                                            data-url="{{ route('fournisseurs.ajouter') }}"
                                            class="btn btn-soft-secondary input-group-append">+
                                        </button>
                                        @if ($errors->has('fournisseur_id'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('fournisseur_id') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="date_emission" class="form-label required">
                                        @lang('achats.' . $type . '.date_emission')
                                    </label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('date_emission') ? 'is-invalid' : '' }}"
                                        id="date_emission" name="date_emission" required readonly
                                        value="{{ old('date_emission', $o_achat->date_emission) }}">
                                    @if ($errors->has('date_emission'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('date_emission') }}
                                        </div>
                                    @endif
                                </div>
                                @if (in_array($type, ['dva', 'faa', 'fpa', 'bca']))
                                    <div class="col-12 col-lg-3 col-md-4 mb-3">
                                        <label for="date_expiration" class="form-label required">
                                            @lang('achats.' . $type . '.date_expiration')
                                        </label>
                                        <input type="text"
                                            class="form-control {{ $errors->has('date_expiration') ? 'is-invalid' : '' }}"
                                            id="date_expiration" name="date_expiration" readonly required
                                            value="{{ old('date_expiration', $o_achat->date_expiration) }}">
                                        @if ($errors->has('date_expiration'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('date_expiration') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                <!-- Object -->
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="object" class="form-label">
                                        Objet
                                    </label>
                                    <input type="text"
                                        class="form-control {{ $errors->has('objet') ? 'is-invalid' : '' }}"
                                        id="object" name="objet" value="{{ old('objet', $o_achat->objet) }}">
                                    @if ($errors->has('objet'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('objet') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="balises-select" class="form-label">
                                        Étiquette
                                    </label>
                                    <select class="form-control {{ $errors->has('balises') ? 'is-invalid' : '' }}"
                                        id="balises-select" name="balises[]" multiple>
                                        @foreach ($o_achat->tags as $tag)
                                            <option selected value="{{ $tag->id }}">{{ $tag->nom }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('balises'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('balises') }}
                                        </div>
                                    @endif
                                </div>
                                @if($globals['template_par_document'])
                                    <div class="col-12 col-xl-3 col-lg-6 col-md-6 mb-3">
                                        <label for="template_id" class="form-label "> Template</label>
                                        <select name="template_id" id="template_id" class="form-control">
                                            <option value="">Choisir un template</option>
                                            @foreach ($templates as $template)
                                                <option value="{{ $template->id }}" {{ old('template_id', $o_achat->template_id) == $template->id ? 'selected' : '' }}>
                                                    {{ $template->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                            </div>
                            <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                    <thead>
                                        <tr class="bg-primary text-white ">
                                            {{-- <th>Reference</th> --}}
                                            <th class="text-white ">Article</th>
                                            <th class="text-white" style="width: 1%;white-space: nowrap;">Quantité</th>
                                            <th class="text-white" style="width: 1%;white-space: nowrap;">HT (MAD)</th>
                                            <th class="text-white" style="width: 1%;white-space: nowrap;"> Réduction
                                                HT
                                            </th>
                                            <th class="text-white" style="width: 1%;white-space: nowrap;">TVA (%)</th>
                                            <th class="text-white" style="width: 1%;white-space: nowrap;min-width: 130px">
                                                Total TTC (MAD)
                                            </th>
                                            <th class="text-white " style="width: 1%;white-space: nowrap;min-width: 55px">
                                            </th>
                                        </tr>
                                    </thead>
                                    <!-- The tbody tag will be populated by JavaScript -->
                                    <tbody id="productTableBody">
                                        @foreach (old('lignes', $o_achat->lignes->sortby('position')) as $key => $o_ligne)
                                            @include('achats.partials.product_row_modifier')
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-end">
                                <button type="button" id="addRowBtn" class="btn btn-sm  btn-soft-success add_row">
                                    <i class="fa-plus fa"></i> Ajouter une ligne
                                </button>
                            </div>
                            <div class="col-12 mt-3 row justify-content-between p-2">
                                <div class="col-md-6"></div>
                                <div class="col-md-6 col-12 row m-0 bg-primary p-3 rounded text-white" style="max-width: 500px">
                                    <h5 class="col-md-4 col-6 fw-normal">Total HT</h5>
                                    <h5 class="col-md-8 col-6 text-end fw-normal" id="total-ht-text">
                                        {{ number_format($o_achat?->total_ht, '2', '.', '') . ' MAD' ?? '0.00 MAD' }}</h5>
                                    <h5 class="col-md-4 col-6 fw-normal">Total Réduction</h5>
                                    <h5 class="col-md-8 col-6 text-end fw-normal" id="total-reduction-text">
                                        {{ number_format($o_achat?->total_reduction, '2', '.', '') . ' MAD' ?? '0.00 MAD' }}
                                    </h5>
                                    <h5 class="col-md-4 col-6 fw-normal">Total TVA</h5>
                                    <h5 class="col-md-8 col-6 text-end fw-normal" id="total-tva-text">
                                        {{ number_format($o_achat?->total_tva, '2', '.', '') . ' MAD' ?? '0.00 MAD' }}</h5>
                                    <h5 class="col-md-4 col-6 mb-0 fw-normal">Total TTC</h5>
                                    <h2 class="col-md-8 col-12 mb-0 text-end" id="total-ttc-text">
                                        {{ number_format($o_achat?->total_ttc, '2', '.', '') . ' MAD' ?? '0.00 MAD' }}
                                    </h2>
                                </div>
                            </div>
                            <div class="col-12">
                                <label for="i_note" class="form-label">Note</label>
                                <textarea name="i_note" id="i_note" cols="30" rows="10">{{ old('i_note', $o_achat->note) }}</textarea>
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
    <div class="modal fade " id="fournisseur-modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content ">

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/tinymce/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('libs/dropify/js/dropify.min.js') }}"></script>
    <script>
        const __row = `@include('achats.partials.product_blank_row')`;
        const __articles_modal_route = "{{ route('articles.article_select_modal', ['type' => 'achat']) }}";
    </script>
    @vite('resources/js/achats_create.js')
    <script>
        $('#fichier_document').on('dropify.afterClear', function(event, element) {
            if ($('#i_supprimer_fichier').length) {
                $('#i_supprimer_fichier').val(1)
            } else {
                $('#achat-form').append(
                    '<input id="i_supprimer_fichier" type="hidden" name="i_supprimer_fichier" value="1" >');
            }
        });
    </script>
@endpush
