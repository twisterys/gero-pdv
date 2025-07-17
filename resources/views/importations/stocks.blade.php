


@extends('layouts.main')
@section('document-title', 'Importations')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/jquery-ui-dist/jquery-ui.structure.min.css') }}" rel="stylesheet">
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
                                    Ouverture de stock
                                </h5>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <form action="{{ route('importer-stock') }}" method="POST" enctype="multipart/form-data" class="mt-3" onsubmit="return validateForm()">
                        @csrf
                        <div class="row">
                            <div class="col-md-2 mb-3">
                                <label for="i_date" class="form-label required">Date de stock</label>
                                <input type="text" class="form-control" id="i_date" name="i_date" required readonly>

                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label required" for="magasin">Magasin</label>
                                <select class="form-select" id="magasin" name="magasin" data-parsley-multiple="groups" data-parsley-mincheck="1">
                                    @foreach($o_magasins as $o_magasin)
                                        <option value="{{$o_magasin->id}}" >{{ $o_magasin->reference }}</option>
                                    @endforeach
                                </select>
                            </div>
                            &nbsp;
                            <div class="col-md-6 mb-6">
                                <div class="d-flex align-items-end justify-content-between">
                                    <div class="w-100">
                                        <label for="file" class="form-label me-2">Importer un fichier Excel</label>
                                        <input style="display: none" type="file" name="file" class="filestyle" id="file" accept=".xlsx, .xls" data-btnClass="btn-soft-primary"
                                               data-buttonBefore="true" data-text="Choisir un fichier " data-placeholder="Pas de fichier">
                                    </div>
                                    <button class="btn btn-success" type="submit" disabled id="submitBtn">Importer</button>
                                </div>
                            </div>
                        </div>


                    </form>
                    <hr class="border">
                    <div class="mb-3">
                        <a href="{{ asset('modele_csv/import_stocks.xlsx') }}" class="btn btn-soft-primary">Télécharger le modèle stock</a>
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
                                <td>Référence Article </td>
                                <td>Numéro de référence unique de l'article</td>
                                <td style="color: red">Obligatoire</td>
                            </tr>
                            <tr>
                                <td>Quantité</td>
                                <td>-</td>
                                <td style="color: red">Obligatoire</td>
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
    <script src="{{ asset('libs/tinymce/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>
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


        $('#i_date').datepicker({
            autoclose: true,
            language:'fr',
            changeYear: false,
            showButtonPanel: true,
            format: 'dd/mm/yyyy',
            startDate: __exercice_start_date,
            endDate: __exercice_end_date,
        });

        var defaultDate = "{{ Carbon\Carbon::now()->setYear(session()->get('exercice'))->format('d/m/Y') }}";
        $('#i_date').datepicker('setDate', defaultDate);
    </script>
    <script src="{{ asset('js/form-validation.init.js') }}"></script>
        @vite('resources/js/vente_create.js')
@endpush
