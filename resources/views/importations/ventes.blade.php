
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
                                    Importer des ventes
                                </h5>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <form action="{{ route('importer-vente') }}" method="POST" enctype="multipart/form-data" class="mt-3" onsubmit="return validateForm()">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="fileType" class="form-label me-2"> Selectionner le type des ventes à importer</label>
                                <select class="form-select w-100" id="fileType" name="fileType">
                                    <option value="dv">Devis</option>
                                    <option value="bc">Commande</option>
                                    <option value="fa">Facture</option>
                                    <option value="fp">Proforma</option>
                                    <option value="bl">Livraison</option>
                                    <option value="fb">Fabrication</option>
                                    <option value="br">Retour</option>
                                    <option value="ava">Avoir</option>
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
                            <div class="col-md-6 mb-6">
                                <div class="d-flex align-items-end justify-content-between">
                                    <div class="w-100">
                                        <label for="file" class="form-label me-2">Importer un fichier Excel</label>

                                        <input style="display: none" type="file" name="file" class="filestyle" id="file"
                                           accept=".xlsx, .xls"
                                           data-btnClass="btn-soft-primary"
                                           data-buttonBefore="true" data-text="Choisir un fichier "
                                           data-placeholder="Pas de fichier">
                                    </div>
                                     <button class="btn btn-success" type="submit" disabled id="submitBtn">Importer</button>
                                </div>
                        </div>
                    </div>

                    </form>
                    <hr class="border">
                    <div class="mb-3">
                        <a href="{{ asset('modele_csv/import_ventes_v2.xlsx') }}" class="btn btn-soft-primary">Télécharger le modèle vente</a>
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

        // Trigger validation when file input changes
        document.getElementById('file').addEventListener('change', function() {
            validateForm();
        });

    </script>
@endpush
