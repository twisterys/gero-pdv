@extends('layouts.main')
@section('document-title','Paramètres Abonnement')

@push('styles')
    <link href="{{ asset('libs/summernote/summernote.min.css') }}" rel="stylesheet">
    <style>
        #emails-display {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
        }

        .email-tag {
            background-color: #007bff;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 14px;
        }

        .email-tag:hover {
            background-color: #0056b3;
            cursor: pointer;
        }

    </style>
@endpush
@section('page')
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data"
                      action="{{ route('abonnementsSettings.mettre_a_jour') }}" class="needs-validation"
                      novalidate autocomplete="off">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div id="__fixed" class="d-flex switch-filter justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <a href="{{ route('parametres.liste') }}"><i class="fa fa-arrow-left text-success me-2"></i></a>
                                <h5 class="m-0">Paramétres d'abonnement</h5>
                            </div>
                            <div class="pull-right">
                                <button id="save-btn" class="btn btn-soft-info"><i class="fa fa-save"></i> Sauvegarder</button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    @csrf
                    <div class="row col-12 mx-0">
                        <div class="col-md-6 col-sm-12 form-group mb-4">
                            <label for="emails" class="form-label required">Emails</label>
                            <input type="text" name="emails" id="emails" class="form-control @error('emails') is-invalid @enderror"
                                   value="{{ old('emails', $abonnements_settings->emails ?? '') }}"
                                   placeholder="Entrez les emails séparés par ;"
{{--                                   oninput="highlightEmails()" --}}
                            />
                            <div id="emails-display" class="mt-2"></div> <!-- Div to display emails -->
                            @error('emails')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="col-md-6 col-sm-12 form-group mb-4">
                            <label for="emails-input" class="form-label required">
                                Objet
                            </label>
                            <input  value="{{ old('subject', $abonnements_settings->subject ?? '') }}" type="text" class="form-control @error('subject') is-invalid @enderror" name="subject">
                            @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 col-sm-12 form-group mb-4">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="notifier_client"
                                       name="notifier_client"
                                       value="1"
                                    {{ old('notifier_client', $abonnements_settings->notifier_client ?? false) ? 'checked' : '' }}>
                                <label class="form-check-label" for="notifier_client">
                                    Notifier les clients
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 col-sm-12 form-group mb-4">
                            <label for="html-input" class="form-label required">HTML (corps)</label>
                            <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="20" >{{ old('content', $abonnements_settings->content ?? '') }}</textarea>
                            @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Bloc Informations -->
                        <div class="col-md-6 col-sm-12 form-group mb-4">
                            <label class="form-label mb-2">Tags HTML Disponibles</label>
                            <div class="border rounded p-3 shadow-sm bg-light">
                                <h6 class="mb-3 text-primary">Utilisez ces tags dans le contenu HTML :</h6>
                                <ul class="list-unstyled mb-0">
                                    <li><strong>[CLIENT]</strong> : Nom du client</li>
                                    <li><strong>[TYPE]</strong> : Type d'abonnement</li>
                                    <li><strong>[TITRE]</strong> : Titre de l'abonnement</li>
                                    <li><strong>[PRIX]</strong> : Prix de l'abonnement</li>
                                    <li><strong>[DATE_EXPIRATION]</strong> : Date d'expiration</li>
                                    <li><strong>[EXPIRE_DANS]</strong> : Jours restants avant expiration</li>
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
        function highlightEmails() {
            const input = document.getElementById('emails');
            const display = document.getElementById('emails-display');

            // Récupérer la valeur du champ de saisie
            const inputValue = input.value;

            // Séparer les emails en utilisant le point-virgule comme séparateur
            const emails = inputValue.split(';').map(email => email.trim()).filter(email => email);

            // Vider l'affichage
            display.innerHTML = '';

            // Ajouter chaque email dans l'affichage
            emails.forEach(email => {
                const emailTag = document.createElement('div');
                emailTag.classList.add('email-tag');
                emailTag.textContent = email;
                display.appendChild(emailTag);
            });
        }

    </script>
@endpush

