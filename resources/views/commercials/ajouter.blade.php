@extends('layouts.main')
@section('document-title', ' Ajouter un commercial')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('libs/dropify/css/dropify.min.css') }}">
@endpush
@section('page')
    <form action="{{ route('commercials.sauvegarder') }}" method="POST" class="needs-validation"
        enctype="multipart/form-data" novalidate autocomplete="off">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('commercials.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts  me-2 text-success"></i>
                                        Ajouter un commercial</h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        @csrf
                        <div class="row px-3 align-items-start ">
                            <div class="row col-md-6 ">

                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">
                                        Informations personnelles</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="reference " class="form-label required">
                                        Référence
                                    </label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror"
                                        id="reference" name="reference" @if (!$modifier_reference) readonly @endif
                                        maxlength="20" value="{{ old('reference', $commercial_reference) }}" required>
                                    @error('reference')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="formJuridique" class="form-label required">Type</label>
                                    <select
                                        class="select2 form-control mb-3 form-select @error('type_de_commercial') is-invalid @enderror"
                                        id="type_commercial" name="type_commercial">
                                        @foreach ($type_de_commercial as $key => $label)
                                            <option @if (old('type_de_commercial') === $key)  @endif value="{{ $key }}">
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('type_commercial')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror

                                </div>

                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="nom" class="form-label required" id="dynamic_label">
                                        Dénomination
                                    </label>
                                    <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                        id="nom" value="{{ old('nom') }}" name="nom" required>

                                    @error('nom')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email"
                                        value="{{ old('email') }}" name="email" id="example-email-input1">
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="telephone" class="form-label">
                                        Téléphone
                                    </label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                        id="telephone" value="{{ old('telephone') }}" name="telephone">
                                    @error('telephone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="zone_geographique " class="form-label">
                                        Secteur
                                    </label>
                                    <input class="form-control @error('secteur') is-invalid @enderror" id="secteur"
                                        value="{{ old('secteur') }}" name="secteur">
                                    @error('secteur')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-md-12 col-lg-6 col-12">
                                    <label for="note" class="form-label">
                                        Note
                                    </label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" style="resize: vertical"
                                        placeholder="Ajouter note ici ....." id="note" name="note" cols="30" rows="8">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label for="i_image"
                                        class="form-label {{ $errors->has('i_image') ? 'is-invalid' : '' }}">Image</label>
                                    <input name="i_image" type="file" id="i_image" accept="image/*">
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
                                            value="{{ old('commission_par_defaut') }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">%</span>
                                        </div>
                                    </div>

                                    @error('commission_par_defaut')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-12 col-lg-6 mb-3  ">
                                    <label class="form-label" for="vente_ht-input">Objectif
                                    </label>
                                    <div class="input-group">
                                        <input type="number" min="0" step="0.001"
                                            class="form-control @error('objectif') is-invalid @enderror" id="objectif"
                                            name="objectif" value="{{ old('limite_de_credit') }}">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="basic-addon1">MAD</span>
                                        </div>
                                    </div>
                                    @error('objectif')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
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
    <script src="{{ asset('libs/dropify/js/dropify.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $('#formJuridique').select2({
                width: '100%',
                placeholder: 'Selectioner un type'
            })
        });
    </script>
    <script>
        $("#i_image").dropify({
            messages: {
                default: "Glissez-déposez un fichier ici ou cliquez",
                replace: "Glissez-déposez un fichier ou cliquez pour remplacer",
                remove: "Supprimer",
                error: "Désolé, le fichier trop volumineux",
            },
        });
    </script>
@endpush
