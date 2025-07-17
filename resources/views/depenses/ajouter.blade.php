@extends('layouts.main')
@section('document-title', 'Ajouter un dépense')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.structure.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
@endpush
@section('page')
    <form action="{{route('depenses.sauvegarder')}}" method="POST" class="needs-validation" novalidate
          autocomplete="off">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{route('depenses.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                        Ajouter une dépense </h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        @csrf
                        <div class="row">


                            <!-- First Row -->
                            <div class="col-md-6 col-12 row mx-0 align-content-start">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Détails de dépense</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class=" col-sm-6 col-12 mb-3">
                                    <label for="categorie_select" class="form-label required">Catégorie</label>
                                    <div class="input-group d-grid" style="grid-template-columns:9fr 1fr ">
                                        <select required
                                                class="select2 form-control mb-3 custom-select {{$errors->has('i_categorie')?'is-invalid':''}} "
                                                id="categorie_select"
                                                name="i_categorie">
                                        </select>
                                        <a href="{{route('categories.liste')}}" target="_blank"
                                           class="input-group-text justify-content-center"><span
                                                class="fa fa-plus"></span></a>
                                        @if ($errors->has('i_categorie'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('i_categorie') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="reference">Référence</label>
                                    <input required type="text"
                                           class="form-control {{$errors->has('i_reference') ? 'is-invalid' : ''}}"
                                           value="{{$referenceDepanse}}"
                                           id="reference" name="i_reference">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_reference'))
                                            {{ $errors->first('i_reference') }}
                                        @endif
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="nom-depense-input">Nom dépense</label>
                                    <input required type="text"
                                           class="form-control {{$errors->has('i_nom_depense') ? 'is-invalid' : ''}}"
                                           id="nom-depense-input" name="i_nom_depense" value="{{old('i_nom_depense')}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_nom_depense'))
                                            {{ $errors->first('i_nom_depense') }}
                                        @endif
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="i_pour">Bénéficiaire</label>
                                    <input required type="text"
                                           class="form-control {{$errors->has('i_pour') ? 'is-invalid' : ''}}"
                                           id="i_pour" name="i_pour" value="{{old('i_pour')}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_pour'))
                                            {{ $errors->first('i_pour') }}
                                        @endif
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-12 mb-3">
                                    <label class="form-label required" for="i_date_operation">Date de dépense</label>
                                    <input required type="date"
                                           class="form-control {{$errors->has('i_date_operation') ? 'is-invalid' : ''}}"
                                           id="i_date_operation" name="i_date_operation"
                                           value="{{old('i_date_operation', now()->format('Y-m-d'))}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_date_operation'))
                                            {{ $errors->first('i_date_operation') }}
                                        @endif
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-12 mb-3 ">
                                    <label class="form-label   required" for="montant-input">Montant</label>
                                    <div class="input-group">
                                        <input required type="number" step="0.01"
                                               class="form-control {{$errors->has('i_montant')? 'is-invalid' : ''}}"
                                               id="montant-input" min="0"
                                               name="i_montant" value="{{old('i_montant',0)}}">
                                        <span class="input-group-text">MAD</span>
                                        <div class="invalid-feedback">
                                            @if($errors->has('i_montant'))
                                                {{ $errors->first('i_montant') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-12 mb-3 ">
                                    <label class="form-label   required" for="tax-input">Impôt</label>
                                    <div class="input-group">
                                        <select name="i_tax" id="tax-input" class="form-select" >
                                            @foreach($taxes as $taxe)
                                                <option @selected(old('i_tax') == $taxe->valeur) value="{{$taxe->valeur}}">{{$taxe->nom}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @if($errors->has('i_tax'))
                                                {{ $errors->first('i_tax') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-sm-6 col-12 mb-3 ">
                                    <label class="form-label " for="ttc-input">Montant ttc</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="ttc-input" value="{{number_format(old('i_montant')*(1+old('i_tax')/100),2)}}">
                                    </div>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label" for="desc-input">Description</label>
                                    <textarea name="i_description"
                                              class="form-control {{$errors->has('description') ? 'is-invalid' : ''}}"
                                              style="resize: vertical" placeholder="Tapez votre description ici..."
                                              id="desc-input" cols="30" rows="8">{{old('description')}}</textarea>
                                    <div class="invalid-feedback">
                                        @if($errors->has('description'))
                                            {{ $errors->first('description') }}
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Second Row -->
                            <div class="col-md-6 col-12 row mx-0 align-content-start paiement">
                                <div class="col-12 mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="text-muted">Paiement</h5>
                                        <div class="d-flex align-items-center">
                                            <input name="regle" value="1" type="checkbox" checked id="regle-checkbox"
                                                   switch="bool">
                                            <label for="regle-checkbox" data-on-label="Oui"
                                                   data-off-label="Non"></label>
                                        </div>
                                    </div>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-sm-6 col-12 mb-3">
                                    <label for="compte-input" class="form-label required ">Compte</label>
                                    <select required name="i_compte_id"
                                            class="form-control {{$errors->has('i_compte_id') ? 'is-invalid' : ''}}"
                                            style="width: 100%" id="compte-input">
                                        @foreach($comptes as $compte)
                                            <option
                                                @selected(old('i_compte_id',$compte->principal)) value="{{$compte->id}}">{{$compte->nom}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_compte_id'))
                                            {{ $errors->first('i_compte_id') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12 mb-3">
                                    <label for="method-input" class="form-label required">Méthode de paiement</label>
                                    <select required name="i_method_key"
                                            class="form-control {{$errors->has('i_method_key') ? 'is-invalid' : ''}}"
                                            style="width: 100%" id="method-input">
                                        @foreach($methodes as $methode)
                                            <option @selected(old('i_method_key') == $methode->key) value="{{$methode->key}}">{{$methode->nom}}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_method_key'))
                                            {{ $errors->first('i_method_key') }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12 mb-3 __variable">
                                    <label for="date" class="form-label required">Date prévu</label>
                                    <div class="input-group">
                                        <input required
                                               class="form-control datupickeru {{$errors->has('i_date') ? 'is-invalid' : ''}}"
                                               data-provide="datepicker" data-date-autoclose="true" type="text"
                                               name="i_date" id="date">
                                        <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                                        @if($errors->has('i_date'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('i_date') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-6 col-12 mb-3 __variable">
                                    <label for="i_reference_paiement" class="form-label required">Référence de
                                        chéque</label>
                                    <input required
                                           class="form-control {{$errors->has('i_reference_paiement') ? 'is-invalid' : ''}}"
                                           type="text" name="i_reference_paiement" id="i_reference_paiement">
                                    @if($errors->has('i_reference_paiement'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('i_reference_paiement') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-sm-6 col-12 mb-3">
                                    <label for="i_note" class="form-label">Note</label>
                                    <textarea name="i_note" id="i_note" cols="30" rows="3"
                                              class="form-control {{$errors->has('i_note') ? 'is-invalid' : ''}}"></textarea>
                                    @if($errors->has('i_note'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('i_note') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>

@endsection
@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{asset('libs/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('libs/daterangepicker/js/daterangepicker.js')}}"></script>
    <script src="{{asset('libs/dropify/js/dropify.min.js')}}"></script>
    @vite('resources/js/article_create.js')
    <script>

        $('#categorie_select').select2({
            width: '100%',
            placeholder: 'Sélectionnez un categorie',
            ajax: {
                url: '{{ route('categories.select') }}', // Remplacez 'categories.select2' par la route appropriée de votre application
                cache: true,
                type: 'GET', processResults: function (data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                },
            }
        });

        @if(old('i_categorie'))
        $.ajax({
            url: '{{route('categories.afficher_ajax',old('i_categorie'))}}',
            success: function (response) {
                console.log(response);
                $('#categorie_select').append(`<option value="{{old('i_categorie')}}">${response.nom}</option>`).trigger('change')
            },
            error: function (xhr) {
            }
        })
        @endif
        function check() {
            let methods = ['cheque', 'lcn'];
            if (methods.indexOf($('#method-input').find('option:selected').val()) !== -1) {
                $('.__variable').removeClass('d-none').find('input').attr('required', '')
            } else {
                $('.__variable').addClass('d-none').find('input').removeAttr('required')
            }
        }

        $(document).on('change', '#method-input', function () {
            check()
        })
        check()
        $('#compte-input , #method-input, #tax-input').select2({
            minimumResultsForSearch: -1
        });
        $(document).on('change', '#regle-checkbox', function () {
            let checked = $(this).is(':checked')
            if (!checked) {
                $('.paiement').find('input[type="text"],select,textarea').attr('disabled', '')
            } else {
                $('.paiement').find('input[type="text"],select,textarea').removeAttr('disabled')
            }
        })

        $(document).on('input', '#tax-input, #montant-input', function () {
            let ttc = (Number($('#montant-input').val()) *(1+ Number($('#tax-input').val()/100))).toFixed(2);
            $('#ttc-input').val(ttc)
        })
    </script>
@endpush
