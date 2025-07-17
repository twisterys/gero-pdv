@extends('layouts.main')
@section('document-title', 'Modifier un dépense')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <form action="{{route('depenses.mettre-a-jour', $o_depense->id)}}" method="POST" class="needs-validation" novalidate autocomplete="off">
       @csrf
        @method("PUT")
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">
                            <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{route('depenses.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                        Modifier une dépense</h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>

                        <div class="row">


                            <!-- First Row -->
                            <div class="col-12 row mx-0">
                                <div class="col-md-4 mb-3 @if ($magasins_count  <= 1) d-none @endif">
                                    <label for="magasin_id" class="form-label required">
                                        Magasin
                                    </label>
                                    <select name="magasin_id" {{count($o_magasins) <=1 ? 'readonly':null }}
                                        class="form-control {{ $errors->has('magasin_id') ? 'is-invalid' : '' }}"
                                        id="magasin-select">
                                        @foreach ($o_magasins as $o_magasin)
                                            <option value="{{ $o_magasin->id }}" @if($o_depense->magasin_id == $o_magasin->id) selected @endif>{{ $o_magasin->text }}</option>
                                        @endforeach
                                    </select>
                                    @error('magasin_id')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="categorie_select" class="form-label required">Catégorie</label>
                                    <div class="input-group d-grid" style="grid-template-columns:9fr 1fr ">
                                        <select required
                                                class="select2 form-control mb-3 custom-select {{$errors->has('i_categorie')?'is-invalid':''}} "
                                                id="categorie_select"
                                                name="i_categorie">
                                            @if($o_depense?->categorie_depense_id)
                                                <option selected
                                                        value="{{$o_depense->categorie_depense_id}}">{{$o_depense->categorie->nom}}</option>
                                            @endif

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
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required" for="reference">Référence</label>
                                    <input required type="text" class="form-control {{$errors->has('i_reference') ? 'is-invalid' : ''}}" id="reference"
                                           value="{{ old('reference', $o_depense->reference) }}" name="i_reference" @if(!$modifier_reference) readonly @endif>
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_reference'))
                                            {{ $errors->first('i_reference') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label required" for="nom-depense-input">Nom dépense</label>
                                    <input required type="text" class="form-control {{$errors->has('i_nom_depense') ? 'is-invalid' : ''}}"
                                           value="{{$o_depense->nom_depense}}" id="nom-depense-input" name="i_nom_depense" value="{{old('i_nom_depense')}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_nom_depense'))
                                            {{ $errors->first('i_nom_depense') }}
                                        @endif
                                    </div>
                                </div>

{{--                                <div class="col-md-4 mb-3">--}}
{{--                                    <label class="form-label required" for="unity-select">Categorie</label>--}}
{{--                                    <select required class="select2 form-control mb-3 custom-select {{$errors->has('i_categorie') ? 'is-invalid' : ''}}" name="i_categorie" id="unity-select">--}}
{{--                                        @if(isset($categories))--}}
{{--                                            @foreach($categories as $categorie)--}}
{{--                                                <option @if($categorie['id']==$o_depense->categorie_depense_id) selected @endif value="{{$categorie['id']}}">{{$categorie['nom']}}</option>--}}
{{--                                            @endforeach--}}
{{--                                        @endif--}}
{{--                                    </select>--}}
{{--                                    <div class="invalid-feedback">--}}
{{--                                        @if($errors->has('i_categorie'))--}}
{{--                                            {{ $errors->first('i_categorie') }}--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                </div>--}}

                            </div>

                            <!-- Second Row -->
                            <div class="col-12 row mx-0">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label required" for="i_pour">Bénéficiaire</label>
                                    <input required type="text" class="form-control {{$errors->has('i_pour') ? 'is-invalid' : ''}}"
                                            id="i_pour" name="i_pour" value="{{$o_depense->pour}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_pour'))
                                            {{ $errors->first('i_pour') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label required" for="i_date_operation">Date de dépense</label>
                                    <input required type="date" class="form-control {{$errors->has('i_date_operation') ? 'is-invalid' : ''}}" id="i_date_operation" name="i_date_operation" value="{{$o_depense->date_operation}}">
                                    <div class="invalid-feedback">
                                        @if($errors->has('i_date_operation'))
                                            {{ $errors->first('i_date_operation') }}
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-4 mb-3 ">
                                    <label class="form-label   required" for="montant-input">Montant</label>
                                    <div class="input-group">
                                        <input required type="number" step="0.01"
                                               class="form-control {{$errors->has('i_montant')? 'is-invalid' : ''}}"
                                               id="montant-input" min="0"
                                               name="i_montant" value="{{$o_depense->montant}}">
                                        <span class="input-group-text">MAD</span>
                                        <div class="invalid-feedback">
                                            @if($errors->has('i_montant'))
                                                {{ $errors->first('i_montant') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-md-4 col-12 mb-3 ">
                                    <label class="form-label   required" for="tax-input">Impôt</label>
                                    <div class="input-group">
                                        <select name="i_tax" id="tax-input" class="form-select" >
                                            @foreach($taxes as $taxe)
                                                <option @selected(old('i_tax',$o_depense->taxe) == $taxe->valeur) value="{{$taxe->valeur}}">{{$taxe->nom}}</option>
                                            @endforeach
                                        </select>
                                        <div class="invalid-feedback">
                                            @if($errors->has('i_tax'))
                                                {{ $errors->first('i_tax') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class=" col-md-4 col-12 mb-3 ">
                                    <label class="form-label " for="ttc-input">Montant ttc</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="ttc-input" value="{{number_format(old('i_montant',$o_depense->montant)*(1+old('i_tax',$o_depense->taxe)/100),2)}}">
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label" for="desc-input">Description</label>
                                    <textarea name="i_description" class="form-control {{$errors->has('description') ? 'is-invalid' : ''}}" style="resize: vertical" placeholder="Tapez votre description ici..." id="desc-input" cols="30" rows="8">{{$o_depense->description}}</textarea>
                                    <div class="invalid-feedback">
                                        @if($errors->has('description'))
                                            {{ $errors->first('description') }}
                                        @endif
                                    </div>
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
            minimumInputLength: 3, // Specify the ajax options for loading the product data
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

        $('#magasin-select').select2({
            minimumResultsForSearch: -1,
            width: '100%'
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
