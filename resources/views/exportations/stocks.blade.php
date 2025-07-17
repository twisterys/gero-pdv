


@extends('layouts.main')
@section('document-title', 'Exportation')
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
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('exporter-liste') }}"><i class="fa fa-arrow-left"></i></a>
                                <h5 class="m-0 float-end ms-3" style="margin-top: .1rem!important">
                                    Exporter votre stock
                                </h5>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <form id="export-form" action="{{route('exporter-stock') }}" method="POST" class="mt-3">
                        @csrf
                        @isset($o_magasins)
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label required" for="magasin">Magasin</label>
                                <select class="form-select" id="magasin" name="magasin" data-parsley-multiple="groups" data-parsley-mincheck="1">
                                    @foreach($o_magasins as $o_magasin)
                                        <option value="{{ $o_magasin->id }}">{{ $o_magasin->reference }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endisset
                        <hr class="border">
                        <div class="mb-3">
                            <button type="button"  class="btn btn-soft-primary" id="export-btn">Exporter stock</button>
                        </div>
                    </form>

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
    <script src="{{ asset('js/form-validation.init.js') }}"></script>
    <script>
        $('#export-btn').click(function (){
            $('#export-form').submit()
        })
    </script>
    @vite('resources/js/vente_create.js')
@endpush
