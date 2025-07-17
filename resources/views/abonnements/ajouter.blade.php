@extends('layouts.main')
@section('document-title', 'Abonnements')
@push('styles')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">

@endpush
@section('page')
    <form action="{{route('abonnements.sauvegarder')}}" method="POST" class="needs-validation" novalidate
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
                                    <a href="{{ route('abonnements.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i
                                            class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i>
                                        Ajouter un abonnement
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


                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="client_select" class="form-label required">Client</label>
                                <div class="input-group d-grid" style="grid-template-columns:9fr 1fr ">
                                    <select required
                                            class="select2 form-control mb-3 custom-select {{ $errors->has('client_id') ? 'is-invalid' : '' }} "
                                            id="client_select" name="client_id">
                                    </select>
                                    <button type="button" id="ajout-client" data-url="{{ route('clients.ajouter') }}"
                                            class="btn btn-soft-secondary input-group-append ">+
                                    </button>
                                    @if ($errors->has('client_id'))
                                        <div class="invalid-feedback flex-fill">
                                            {{ $errors->first('client_id') }}
                                        </div>
                                    @endif
                                </div>

                            </div>
                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="article_select" class="form-label required">Type d'abonnement</label>
                                <div class="input-group">
                                    <select required
                                            class="select2 form-control mb-3 custom-select {{ $errors->has('article_id') ? 'is-invalid' : '' }} "
                                            id="article_select" name="article_id">
                                    </select>
                                    @if ($errors->has('article_id'))
                                        <div class="invalid-feedback flex-fill">
                                            {{ $errors->first('article_id') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12 col-lg-6 col-md-4 mb-3">
                                <label for="titre" class="form-label required">
                                    Titre
                                </label>
                                <input type="text"
                                       class="form-control {{ $errors->has('titre') ? 'is-invalid' : '' }}" id="titre"
                                       name="titre" value="{{ old('titre') }}">
                                @if ($errors->has('titre'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('titre') }}
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="date_debut" class="form-label required">
                                    Date d'abonnement
                                </label>
                                <input type="text"
                                       class="form-control {{ $errors->has('date_abonnement') ? 'is-invalid' : '' }}"
                                       id="date_abonnement" name="date_abonnement" required readonly
                                       value="{{ old('date_abonnement', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
                                @if ($errors->has('date_abonnement'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('date_abonnement') }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="date_fin" class="form-label ">
                                    Date d'expiration
                                </label>
                                <input type="text"
                                       class="form-control {{ $errors->has('date_expiration') ? 'is-invalid' : '' }}"
                                       id="date_expiration" name="date_expiration" readonly
                                       value="{{ old('date_expiration', Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y')) }}">
                                @if ($errors->has('date_expiration'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('date_expiration') }}
                                    </div>
                                @endif
                            </div>




                            <div class="col-12 col-mb-6 col-lg-3 col-md-4 mb-3">
                                <label for="prix" class="form-label">
                                    Prix
                                </label>
                                <input type="number"
                                       class="form-control {{ $errors->has('ca_global') ? 'is-invalid' : '' }}"
                                       id="prix"
                                       name="prix"
                                       value="{{ old('prix') }}">
                                @if ($errors->has('prix'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('prix') }}
                                    </div>
                                @endif
                            </div>

                            <div class=" col-12 col-lg-6 col-md-4 mb-6">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description"
                                          class="form-control summernote {{ $errors->has('description') ? 'is-invalid' : '' }}"
                                          id="description">{{ old('description') }}</textarea>
                                @if ($errors->has('description'))
                                    <div class="invalid-feedback">
                                        {{ $errors->first('description') }}
                                    </div>
                                @endif
                            </div>

                    </div>

                </div>
            </div>
        </div>
        </div>
    </form>
    <div class="modal fade " id="client-modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog  modal-dialog-centered">
            <div class="modal-content ">

            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>

        /* Dans le cas de retour des erreurs , les listes Select2 ne contient aucun éléménts donc le systeme ne peux supprimer aucune donnée , pour cette raison
        on doit relire et extraraire le nom de la valeur précedente
         */
        @if (old('client_id'))
        $.ajax({
            url: '{{ route('clients.afficher_ajax', old('client_id')) }}',
            success: function (response) {
                $('#client_select').append(
                    `<option value="{{ old('client_id') }}">${response.nom}</option>`).trigger(
                    'change')
            },
            error: function (xhr) {
            }
        })
        @endif

        @if (old('article_id'))
        $.ajax({
            url: '{{ route('article.afficher_ajax', old('article_id')) }}',
            success: function (response) {
                $('#article_select').append(
                    `<option value="{{ old('article_id') }}">${response.designation}</option>`).trigger(
                    'change')
            },
            error: function (xhr) {

            }
        })
        @endif





    </script>
    <script src="{{ asset('js/form-validation.init.js') }}"></script>
<script>
    $("#client_select").select2({
        width: "100%",
        placeholder: "Sélectionnez un client",
        minimumInputLength: 3, // Specify the ajax options for loading the product data
        ajax: {
            // The URL of your server endpoint that returns the product data
            url: __client_select2_route,
            cache: true, // The type of request, GET or POST
            type: "GET",
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data,
                };
            },
        },
    });

    $("#article_select").select2({
        width: "100%",
        placeholder: "Sélectionnez un article",
        minimumInputLength: 3, // Specify the ajax options for loading the product data
        ajax: {
            // The URL of your server endpoint that returns the product data
            url: "{{ route('article.select') }}",
            cache: true, // The type of request, GET or POST
            type: "GET",
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                return {
                    results: data,
                };
            },
        },
    });

    document.addEventListener("DOMContentLoaded", function () {
        $("#date_abonnement,#date_expiration").datepicker({
            autoclose: true,
            language: "fr",
            changeYear: false,
            showButtonPanel: true,
            format: "dd/mm/yyyy",
        });})


</script>
@endpush
