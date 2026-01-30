@extends('layouts.main')
@section('document-title','Articles')
@push('styles')
    <link rel="stylesheet" href="{{asset('libs/select2/css/select2.min.css')}}">
    <link href="{{asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('libs/daterangepicker/css/daterangepicker.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
    <link rel="stylesheet" href="{{asset('libs/spectrum-colorpicker2/spectrum.min.css')}}">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form id="articles-form" enctype="multipart/form-data"
                          action="{{route('articles.mettre_a_jour',$o_article->id)}}"
                          method="post" class="needs-validation" novalidate autocomplete="off">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div id="__fixed" class="d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="{{route('articles.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="fa  fas fa-boxes me-2 text-success"></i>
                                        Modifier une Article</h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        @csrf
                        @method('PUT')
                        <div class="row px-3 ">
                            <div class="col-sm-6 row mx-0 col-12 px-0">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Propriétés</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class=" col-12 col-lg-6 mb-3 ">
                                    <label class="form-label " for="reference-input">Référence</label>
                                    <input  type="text" @if(!$modifier_reference) readonly @endif
                                           class="form-control {{$errors->has('i_reference')? 'is-invalid' : ''}}"
                                           id="reference-input" placeholder="" name="i_reference"
                                            value="{{old('i_reference',$o_article->reference)}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_reference'))
                                            {{ $errors->first('i_reference') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label   " for="designation-input">Désignation</label>
                                    <input  type="text"
                                           class="form-control {{$errors->has('i_designation')? 'is-invalid' : ''}}"
                                           id="designation-input"
                                           placeholder=""
                                           name="i_designation"
                                           value="{{old('i_designation',$o_article->designation)}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_designation'))
                                            {{ $errors->first('i_designation') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label   " for="unity-select">Unité</label>
                                    <div class="input-group">
                                        <select
                                                class="select2 form-control mb-3 custom-select {{$errors->has('i_unite')? 'is-invalid' : ''}}"
                                                name="i_unite"
                                                id="unity-select">
                                            @if($unites)
                                                @foreach($unites as $unite)
                                                    <option @if(old('i_unite',$o_article->unite) === $unite['id']) selected
                                                            @endif value="{{$unite['id']}}">{{$unite['nom']}}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                        <button type="button" class="btn btn-light" data-bs-target="#unite-modal" data-bs-toggle="modal" >+</button>
                                        @if($errors->has('i_unite'))
                                        <div class="invalid-feedback">
                                                {{ $errors->first('i_unite') }}
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label  " for="cat-select">Famille</label>
                                    <div class="input-group"><select
                                            class="select2 form-control mb-3 custom-select {{$errors->has('i_famille')? 'is-invalid' : ''}}"
                                            name="i_famille"
                                            id="cat-select">
                                            @if($o_article['famille'])
                                                <option
                                                    value="{{$o_article->famille->id}}">{{$o_article->famille->nom}}</option>
                                            @endif
                                        </select>
                                        <button type="button" class="btn btn-light" data-bs-target="#family-modal" data-bs-toggle="modal" >+</button>
                                    @if($errors->has('i_famille'))
                                        <div class="invalid-feedback">
                                                {{ $errors->first('i_famille') }}
                                        </div>
                                        @endif
                                    </div>

                                </div>
                                @if($marque)
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="marque" class="form-label">Marque</label>
                                   <div class="input-group">
                                       <select name="i_marque_id"
                                               class="select2 form-control mb-3 custom-select {{$errors->has('i_marque_id')? 'is-invalid' : ''}}"
                                               id="marque">
                                           <option selected value="">...</option>
                                           @foreach($marques as $marque)
                                               <option @selected(old('i_marque_id',$o_article->marque_id) === $marque->id)  value="{{$marque->id}}">{{$marque->nom}}</option>
                                           @endforeach
                                       </select>
                                       <button type="button" class="btn btn-light" data-bs-target="#marque-modal" data-bs-toggle="modal" >+</button>
                                       @error('i_marque_id')
                                       <div class="invalid-feedback">
                                           {{ $message }}
                                       </div>
                                       @enderror
                                   </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 d-none d-lg-block"></div>
                                @endif
                                @if($image)
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label for="i_image"
                                           class="form-label {{$errors->has('i_image')? 'is-invalid' : ''}}">Image</label>
                                    <input name="i_image" type="file" id="i_image" accept="image/*"
                                           @if($o_article->image) data-default-file="{{route('article.image.load', $o_article->image)}}" @endif>
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_image'))
                                            {{ $errors->first('i_image') }}
                                        @endif
                                    </div>

                                </div>
                                @endif
                                <div class="col-12 col-lg-6 mb-3">
                                    <label class="form-label" for="desc-input">Description</label>
                                    <textarea name="description"
                                              class="form-control {{$errors->has('description')? 'is-invalid' : ''}}"
                                              style="resize: vertical"
                                              placeholder="Tapez votre description ici..." id="desc-input" cols="30"
                                              rows="8">{{old('description',$o_article->description)}}</textarea>
                                    <div class="invalid-feedback">
                                        @if($errors->has('description'))
                                            {{ $errors->first('description') }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6 row mx-0 px-0 col-12 align-content-start">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Prix</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label    " for="tax-select">Taxe</label>
                                    <select
                                            class="select2 form-control mb-3 custom-select {{$errors->has('i_taxe')? 'is-invalid' : ''}}"
                                            name="i_taxe"
                                            id="tax-select">
                                        @if($taxes)
                                            @foreach($taxes as $taxe)
                                                <option @if(old('i_taxe',$o_article->taxe)==$taxe->valeur) selected
                                                        @endif value="{{$taxe['valeur']}}">{{$taxe['nom']}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_taxe'))
                                            {{ $errors->first('i_taxe') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label   " for="vente_ht-input">Prix de vente</label>
                                    <div class="input-group">
                                        <input  type="number" step="0.001" min="0"
                                               class="form-control {{$errors->has('i_vente_prix')? 'is-invalid' : ''}}"
                                               id="vente-input"
                                               name="i_vente_prix"
                                               value="{{old('i_vente_prix',$o_article->prix_vente)}}">
                                        <span class="input-group-text">MAD</span>
                                        <div class="invalid-feedback">
                                            @if($errors->has('i_vente_prix'))
                                                {{ $errors->first('i_vente_prix') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-12 col-lg-6 mb-3 ">
                                    <label class="form-label   " for="achat_price-input">Prix d'achat</label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" min="0"
                                               class="form-control {{$errors->has('i_achat_prix')? 'is-invalid' : ''}}"
                                               id="achat_price-input"
                                               name="i_achat_prix"
                                               value="{{old('i_achat_prix',$o_article->prix_achat)}}">
                                        <span class="input-group-text">MAD</span>
                                        <div class="invalid-feedback">
                                            @if($errors->has('i_achat_prix'))
                                                {{ $errors->first('i_achat_prix') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
{{--                                <div class="col-12 col-lg-6 mb-3 ">--}}
{{--                                    <label class="form-label  " for="revien_prix-input">Prix de revient</label>--}}
{{--                                    <div class="input-group">--}}
{{--                                        <input type="number" step="0.001" min="0"--}}
{{--                                               class="form-control {{$errors->has('i_revient_prix')? 'is-invalid' : ''}}"--}}
{{--                                               id="revien_prix-input"--}}
{{--                                               name="i_revient_prix"--}}
{{--                                               value="{{old('i_revient_prix',$o_article->prix_revient)}}">--}}
{{--                                        <span class="input-group-text">MAD</span>--}}
{{--                                        <div class="invalid-feedback">--}}
{{--                                            @if($errors->has('i_revient_prix'))--}}
{{--                                                {{ $errors->first('i_revient_prix') }}--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Stock</h5>
                                    <hr class="border border-success">
                                </div>
                                @if($numero_serie)
                                    <div class="col-12 col-lg-6 mb-3 ">
                                        <label class="form-label" for="i_numero_serie-input">Numéro de série</label>
                                        <input required type="text"
                                               class="form-control {{$errors->has('i_numero_serie')? 'is-invalid' : ''}}"
                                               id="i_numero_serie-input"
                                               placeholder=""
                                               name="i_numero_serie" value="{{old('i_numero_serie',$o_article->numero_serie)}}">
                                        <div class="invalid-feedback">
                                            @if($errors->has('i_numero_serie'))
                                                {{ $errors->first('i_numero_serie') }}
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label" for="quantite-input">Quantité d'alerte</label>
                                    <input type="number" step="0.001"
                                           class="form-control {{$errors->has('i_quantite_alerte')? 'is-invalid' : ''}}"
                                           id="quantite-input"
                                           placeholder=""
                                           name="i_quantite_alerte"
                                           value="{{old('i_quantite_alerte',$o_article->quantite_alerte)}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_quantite_alerte'))
                                            {{ $errors->first('i_quantite_alerte') }}
                                        @endif
                                    </div>
                                </div>




                                @if($globals['code_barre'])
                                    <div class="col-12 col-lg-6 mb-3">
                                        <label class="form-label" for="i_code_barre-input">Code-barre</label>
                                        <div class="input-group flex-nowrap">
                                            <input required type="text"
                                                   class="form-control {{$errors->has('i_code_barre') ? 'is-invalid' : ''}}"
                                                   id="i_code_barre-input"
                                                   placeholder="Entrez le code-barre"
                                                   name="i_code_barre"
                                                   value="{{ old('i_code_barre', $o_article->code_barre) }}">

                                            <button type="button" class="btn btn-outline-secondary" onclick="imprimerCodeBarre()">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>

                                        @if($errors->has('i_code_barre'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('i_code_barre') }}
                                            </div>
                                        @endif

                                        <div id="message-erreur-codebarre" class="text-danger mt-2" style="display: none;">
                                            Veuillez saisir un code barre avant d'imprimer.
                                        </div>
                                    </div>
                                @endif




                                <div class="col-12 col-lg-6 mb-3 d-flex align-items-center pt-4">
                                    <div class="d-flex align-items-center">
                                        <label for="i_stockable" class="form-check-label me-2">Stockable</label>
                                        <div class="form-check-inline " style="height: 24px">
                                            <input name="i_stockable" value="1" type="checkbox" id="i_stockable"
                                                   switch="bool"
                                                   @if(old('i_stockable',$o_article->stockable)==1)checked @endif >
                                            <label for="i_stockable" data-on-label="Oui"
                                                   data-off-label="Non"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="unite-modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-uni-modal-title">Ajouter une unité</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('unites.sauvegarder')}}" class="needs-validation" id="unite-form" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label  " for="unite-input">Unité</label>
                                <input type="text"  class="form-control" id="unite-input" name="i_nom">
                                <div class="invalid-feedback">Veuillez d'abord entrer une unité</div>
                            </div>
                            <div class="col-12 mb-3">
                                <div class="form-check-inline d-flex align-items-center">
                                    <label for="" class="form-check-label me-2">Par défaut</label>
                                    <input name="i_default" value="1" type="checkbox" id="active-input" switch="bool">
                                    <label for="active-input" data-on-label="Oui" data-off-label="Non"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="family-modal" tabindex="-1" aria-labelledby="add-cat-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-cat-modal-title">Ajouter une famille</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="family-form" action="{{route('familles.sauvegarder')}}" class="needs-validation" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label  " for="nom-input">Nom</label>
                                <input type="text"  class="form-control" id="nom-input" name="i_nom">
                                <div class="invalid-feedback">Veuillez d'abord entrer un nom</div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="color-input" class="form-label">Couleur</label>
                                <input type="text" name="i_couleur" class="form-control " value="#3b5461" id="couleur-input">
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <div class="form-check-inline d-flex align-items-center">
                                <label for="" class="form-check-label me-2" >Active</label>
                                <input name="i_actif" value="1" type="checkbox" id="active-input" switch="bool" checked="">
                                <label for="active-input" data-on-label="Oui" data-off-label="Non"></label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    @if($marque)
    <div class="modal fade" id="marque-modal" tabindex="-1" aria-labelledby="add-marque-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="add-marque-modal-title">Ajouter une marque</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" action="{{route('marques.sauvegarder')}}" class="needs-validation" id="marque-form" novalidate>
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label  " for="nom-input">Nom</label>
                                <input type="text"  class="form-control" id="nom-input" name="nom">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
    @endif
@endsection
@push('scripts')
    <script src="{{asset('libs/spectrum-colorpicker2/spectrum.min.js')}}" ></script>
    <script src="{{asset('libs/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('libs/daterangepicker/js/daterangepicker.js')}}"></script>
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script>
        $('#i_image').on('dropify.afterClear', function(event, element) {
            if ($('#i_supprimer_image').length){
                $('#i_supprimer_image').val(1)
            }else {
                $('#articles-form').append('<input id="i_supprimer_image" type="hidden" name="i_supprimer_image" value="1" >');
            }
        });
    </script>

    <script>

        function imprimerCodeBarre() {
            let codeBarre = document.querySelector('[name="i_code_barre"]').value;
            let messageErreur = document.getElementById('message-erreur-codebarre');

            if (codeBarre) {
                window.open('/articles/imprimer-code-barre/' + codeBarre, '_blank');
                messageErreur.style.display = 'none';
            } else {
                messageErreur.style.display = 'block';
            }
        }
    </script>


    @vite('resources/js/article_create.js')
@endpush
