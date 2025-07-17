@extends('layouts.main')
@section('document-title','Modifier Relance Template')

@push('styles')
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{ route('relance.mettre_a_jour', $template->id) }}" class="needs-validation"
                      novalidate autocomplete="off">
                    @csrf
                    @method('PUT')
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('relance.liste') }}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Modifier Paramètres de relance</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <div class="row col-12 mx-0">
                        <div class="col-md-6 col-sm-12 form-group mb-4">
                            <label for="name" class="form-label required">Nom de template</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $template->name) }}"
                            />
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 col-sm-12 form-group mb-4">
                            <label for="emails-input" class="form-label required">
                                Objet
                            </label>
                            <input value="{{ old('subject', $template->subject) }}" type="text" class="form-control @error('subject') is-invalid @enderror" name="subject">
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 col-sm-12 form-group mb-4">
                            <label for="type" class="form-label required">Type</label>
                            <select required name="type" class="form-select " id="type">
                                <option value="dv" @if(old('type', $template->type) == 'dv') selected @endif>Devis</option>
                                <option value="fa" @if(old('type', $template->type) == 'fa') selected @endif>Facture</option>
                                <option value="fp" @if(old('type', $template->type) == 'fp') selected @endif>Facture proforma</option>
                            </select>
                            @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-9 col-sm-12 form-group mb-4">
                            <label for="emails" class="form-label">
                                Emails en CC <span class="text-muted ms-2" style="font-size: 0.85em;"> (Entrez les emails séparés par " ; " )</span>
                            </label>
                            <input type="text" name="emails" id="emails" class="form-control @error('emails') is-invalid @enderror"
                                   value="{{ old('emails', $template->emails_cc) }}"
                                   placeholder="Entrez les emails séparés par ; "/>
                            @error('emails')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mb-4 mt-2">
                            <div class="form-check-inline d-flex align-items-center">
                                <label for="active" class="form-check-label me-2">Active</label>
                                <input name="active" value="1" type="checkbox" id="active" switch="bool" @if(old('active', $template->active)) checked @endif>
                                <label for="active" data-on-label="Oui" data-off-label="Non"></label>
                            </div>
                        </div>

                        <div class="col-md-9 col-sm-12 form-group mb-4 mt-2">
                            <label for="html-input" class="form-label required">HTML (corps)</label>
                            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="20">{{ old('content', $template->content) }}</textarea>
                            @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 col-sm-12 form-group mb-4 mt-2">
                            <label class="form-label mb-2">Tags HTML Disponibles</label>
                            <div class="border rounded p-3 shadow-sm bg-light">
                                <h6 class="mb-3 text-primary">Utilisez ces tags dans l'objet et le contenu HTML :</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>[CLIENT]</strong> : Nom du client</li>
                                    <li><strong>[DATE_EXPIRATION]</strong> : Date d'expiration</li>
                                    <li><strong>[DATE_EMISSION]</strong> : Date d'émission</li>
                                    <li><strong>[TOTAL]</strong> : Total ttc</li>
                                    <li><strong>[SOLDE]</strong> : Montant non payé</li>
                                    <li><strong>[REFERENCE]</strong> : Référence du document</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('libs/tinymce/jquery.tinymce.min.js') }}"></script>
    <script src="{{ asset('libs/tinymce/tinymce.min.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            tinymce.init({
                selector: "#content",
                menubar: !1,
                plugins: __tinymce_plugins,
                toolbar: __tinymce_toolbar,
                toolbar_mode: "floating",
                content_style:
                    "body { font-family:Helvetica,Arial,sans-serif; font-size:16px }",
            });
        });
    </script>
@endpush
