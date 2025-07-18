@extends('layouts.main')
@section('document-title', __('ventes.' . $type . '.add.title'))
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
    <form action="{{ route('ventes.sauvegarder', ['type' => $type]) }}" method="POST" class="needs-validation" novalidate
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
                                    <a href="{{ route('ventes.liste', $type) }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i
                                            class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                        @lang('ventes.' . $type . '.add.title')
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
                                <div class="col-12 col-lg-3 col-md-4 mb-3 @if ($magasins_count  <= 1) d-none @endif">
                                    <label for="magasin_id" class="form-label required">
                                        Magasin
                                    </label>
                                    <select name="magasin_id" {{count($o_magasins) <=1 ? 'readonly':null }}
                                        class="form-control {{ $errors->has('magasin_id') ? 'is-invalid' : '' }}"
                                        id="magasin-select">
                                        @foreach ($o_magasins as $o_magasin)
                                            <option value="{{ $o_magasin->id }}">{{ $o_magasin->text }}</option>
                                        @endforeach
                                    </select>
                                    @error('magasin_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="client_select" class="form-label required">Client</label>
                                    <div class="input-group d-flex flex-nowrap" >
                                        <select required
                                            class="select2 form-control mb-3 custom-select {{ $errors->has('client_id') ? 'is-invalid' : '' }} "
                                            id="client_select" name="client_id">
                                        </select>
                                        <button type="button" id="ajout-client" data-url="{{ route('clients.ajouter') }}"
                                            class="btn btn-soft-secondary input-group-append ">+</button>
                                        @if ($errors->has('client_id'))
                                            <div class="invalid-feedback flex-fill">
                                                {{ $errors->first('client_id') }}
                                            </div>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="date_emission" class="form-label required">
                                        @lang('ventes.' . $type . '.date_emission')
                                    </label>
                                    @cannot('vente.date')
                                        <input type="text"
                                               class="form-control {{ $errors->has('date_emission') ? 'is-invalid' : '' }}"
                                               readonly value="{{ old('date_emission', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
                                    @endcannot
                                    <input type="text"
                                        class="form-control @cannot('vente.date') d-none @endcannot  {{ $errors->has('date_emission') ? 'is-invalid' : '' }}"
                                        id="date_emission" name="date_emission" required readonly
                                        value="{{ old('date_emission', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
                                    @if ($errors->has('date_emission'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('date_emission') }}
                                        </div>
                                    @endif
                                </div>
                                @if (in_array($type, ['dv', 'fa', 'fp', 'bc']))
                                    <div class="col-12 col-lg-3 col-md-4 mb-3">
                                        <label for="date_expiration" class="form-label required">
                                            @lang('ventes.' . $type . '.date_expiration')
                                        </label>
                                        @cannot('vente.date')
                                            <input type="text"
                                                   class="form-control {{ $errors->has('date_expiration') ? 'is-invalid' : '' }}"
                                                   readonly value="{{ old('date_expiration', Carbon\Carbon::now()->addDays(15)->format('d/m/Y')) }}">
                                        @endcannot
                                        <input type="text"
                                            class="form-control @cannot('vente.date') d-none @endcannot {{ $errors->has('date_expiration') ? 'is-invalid' : '' }}"
                                            id="date_expiration" name="date_expiration" readonly required
                                            value="{{ old('date_expiration', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
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
                                        class="form-control {{ $errors->has('objet') ? 'is-invalid' : '' }}" id="object"
                                        name="objet" value="{{ old('objet') }}">
                                    @if ($errors->has('commercial_id'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('commercial_id') }}
                                        </div>
                                    @endif
                                </div>

                                @if(\App\Services\LimiteService::is_enabled('commerciaux'))
                                <!-- Commercial -->
                                <div class="col-12 col-xl-3 col-lg-6 col-md-6 mb-3">
                                    <label for="commercial_id" class="form-label ">Commercial
                                    </label>
                                    <div class="input-group">
                                        <select
                                            class="select2 form-control mb-3 custom-select {{ $errors->has('commercial_id') ? 'is-invalid' : '' }}"
                                            id="commercial_id" name="commercial_id">
                                        </select>
                                        <input id="commission_par_defaut" type="number"
                                            value="{{ old('i_commercial_pourcentage') }}" min="0"
                                            name="i_commercial_pourcentage"
                                            class="form-control {{ $errors->has('i_commercial_pourcentage') ? 'is-invalid' : null }} ">
                                        <span class="input-group-text">%</span>
                                        @error('i_commercial_pourcentage')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                        @if ($errors->has('commercial_id'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('commercial_id') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endif


                                <div class="col-12 col-xl-3 col-lg-6 col-md-6 mb-3">
                                    <label for="balises" class="form-label ">Balises
                                    </label>
                                    <select
                                        class="select2 form-control mb-3 custom-select {{ $errors->has('balises') ? 'is-invalid' : '' }}"
                                        id="balises" name="balises[]">
                                    </select>
                                    @error('balises[]')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>


                                @if($globals['template_par_document'])
                                    <div class="col-12 col-lg-3 col-md-4 mb-3">
                                        <label for="template_id" class="form-label">
                                            Template
                                        </label>
                                        <select class="form-select {{ $errors->has('template_id') ? 'is-invalid' : '' }}" id="template_id" name="template_id">
                                            <option value="">Choisir un template</option>
                                            @foreach($templates as $template)
                                                <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                                    {{ $template->nom }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('template_id'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('template_id') }}
                                            </div>
                                        @endif
                                    </div>
                                @endif








                            </div>
                            <!--### Lignes Table ###-->
                            <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                    <thead>
                                        <tr class="bg-primary text-white ">
                                            {{-- <th>Reference</th> --}}
                                            <th class="text-white ">Article</th>
                                            <th class="text-white" style="width: 1%;white-space: nowrap;">Quantité</th>
                                            @if ($prix_revient)
                                                <th class="text-white" style="width: 1%;white-space: nowrap;">Revient
                                                    (MAD)</th>
                                            @endif
                                            <th class="text-white" style="width: 1%;white-space: nowrap;">HT (MAD)</th>
                                            <th class="text-white" style="width: 1%;white-space: nowrap;"> Réduction HT
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
                                        @if (old('lignes'))
                                            @foreach (old('lignes') as $key => $ligne)
                                                @include('ventes.partials.product_row_ajouter')
                                            @endforeach
                                        @else
                                            @include('ventes.partials.product_row_ajouter')
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
                        <div class="col-12 mt-3 row mx-0 justify-content-between p-2">
                            <div class="col-md-6 col-12"></div>
                            <div class="col-md-6 col-12 mx-0 row m-0 bg-primary p-3 rounded text-white" style="max-width: 500px">
                                <h5 class="col-4 fw-normal">Total HT</h5>
                                <h5 class="col-8 text-end fw-normal" id="total-ht-text">0.00 MAD</h5>
                                <h5 class="col-4 fw-normal">Total Réduction</h5>
                                <h5 class="col-8 text-end fw-normal" id="total-reduction-text">0.00 MAD</h5>
                                <h5 class="col-4 fw-normal">Total TVA</h5>
                                <h5 class="col-8 text-end fw-normal" id="total-tva-text">0.00 MAD</h5>
                                <h5 class="col-4 mb-0 fw-normal">Total TTC</h5>
                                <h2 class="col-8 mb-0 text-end" id="total-ttc-text">0.00 MAD</h2>
                            </div>
                        </div>
                        <div class="col-12">
                            <label for="i_note" class="form-label">Note</label>
                            <textarea name="i_note" id="i_note" cols="30" rows="10">{{ old('i_note') }}</textarea>
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
    <div class="modal fade " id="historique_prix_modal" tabindex="-1" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog  modal-dialog-centered position-relative ">
            <div class="modal-content ">
            </div>
        </div>
    </div>

    <div class="modal fade " id="descriptionModal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">Description</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <textarea name="description" id="tinymceEditor"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                    <button class="btn btn-success" type="button" id="saveDescription"  >Valider</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/tinymce/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>
    <script>
        const __row = `@include('ventes.partials.product_blank_row')`;
        @if (old('client_id'))
            $.ajax({
                url: '{{ route('clients.afficher_ajax', old('client_id')) }}',
                success: function(response) {
                    $('#client_select').append(
                        `<option value="{{ old('client_id') }}">${response.nom}</option>`).trigger(
                        'change')
                },
                error: function(xhr) {

                }
            })
        @endif
        @if (old('commercial_id'))
            $.ajax({
                url: '{{ route('commercials.afficher_ajax', old('commercial_id')) }}',
                success: function(response) {
                    $('#commercial_id').append(`<option value="${response.id}">${response.nom}</option>`)
                        .trigger('change')
                    @if (old('i_commercial_pourcentage'))
                        $('#commission_par_defaut').val({{ old('i_commercial_pourcentage') }})
                    @else
                        $('#commission_par_defaut').val(response.commission_par_defaut)
                    @endif

                },
                error: function(xhr) {

                }
            })
        @endif
        const __articles_modal_route = "{{ route('articles.article_select_modal', ['type' => 'vente']) }}";
    </script>



    <script src="{{ asset('js/form-validation.init.js') }}"></script>
    @vite('resources/js/vente_create.js')
@endpush
