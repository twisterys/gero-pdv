@extends('layouts.main')

@section('document-title',__('Inventaire'))
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('libs/dropify/css/dropify.min.css')}}">
    <style>
        .last-col {
            width: 1%;
            white-space: nowrap;
        }
    </style>
@endpush
@section('page')

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0 float-end ">
                                    Exporter votre inventaire
                                </h5>
                            </div>
                        </div>
                    </div>

                    <p class="text-danger">
                        Pendant l'opération d'inventaire, éviter toute action susceptible d'affecter le stock. Cela
                        assurera un processus fluide et précis.
                    </p>
                    <form action="{{route('inventaire-exporter-stocks') }}" method="POST" enctype="multipart/form-data"
                          class="mt-3">
                        @csrf
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label required" for="magasin">Magasin</label>
                                <select class="form-select" id="magasin" name="magasin" data-parsley-multiple="groups"
                                        data-parsley-mincheck="1">
                                    @foreach($o_magasins as $o_magasin)
                                        <option value="{{ $o_magasin->id }}">{{ $o_magasin->reference }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="border">
                        <div class="mb-3">
                            <button type="submit" class="btn btn-soft-primary" id="export-btn">Exporter les stocks
                                d'articles
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <div  class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="m-0 float-end">
                                Importer un inventaire
                            </h5>
                        </div>
                    </div>
                    <hr class="border">
                    <form  action="{{ route('inventaire-importer-stocks') }}" method="POST" enctype="multipart/form-data" class="" onsubmit="return validateForm()">
                        @csrf
                        <div class="col-12">
                            <label for="i_reference" class="form-label required">
                                Référence
                            </label>
                            <input type="text"
                                   class="form-control {{$errors->has('i_reference')?'is-invalid':''}}"
                                   id="i_reference" name="i_reference"
                                   value="IVT-{{ \Carbon\Carbon::now()->format('YmdHis') }}">
                            @if ($errors->has('i_reference'))
                                <div class="invalid-feedback">
                                    {{ $errors->first('i_reference') }}
                                </div>
                            @endif
                        </div>
                        <hr class="border">

                        <label for="type" class="form-label me-2 required"> Selectionner le type d'inventaire</label>
                        <select class="form-select " id="type" name="type">
                            <option value="ordinaire" selected>Ordinaire</option>
                            <option value="extra_ordinaire">Extra Ordinaire</option>

                        </select>
                        <hr class="border">

                        <label class="form-label required me-2" for="magasin">Magasin</label>
                        <select class="form-select " id="magasin" name="magasin" data-parsley-multiple="groups" data-parsley-mincheck="1">
                            @foreach($o_magasins as $o_magasin)
                                <option value="{{$o_magasin->id}}" >{{ $o_magasin->reference }}</option>
                            @endforeach
                        </select>
                        <hr class="border">
                        <p id="error-message" class="alert alert-danger " style="display: none;">Veuillez sélectionner un fichier Excel valide avec l'extension .xlsx ou .xls.</p>
                        <label for="file" class="form-label me-2">Importer un fichier Excel</label>
                        <div class=" d-flex align-items-center " >
                            <input style="display: none" type="file" name="file" class="filestyle" id="file"
                                   accept=".xlsx, .xls"
                                   data-btnClass="btn-soft-primary"
                                   data-buttonBefore="true" data-text="Choisir un fichier "
                                   data-placeholder="Pas de fichier">
                            <button class="btn btn-success" type="submit" disabled id="submitBtn">Importer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

@endsection


@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script src="{{ asset('libs/dropify/js/dropify.min.js')}}"></script>
    <script src="{{ asset('libs/admin-resources/bootstrap-filestyle/bootstrap-filestyle.min.js') }}"></script>

    <script src="{{asset('js/form-validation.init.js')}}"></script>
    <script>
        $(document).ready(function () {
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
        document.getElementById('file').addEventListener('change', function () {
            validateForm();
        });

    </script>
@endpush
