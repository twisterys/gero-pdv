


@extends('layouts.main')
@section('document-title', 'Importations')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
@endpush
@section('page')

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <p id="error-message" class="alert alert-danger" style="display: none;">Veuillez sélectionner un fichier Excel valide avec l'extension .xlsx ou .xls.</p>

    @if (session()->has('failures'))
        @foreach (session()->get('failures') as $validation)
            <tr>
                <td>{{ $validation->row() }}</td>
                <td>{{ $validation->attribute() }}</td>
                <td>
                    <ul>
                        @foreach ($validation->errors() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </td>
                <td>
                    {{ $validation->values()[$validation->attribute()] }}
                </td>
            </tr>
        @endforeach
    @endif


    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="card-title">
                        <div  class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{route('importer-liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3" style="margin-top: .1rem!important">
                                    Importer des produits
                                </h5>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <form action="{{ route('importer-produit') }}" method="POST" enctype="multipart/form-data" class="mt-3" onsubmit="return validateForm()">
                        @csrf
                        <label for="file" class="form-label me-2">Importer un fichier Excel</label>

                        <div class="mb-3 d-flex align-items-center w-50" >
                            <input style="display: none" type="file" name="file" class="filestyle" id="file"
                                   accept=".xlsx, .xls"
                                   data-btnClass="btn-soft-primary"
                                   data-buttonBefore="true" data-text="Choisir un fichier "
                                   data-placeholder="Pas de fichier">
                            <button class="btn btn-success" type="submit" disabled id="submitBtn">Importer</button>
                        </div>
                    </form>
                    <hr class="border">
                    <div class="mb-3">
                        @php
                        $name = $codeBarre ? 'import_produits_code_barre_v2.xlsx' : 'import_produits_v2.xlsx';
                        @endphp
                        <a href="{{ asset('modele_csv/'.$name) }}" class="btn btn-soft-primary">Télécharger le modèle produit</a>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div  class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i class="mdi mdi-account-alert me-2 text-success"></i>Règles d'importation
                            </h5>
                        </div>
                        <hr class="border">
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th style="width: 30%"><strong>Nom de la colonne</strong></th>
                                <th style="width: 40%"><strong>Description</strong></th>
                                <th><strong>Indication</strong></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>Référence</td>
                                <td>Numéro de référence unique pour le produit</td>
                                <td >Laissez ce champ vide pour attribuer automatiquement une valeur, Unique</td>
                            </tr>
                            @if($codeBarre)
                                <tr>
                                    <td>Code barre</td>
                                    <td>Code barre unique pour le produit</td>
                                    <td >Optionnel</td>
                                </tr>
                            @endif
                            <tr>
                                <td>Designation *</td>
                                <td>Nom du produit</td>
                                <td style="color: red;">Obligatoire</td>
                            </tr>
                            <tr>
                                <td>Nom Famille</td>
                                <td>Nom de la famille  du produit</td>
                                <td>Optionnel</td>
                            </tr>
                            <tr>
                                <td>Nom Unité *</td>
                                <td>Unité du produit </td>
                                <td style="color: red;">Obligatoire</td>
                            </tr>
                            <tr>
                                <td>Prix Vente *</td>
                                <td>Prix de vente du produit</td>
                                <td style="color: red;">Obligatoire</td>
                            </tr>
                            <tr>
                                <td>Taxe *</td>
                                <td>Taxe applicable au produit (0%, 7%, 10%, 16%, 20%)</td>
                                <td style="color: red;">Obligatoire</td>
                            </tr>
                            <tr>
                                <td>Stockable</td>
                                <td>un produit peut être stocké ou non</td>
                                <td style="color:red">Obligatoire</td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>

@endsection
@push('scripts')

    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/admin-resources/bootstrap-filestyle/bootstrap-filestyle.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(":file").filestyle();
        });
        function validateForm() {
            var fileInput = document.getElementById('file');
            var filePath = fileInput.value;
            var allowedExtensions = /(\.xlsx|\.xls)$/i;
            var errorMessage = document.getElementById('error-message');
            var submitBtn = document.getElementById('submitBtn');


            if (!allowedExtensions.exec(filePath)) {
                errorMessage.style.display = 'block';
                fileInput.value = '';
                submitBtn.disabled = true;
                return false;
            } else {
                errorMessage.style.display = 'none';
                submitBtn.disabled = false;
                return true;
            }
        }

        document.getElementById('file').addEventListener('change', function() {
            validateForm();

        });

    </script>
@endpush
