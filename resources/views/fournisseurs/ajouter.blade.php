@extends('layouts.main')
@section('document-title', 'Fournisseurs')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <form action="{{ route('fournisseurs.sauvegarder') }}" method="POST" class="needs-validation" novalidate
        autocomplete="off">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                                <div>
                                    <a href="{{ route('fournisseurs.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts me-2 text-success"></i>
                                        Ajouter un fournisseur</h5>
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
                                        Informations juridiques</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="reference " class="form-label required">
                                        Référence
                                    </label>
                                    <input type="text" class="form-control  @error('reference') is-invalid @enderror"
                                           id="reference" name="reference"
                                           maxlength="20" value="{{old('reference',$fournisseur_reference) }}" @if(!$modifier_reference) readonly @endif required>
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
                                    <label for="ice" class="form-label">
                                        ICE
                                    </label>
                                    <input type="text"
                                           class="form-control @error('ice') is-invalid @enderror" max="10"
                                           id="ice" value="{{old('ice')}}"
                                           oninput="validation_number_input(this)" name="ice">
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
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="rib" class="form-label">
                                        RIB
                                    </label>
                                    <input type="text"
                                           class="form-control @error('rib') is-invalid @enderror"
                                           id="rib" value="{{old('rib')}}"
                                            name="rib">
                                    @error('rib')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="row m-0 p-0 col-12">
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
                                    <div class="col-12  col-lg-6 mb-3  mb-3">
                                        <label for="note" class="form-label">
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
                            <div class="col-sm-6 row mx-0 a col-12 align-items-start">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Gestion</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label" for="vente_ht-input">Limite de crédit
                                    </label>
                                    <div class="input-group">
                                        <input type="text" step="0.01" min="0"
                                               class="form-control @error('limite_de_credit') is-invalid @enderror"
                                               id="limite_de_credit"
                                               name="limite_de_credit" value="{{ old('limite_de_credit') }}">
                                            <span class="input-group-text" id="basic-addon1">MAD</span>
                                        @error('limite_de_credit')
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
        $('#rib').inputmask("999 999 9999999999999999 99")
        document.addEventListener('DOMContentLoaded', function() {

            $('#formJuridique').select2({
                width: '100%',
                placeholder: 'Selectioner un type'
            })

            $('#addContact').click(function() {
                addContactRow();
            });

            // Show the default input field based on the initial selected value

            function getSelectedRow() {
                var selectedRow = $('#table-contacts tbody tr').index($(
                    '#table-contacts tbody tr:has(.radio:checked)'));
                return selectedRow;
            }


        });
    </script>
@endpush
