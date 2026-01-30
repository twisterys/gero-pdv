@extends('layouts.main')
@section('document-title', 'Clients')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <form action="{{ route('clients.sauvegarder') }}" method="POST" autocomplete="off">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('clients.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                        Ajouter un client
                                    </h5>
                                </div>
                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>

                            </div>
                            <hr class="border">
                        </div>
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
                                    <input type="text" class="form-control  @error('reference') is-invalid @enderror"
                                           id="reference" name="reference" @if(!$modifier_reference) readonly @endif
                                           maxlength="20" value="{{old('reference',$client_reference) }}" required>
                                    @error('reference')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="formJuridique"
                                           class="form-label required">Forme juridique</label>
                                    <select
                                        class="select2 form-control @error('forme_juridique') is-invalid @enderror  mb-3 custom-select"
                                        id="formJuridique"
                                        name="forme_juridique">
                                        @foreach ($form_juridique_types as $form_juridique)
                                            <option @if(old('forme_juridique')===$form_juridique->id) selected
                                                    @endif
                                                    id="{{$form_juridique->nom_sur_facture}}"
                                                    value="{{ $form_juridique->id }}">{{ $form_juridique->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('forme_juridique')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="nom"
                                           class="form-label required"
                                           id="dynamic_label">
                                        Dénomination
                                    </label>
                                    <input type="text" class="form-control  @error('nom') is-invalid @enderror" value="{{old('nom')}}" id="nom" name="nom" required>
                                    @error('nom')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="ville" class="form-label">
                                        Ville
                                    </label>
                                    <input type="text"
                                           class="form-control @error('ville') is-invalid @enderror"
                                           id="ville" value="{{old('ville')}}"
                                           name="ville">
                                    @error('ville')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="ice" class="form-label">
                                        ICE
                                    </label>
                                    <input type="text"
                                           class="form-control @error('ice') is-invalid @enderror" max="15"
                                           id="ice" value="{{old('ice')}}"
                                           name="ice">
                                    @error('ice')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input class="form-control @error('email') is-invalid @enderror"
                                           type="email" value="{{old('email')}}" name="email"
                                           id="example-email-input1">
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
                                    <input type="tel"
                                           class="form-control @error('telephone') is-invalid @enderror"
                                           id="telephone" value="{{old('telephone')}}" name="telephone">
                                    @error('telephone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                               <div class="row  mx-0">
                                   <div class="col-12  col-lg-6 mb-3  mb-3">
                                       <label for="note" class="form-label">
                                           Note
                                       </label>
                                       <textarea class="form-control @error('note') is-invalid @enderror"
                                                 style="resize: vertical"
                                                 placeholder="Ajouter note ici ....." id="note"
                                                 name="note" cols="30" rows="5">{{old('note')}}</textarea>
                                       @error('note')
                                       <div class="invalid-feedback">
                                           {{ $message }}
                                       </div>
                                       @enderror
                                   </div>
                                   <div class="col-12 col-lg-6 mb-3  mb-3">
                                       <label for="adresse" class="form-label">
                                           Adresse
                                       </label>
                                       <textarea class="form-control @error('adresse') is-invalid @enderror"
                                                 style="resize: vertical"
                                                 placeholder="Ajouter adress ici ....." id="adresse"
                                                 name="adresse" cols="30" rows="5">{{old('adresse')}}</textarea>
                                       @error('adresse')
                                       <div class="invalid-feedback">
                                           {{ $message }}
                                       </div>
                                       @enderror
                                   </div>
                               </div>
                            </div>
                            <div class="col-md-6 row mx-0 a col-12 align-items-start">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Gestion</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label" for="limite_de_credit-input">Limite de crédit
                                    </label>
                                    <div class="input-group">
                                        <input type="text" step="0.001" min="0"
                                               class="form-control @error('limite_de_credit') is-invalid @enderror"
                                               id="limite_de_credit"
                                               name="limite_de_credit" value="{{ old('limite_de_credit') }}">
                                            <span class="input-group-text">MAD</span>
                                        @error('limite_de_credit')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label" for="limite_ventes_impayees-input">Limite de ventes impayées
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="1" min="0"
                                               class="form-control @error('limite_ventes_impayees') is-invalid @enderror"
                                               id="limite_ventes_impayees"
                                               name="limite_ventes_impayees" value="{{ old('limite_ventes_impayees') }}">
                                            <span class="input-group-text">ventes</span>
                                        @error('limite_ventes_impayees')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label" for="remise-par-defaut-input">Remise par défaut
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.001" min="0" max="100"
                                               class="form-control @error('remise_par_defaut') is-invalid @enderror"
                                               id="remise_par_defaut"
                                               name="remise_par_defaut" value="{{ old('remise_par_defaut') }}">
                                        <span class="input-group-text">%</span>
                                        @error('remise_par_defaut')
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
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        var dynamicLabel = document.getElementById('dynamic_label');
        document.addEventListener('DOMContentLoaded', function () {
            $('#formJuridique').select2({
                width: '100%',
                placeholder: 'Selectioner un type'
            })
        });
    </script>
@endpush
