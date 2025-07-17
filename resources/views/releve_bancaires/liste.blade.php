@php
    use Carbon\Carbon;
@endphp
@extends('layouts.main')

@section('document-title', 'Relevés bancaires')

@push('styles')
    @include('layouts.partials.css.__datatable_css')

    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">

@endpush

@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- ##### Card Title ##### -->
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <a href="{{route('comptes.liste')}}"><i class="fa fa-arrow-left"></i></a>
                                <i class="fa fa-money-check me-2 text-success"></i> Relevés bancaires
                            </h5>
                            <div class="page-title-right">

                                <button class="btn btn-soft-success" data-bs-toggle="modal" data-bs-target="#releveBancaireModal">+
                                   Ajouter un relevé bancaire
                                </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>

                    <!-- ##### DataTable ##### -->
                    <div class="row px-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatable">
                                    <thead>
                                    <tr>
                                        <th style="width: 20px">
                                            <input type="checkbox" class="form-check-input" id="select-all-row">
                                        </th>
                                        <th>Compte</th>
                                        <th>Mois</th>
                                        <th>Lien</th>
                                        <th>Actions</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>



                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="releveBancaireModal" tabindex="-1" aria-labelledby="releveBancaireModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="affaireModalTitle">Ajouter un relevé bancaire</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label required" for="bank-select">Banque</label>
                        <select class="form-control mb-3" name="bank" id="bank-select" required>
                            <option value="">Sélectionnez une banque</option>
                            @foreach($o_comptes as $compte)
                                <option value="{{ $compte->id }}">{{ $compte->nom }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="month-select">Mois</label>
                        <select class="form-control mb-3" name="month" id="month-select" required>
                            <option value="">Sélectionnez un mois</option>
                            <option value="1">Janvier</option>
                            <option value="2">Février</option>
                            <option value="3">Mars</option>
                            <option value="4">Avril</option>
                            <option value="5">Mai</option>
                            <option value="6">Juin</option>
                            <option value="7">Juillet</option>
                            <option value="8">Août</option>
                            <option value="9">Septembre</option>
                            <option value="10">Octobre</option>
                            <option value="11">Novembre</option>
                            <option value="12">Décembre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label required" for="url-input">Lien</label>
                        <input type="url" class="form-control mb-3" name="url" id="url-input" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" id="add-releve-bancaire" class="btn btn-primary">Ajouter</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('layouts.partials.js.__datatable_js')
    <script src="{{ asset('libs/moment/min/moment-with-locales.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('libs/bootstrap-datepicker/locales/bootstrap-datepicker.fr.min.js') }}"></script>
    <script src="{{ asset('libs/daterangepicker/js/daterangepicker.js') }}"></script>
    <script>
        const __url_add = "{{route('releve-bancaire.sauvegarder')}}";
    </script>
    <script>
        const __dataTable_columns= [
            { data: 'selectable_td', orderable: false, searchable: false, class: 'check_sell' },
            { data: 'compte_id', name: 'compte_id' },
            { data: 'month', name: 'month' },
            { data: 'url', name: 'url' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ];
        const __dataTable_ajax_link= "{{ route('releve-bancaire.liste') }}";
        const __dataTable_id= "#datatable";


        @php
            $exercice = session()->get('exercice')
        @endphp

        const __datepicker_dates = {
            "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 1': ['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2': ['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3': ['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4': ['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };

        const __datepicker_end_extend_date = '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}';


        const __dataTable_filter_trigger_button_id= '#search-btn';

        const datepicker_locale = {
            format: "DD/MM/YYYY",
            separator: " - ",
            applyLabel: "Appliquer",
            cancelLabel: "Annuler",
            fromLabel: "De",
            toLabel: "à",
            customRangeLabel: "Plage personnalisée",
            weekLabel: "S",
            daysOfWeek: [
                "Di",
                "Lu",
                "Ma",
                "Me",
                "Je",
                "Ve",
                "Sa"
            ],
            monthNames: [
                "Janvier",
                "Février",
                "Mars",
                "Avril",
                "Mai",
                "Juin",
                "Juillet",
                "Août",
                "Septembre",
                "Octobre",
                "Novembre",
                "Décembre"
            ],
            firstDay: 1
        }

    </script>
    <script>
        $('#releveBancaireModal').on('show.bs.modal', function () {
            clearModalErrors();
        });
        function clearModalErrors() {
            $('#bank-select').removeClass('is-invalid');
            $('#month-select').removeClass('is-invalid');
            $('#url-input').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        $(document).on('click', "#add-releve-bancaire", function () {
            // Clear previous error messages
            $('#bank-select').removeClass('is-invalid');
            $('#month-select').removeClass('is-invalid');
            $('#url-input').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            // Préparation des données pour la requête AJAX
            let compte_id = $('#bank-select').val();  // Titre de la pièce jointe
            let url = $('#url-input').val();  // URL de la pièce jointe
            let month = $('#month-select').val();  // URL de la pièce jointe

            // Préparation des données à envoyer avec la requête
            let data = {
                compte_id: compte_id,
                url: url,
                month : month,
                year : '{{ $exercice }}'
            };

            // Envoi de la requête AJAX pour attacher le document
            $.ajax({
                url: __url_add,  // URL du serveur pour attacher le document
                method: 'POST',
                data: data,
                headers:{
                    'X-CSRF-TOKEN' : __csrf_token
                },
                success: function(response) {
                    // Si la réponse est "Document attché", afficher un message de succès
                    if (response === 'Relevé bancaire ajouté avec succès') {
                        $('#pieceJointeModal').modal('hide'); // Fermer la fenêtre modale en cas de succès
                        location.reload();
                    } else {
                        // Si la réponse n'est pas celle attendue, afficher l'erreur
                        toastr.error('Erreur: ' + response);
                    }
                },
                error: function(xhr, status, error) {
                    // Gérer les erreurs lors de la requête AJAX
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.compte_id) {
                            $('#bank-select').addClass('is-invalid');
                            $('#bank-select').after('<div class="invalid-feedback">' + errors.compte_id[0] + '</div>');
                        }
                        if (errors.month) {
                            $('#month-select').addClass('is-invalid');
                            $('#month-select').after('<div class="invalid-feedback">' + errors.month[0] + '</div>');
                        }
                        if (errors.url) {
                            $('#url-input').addClass('is-invalid');
                            $('#url-input').after('<div class="invalid-feedback">' + errors.url[0] + '</div>');
                        }
                    } else {
                        toastr.error('Une erreur est survenue : ' + error);
                    }
                }
            });
        });

    </script>

    <script type="module" src="{{ asset('js/dataTable_init.js') }}"></script>
@endpush
