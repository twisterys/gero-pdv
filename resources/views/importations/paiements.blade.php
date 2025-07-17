


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
                                    Importer des paiements
                                </h5>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <form action="{{ route('importer-paiement') }}" method="POST" enctype="multipart/form-data" class="mt-3" onsubmit="return validateForm()">
                        @csrf

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="payable_type" class="form-label me-2"> Selectionner le type des paiements à importer</label>
                                <select class="form-select w-100" id="payable_type" name="payable_type">
                                    <option value="1">Vente</option>
                                    <option value="2">Achat</option>

                                </select>
                            </div>

                            @if(\App\Models\Magasin::count() > 1 )
                            <div class="col-md-4 mb-3">
                                <label class="form-label required" for="magasin">Magasin</label>
                                <select class="form-select" id="magasin" name="magasin" data-parsley-multiple="groups" data-parsley-mincheck="1">
                                    @foreach($o_magasins as $o_magasin)
                                        <option value="{{$o_magasin->id}}" >{{ $o_magasin->reference }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            &nbsp;
                        <label for="file" class="form-label me-2">Importer un fichier Excel</label>

                        <div class="mb-3 d-flex align-items-center w-50" >
                            <input style="display: none" type="file" name="file" class="filestyle" id="file"
                                   accept=".xlsx, .xls"
                                   data-btnClass="btn-soft-primary"
                                   data-buttonBefore="true" data-text="Choisir un fichier "
                                   data-placeholder="Pas de fichier">
                            <button class="btn btn-success" type="submit" disabled id="submitBtn">Importer</button>
                        </div>
                    </div>
                    </form>
                    <hr class="border">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ asset('modele_csv/import_paiements_v3.xlsx') }}" class="btn btn-soft-primary">Télécharger le modèle paiement</a>
                        </div>
{{--                        <div class="col-md-6 mb-3">Regarder la page d'instructions</div>--}}
                    </div>
                </div>
                </div>
{{--                <div class="card">--}}
{{--                    <div class="card-body">--}}
{{--                        <div class="card-title">--}}
{{--                            <div  class="d-flex justify-content-between align-items-center">--}}
{{--                                <h5 class="m-0">--}}
{{--                                    <i class="mdi mdi-account-alert me-2 text-success"></i>Règles d'importation des paiements--}}
{{--                                </h5>--}}
{{--                            </div>--}}
{{--                            <hr class="border">--}}
{{--                        </div>--}}
{{--                        <div class="table-responsive">--}}
{{--                            <table class="table table-bordered table-hover">--}}
{{--                                <thead>--}}
{{--                                <tr>--}}
{{--                                    <th style="width: 30%"><strong>Nom de la colonne</strong></th>--}}
{{--                                    <th style="width: 40%"><strong>Description</strong></th>--}}
{{--                                    <th><strong>Indication</strong></th>--}}
{{--                                </tr>--}}
{{--                                </thead>--}}
{{--                                <tbody>--}}
{{--                                <tr>--}}
{{--                                    <td>Vente Référence</td>--}}
{{--                                    <td>Numéro de référence unique pour la vente</td>--}}
{{--                                    <td style="color: red">Obligatoire</td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>Méthode de paiement</td>--}}
{{--                                    <td>Espèces, Chèques, LCN, Carte Bancaire, TPE, Virement Bancaire</td>--}}
{{--                                    <td style="color: red">Obligatoire</td>--}}

{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>Montant Payé</td>--}}
{{--                                    <td>Montant valide en DH</td>--}}
{{--                                    <td>--}}
{{--                                        <p style="color: red; display: inline;">Obligatoire</p>,--}}
{{--                                    </td>--}}
{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>Date Paiement</td>--}}
{{--                                    <td>Date de paiement valide au format <span style="color: red">d/m/Y</span></td>--}}
{{--                                    <td style="color: red">Obligatoire</td>--}}

{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>Chèque/LCN Référence</td>--}}
{{--                                    <td>En cas de paiement avec chèque ou lcn </td>--}}
{{--                                    <td>Optionnel</td>--}}

{{--                                </tr>--}}
{{--                                <tr>--}}
{{--                                    <td>Chèque/LCN Date</td>--}}
{{--                                    <td>Date valide du chèque ou lcn au format <span style="color: red">d/m/Y</span></td>--}}
{{--                                    <td>Optionnel</td>--}}

{{--                                </tr>--}}

{{--                                </tbody>--}}
{{--                            </table>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}


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

        // Trigger validation when file input changes
        document.getElementById('file').addEventListener('change', function() {
            validateForm();

        });





    </script>
@endpush
