@extends('layouts.main')
@section('document-title', 'Commerciaux')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('libs/dropify/css/dropify.min.css') }}">
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <form action="{{ route('commercials.mettre_a_jour', $o_commercial->id) }}" method="POST"
                        class="needs-validation" novalidate enctype="multipart/form-data">
                        <div class="card-title">
                            <div class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('commercials.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts  me-2 text-success"></i>
                                        Modifier un commercial</h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>

                        @csrf
                        @method('PUT')
                        <div class="row px-3 align-items-start ">
                            <div class="row col-md-6 ">

                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">
                                        Informations juridiques</h5>
                                    <hr class="border border-success">
                                </div>

                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="reference " class="form-label required">
                                        Référence
                                    </label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror "
                                        id="reference" value="{{ old('reference', $o_commercial->reference) }}"
                                        name="reference" maxlength="20" required
                                        @if (!$modifier_reference) readonly @endif>

                                    @if ($errors->has('reference'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('reference') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="type_commercial" class="form-label required">Type</label>
                                    <select class="form-select @error('type_commercial') is-invalid @enderror"
                                        id="type_commercial" name="type_commercial">

                                        @foreach ($type_de_commercial as $key => $label)
                                            <option value="{{ $key }}"
                                                {{ $o_commercial->type_commercial == $key ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @if ($errors->has('type_commercial'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('type_commercial') }}
                                        </div>
                                    @endif

                                </div>

                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="nom" class="form-label required" id="dynamic_label">
                                        Dénomination
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                        value="{{ old('name', $o_commercial->nom) }}" id="nom" name="nom"
                                        required>
                                    @if ($errors->has('nom'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('nom') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email"
                                        value="{{ old('email', $o_commercial->email) }}" name="email"
                                        id="example-email-input1">
                                    @if ($errors->has('email'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('email') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="telephone" class="form-label">
                                        Téléphone
                                    </label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                        id="telephone" value="{{ old('telephone', $o_commercial->telephone) }}"
                                        name="telephone">
                                    @if ($errors->has('telephone'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('telephone') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="secteur" class="form-label">
                                        Secteur
                                    </label>
                                    <input class="form-control @error('secteur') is-invalid @enderror"
                                        value="{{ old('note', $o_commercial->secteur) }}" id="secteur" name="secteur">
                                    @if ($errors->has('secteur'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('secteur') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="note" class="form-label">
                                        Note
                                    </label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" style="resize: vertical"
                                        placeholder="Ajouter note ici ....." id="note" name="note" cols="30" rows="8">{{ old('note', $o_commercial->note) }}</textarea>
                                    @if ($errors->has('note'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('note') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label for="i_image"
                                        class="form-label {{ $errors->has('i_image') ? 'is-invalid' : '' }}">Image</label>
                                    <input name="i_image" type="file" id="i_image" accept="image/*"
                                        @if ($o_commercial->image) data-default-file="{{ route('commercials.image.load', $o_commercial->image) }}" @endif>
                                    <div class="invalid-feedback">
                                        @if ($errors->has('i_image'))
                                            {{ $errors->first('i_image') }}
                                        @endif
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-6 row mx-0 a col-12 align-items-start">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Objectif</h5>
                                    <hr class="border border-success">
                                </div>

                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label" for="vente_ht-input">Commission par défaut
                                    </label>
                                    <div class="input-group">

                                        <input type="number" min="0" step="0.001"
                                            class="form-control @error('commission_par_defaut') is-invalid @enderror"
                                            id="commission_par_defaut" name="commission_par_defaut"
                                            value="{{ old('commission_par_defaut', $o_commercial->commission_par_defaut) }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">%</span>
                                        </div>
                                    </div>

                                    @if ($errors->has('commission_par_defaut'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('commission_par_defaut') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-12 col-lg-6 mb-3  ">
                                    <label class="form-label" for="vente_ht-input">Objectif
                                    </label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="0.001"
                                            class="form-control @error('objectif') is-invalid @enderror" id="objectif"
                                            name="objectif" value="{{ old('objectif', $o_commercial->objectif) }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">MAD</span>
                                        </div>
                                    </div>
                                    @if ($errors->has('objectif'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('objectif') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/dropify/js/dropify.min.js') }}"></script>

    <script>
        $("#i_image").dropify({
            messages: {
                default: "Glissez-déposez un fichier ici ou cliquez",
                replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
                remove: "Supprimer",
                error: "Désolé, le fichier trop volumineux",
            },
        });
        $('#i_image').on('dropify.afterClear', function(event, element) {
            if ($('#i_supprimer_image').length) {
                $('#i_supprimer_image').val(1)
            } else {
                $('#articles-form').append(
                    '<input id="i_supprimer_image" type="hidden" name="i_supprimer_image" value="1" >');
            }
        });
    </script>
@endpush
