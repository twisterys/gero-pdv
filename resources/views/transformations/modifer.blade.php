@extends('layouts.main')
@section('document-title',"Modifier une transformation")
@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.structure.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <form action="{{ route('transformations.mettre_a_jour',$o_transformation->id) }}" method="POST"
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
                                    <a href="{{ route('transformations.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i
                                            class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                        Modifier une transformation
                                    </h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span
                                            class="d-none d-sm-inline">Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        <div class="row px-3 align-items-start ">
                            <div class="row col-md-12 ">
                                <div class="col-12 col-lg-3 col-md-4 mb-3 @if (count($o_magasins)  <= 1) d-none @endif">
                                    <label for="magasin_id" class="form-label required">
                                        Magasin
                                    </label>
                                    <select name="magasin_id"
                                            class="form-control {{ $errors->has('magasin_id') ? 'is-invalid' : '' }}"
                                            id="magasin-select">
                                        @foreach ($o_magasins as $o_magasin)
                                            <option value="{{ $o_magasin->id }}" {{ old('magasin_id', $o_transformation->magasin_id) == $o_magasin->id ? 'selected' : '' }}>{{ $o_magasin->text }}</option>
                                        @endforeach
                                    </select>
                                    @error('magasin_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="date" class="form-label required">
                                        Date
                                    </label>
                                    <input type="text"
                                           class="form-control {{ $errors->has('date') ? 'is-invalid' : '' }}"
                                           id="date" name="date" readonly required
                                           value="{{ old('date', $o_transformation->date) }}">
                                    @if ($errors->has('date'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('date') }}
                                        </div>
                                    @endif
                                </div>
                                <!-- Object -->
                                <div class="col-12 col-lg-3 col-md-4 mb-3">
                                    <label for="object" class="form-label">
                                        Objet
                                    </label>
                                    <input type="text"
                                           class="form-control {{ $errors->has('objet') ? 'is-invalid' : '' }}"
                                           id="object"
                                           name="objet" value="{{ old('objet', $o_transformation->objet) }}">
                                    @if ($errors->has('commercial_id'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('commercial_id') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!--### Lignes Table ###-->
                        </div>
                       <div class="row mx-0">
                           <div class="col-6 mt-4 tt">
                               <div class="w-100">
                                   <h5 class="text-muted">
                                       Articles sortants</h5>
                                   <hr class="border border-success">
                               </div>
                               <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                   <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                       <thead>
                                       <tr class="bg-primary text-white ">
                                           {{-- <th>Reference</th> --}}
                                           <th class="text-white ">Article</th>
                                           <th class="text-white" style="width: 1%;white-space: nowrap;">Quantité</th>
                                           <th class="text-white" style="width: 1%;white-space: nowrap;">Actions</th>
                                       </tr>
                                       </thead>
                                       <!-- The tbody tag will be populated by JavaScript -->
                                       <tbody id="lignes_sortant">
                                       @foreach ($o_transformation->lignes->where('type', 'sortant') as $position => $ligne)
                                           <tr>
                                               <td>
                                                   <input type="hidden" class="article_id" name="lignes_sortant[{{$position}}][i_article_id]"
                                                          value="{{old('lignes_sortant.'.$position.'.i_article_id', $ligne->article_id)}}">
                                                   <div class="input-group mb-2">
                                                       @if(old('lignes_sortant.'.$position.'.i_article_id', $ligne->article_id))
                                                           <span
                                                               class="input-group-text">{{$ligne->article->reference}}</span>
                                                       @endif
                                                       <input type="text" name="lignes_sortant[{{$position}}][i_article]"
                                                              class="form-control  {{$errors->has('lignes_sortant.'.$position.'.i_article')? 'is-invalid' : ''}}"
                                                              value="{{old('lignes_sortant.'.$position.'.i_article', $ligne->nom_article)}}">
                                                       <button type="button" class="btn btn-soft-primary article_btn">
                                                           <i class="fa fa-store"></i></button>
                                                   </div>
                                                   @error('lignes_sortant.'.$position.'.i_article')
                                                   <div class="invalid-feedback">
                                                       {{ $errors->first('lignes_sortant.'.$position.'.i_article') }}
                                                   </div>
                                                   @enderror
                                               </td>
                                               <td>
                                                   <input style="width: 120px" step="0.01"
                                                          class="form-control quantite mb-1 {{$errors->has('lignes_sortant.'.$position.'.i_quantite')? 'is-invalid' : ''}}"
                                                          name="lignes_sortant[{{$position}}][i_quantite]" type="number"
                                                          value="{{old('lignes_sortant.'.$position.'.i_quantite', $ligne->quantite)}}">
                                                   @error('lignes_sortant.'.$position.'.i_quantite')
                                                   <div class="invalid-feedback">
                                                       {{ $errors->first('lignes_sortant.'.$position.'.i_quantite') }}
                                                   </div>
                                                   @enderror
                                                   <select
                                                       class="form-select row_select2 unite {{$errors->has('lignes_sortant.'.$position.'.i_unite')? 'is-invalid' : ''}}"
                                                       style="width: 120px" name="lignes_sortant[{{$position}}][i_unite]" id="">
                                                       @foreach($o_unites as $o_unite)
                                                           <option @if(old('lignes_sortant.'.$position.'.i_unite', $ligne->unite_id) == $o_unite->id) selected
                                                                   @endif value="{{$o_unite->id}}">{{$o_unite->nom}}</option>
                                                       @endforeach
                                                   </select>
                                                   @error('lignes_sortant.'.$position.'.i_unite')
                                                   <div class="invalid-feedback">
                                                       {{ $errors->first('lignes_sortant.'.$position.'.i_unite') }}
                                                   </div>
                                                   @enderror
                                               </td>
                                               <td></td>
                                           </tr>
                                       @endforeach
                                       </tbody>
                                   </table>
                               </div>
                               <div class="text-end">
                                   <button type="button" id="addRowBtn" data-name="sortant" class="btn btn-sm  btn-soft-success add_row">
                                       <i class="fa-plus fa"></i> Ajouter une ligne
                                   </button>
                               </div>
                           </div>
                           <div class="col-6 mt-4 tt">
                               <div class="w-100">
                                   <h5 class="text-muted">
                                       Articles entrants</h5>
                                   <hr class="border border-success">
                               </div>
                               <div data-simplebar="init" class="table-responsive col-12 mt-3">
                                   <table class="table rounded overflow-hidden table-hover table-striped" id="table">
                                       <thead>
                                       <tr class="bg-primary text-white ">
                                           {{-- <th>Reference</th> --}}
                                           <th class="text-white ">Article</th>
                                           <th class="text-white" style="width: 1%;white-space: nowrap;">Quantité</th>
                                           <th class="text-white" style="width: 1%;white-space: nowrap;">Actions</th>
                                       </tr>
                                       </thead>
                                       <!-- The tbody tag will be populated by JavaScript -->
                                       <tbody id="lignes_entrant">
                                       @foreach ($o_transformation->lignes->where('type', 'entrant') as $position => $ligne)
                                           <tr>
                                               <td>
                                                   <input type="hidden" class="article_id" name="lignes_entrant[{{$position}}][i_article_id]"
                                                          value="{{old('lignes_entrant.'.$position.'.i_article_id', $ligne->article_id)}}">
                                                   <div class="input-group mb-2">
                                                       @if(old('lignes_entrant.'.$position.'.i_article_reference', $ligne->article_reference))
                                                           <span
                                                               class="input-group-text">{{old('lignes_entrant.'.$position.'.i_article_reference', $ligne->article_reference)}}</span>
                                                       @endif
                                                       <input type="text" name="lignes_entrant[{{$position}}][i_article]"
                                                              class="form-control  {{$errors->has('lignes_entrant.'.$position.'.i_article')? 'is-invalid' : ''}}"
                                                              value="{{old('lignes_entrant.'.$position.'.i_article', $ligne->nom_article)}}">
                                                       <button type="button" class="btn btn-soft-primary article_btn">
                                                           <i class="fa fa-store"></i></button>
                                                   </div>
                                                   @error('lignes_entrant.'.$position.'.i_article')
                                                   <div class="invalid-feedback">
                                                       {{ $errors->first('lignes_entrant.'.$position.'.i_article') }}
                                                   </div>
                                                   @enderror
                                                   <div class="text-end">
                                                       <button class="btn btn-soft-success btn-sm add-description "
                                                               type="button"><i class="fa fa-plus"></i>
                                                           Ajouter une description
                                                       </button>
                                                   </div>
                                                   <div
                                                       class="description">{{old('lignes_entrant.'.$position.'.i_description', $ligne->description)}}</div>
                                                   <textarea name="lignes_entrant[{{$position}}][i_description]"
                                                             class="description-line d-none {{$errors->has('lignes_entrant.'.$position.'.i_description')? 'is-invalid' : ''}}">{{old('lignes_entrant.'.$position.'.i_description', $ligne->description)}}</textarea>
                                                   @error('lignes_entrant.'.$position.'.i_description')
                                                   <div class="invalid-feedback">
                                                       {{ $errors->first('lignes_entrant.'.$position.'.i_description') }}
                                                   </div>
                                                   @enderror
                                               </td>
                                               <td>
                                                   <input style="width: 120px" step="0.01"
                                                          class="form-control quantite mb-1 {{$errors->has('lignes_entrant.'.$position.'.i_quantite')? 'is-invalid' : ''}}"
                                                          name="lignes_entrant[{{$position}}][i_quantite]" type="number"
                                                          value="{{old('lignes_entrant.'.$position.'.i_quantite', $ligne->quantite)}}">
                                                   @error('lignes_entrant.'.$position.'.i_quantite')
                                                   <div class="invalid-feedback">
                                                       {{ $errors->first('lignes_entrant.'.$position.'.i_quantite') }}
                                                   </div>
                                                   @enderror
                                                   <select
                                                       class="form-select row_select2 unite {{$errors->has('lignes_entrant.'.$position.'.i_unite')? 'is-invalid' : ''}}"
                                                       style="width: 120px" name="lignes_entrant[{{$position}}][i_unite]" id="">
                                                       @foreach($o_unites as $o_unite)
                                                           <option @if(old('lignes_entrant.'.$position.'.i_unite', $ligne->unite_id) == $o_unite->id) selected
                                                                   @endif value="{{$o_unite->id}}">{{$o_unite->nom}}</option>
                                                       @endforeach
                                                   </select>
                                                   @error('lignes_entrant.'.$position.'.i_unite')
                                                   <div class="invalid-feedback">
                                                       {{ $errors->first('lignes_entrant.'.$position.'.i_unite') }}
                                                   </div>
                                                   @enderror
                                               </td>
                                               <td></td>
                                           </tr>
                                       @endforeach
                                       </tbody>
                                   </table>
                               </div>
                               <div class="text-end">
                                   <button type="button" data-name="entrant" id="addRowBtn" class="btn btn-sm  btn-soft-success add_row">
                                       <i class="fa-plus fa"></i> Ajouter une ligne
                                   </button>
                               </div>
                           </div>
                       </div>
                        <div class="col-12">
                            <label for="i_note" class="form-label">Note</label>
                            <textarea name="i_note" id="i_note" cols="30"
                                      rows="10">{{ old('i_note', $o_transformation->note) }}</textarea>
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
        const __articles_modal_route = "{{ route('articles.article_select_modal', ['type' => 'transformation']) }}";
        const __row = `<tr><td><div class="input-group mb-2"><input type="text" name="[name][-1][i_article]"class="form-control"><button type="button" class="btn btn-soft-primary article_btn"><i class="fa fa-store"></i></button></div><div class="text-end"><button class="btn btn-soft-success btn-sm add-description" type="button"><i class="fa fa-plus"></i>Ajouter une description</button></div><div class="description"></div><textarea name="[name][-1][i_description]" class="description-line d-none"></textarea></td><td><input style="width: 120px" step="0.01" class="form-control quantite mb-1 " name="[name][-1][i_quantite]" type="number"><select class="form-select row_select2 unite " style="width: 120px" name="[name][-1][i_unite]" id="">@foreach($o_unites as $o_unite)<option value="{{$o_unite->id}}">{{$o_unite->nom}}</option>@endforeach</select></td><td></td></tr>`
    </script>
    @vite(['resources/js/transformations/ajouter.js'])
    <script src="{{ asset('js/form-validation.init.js') }}"></script>
@endpush
