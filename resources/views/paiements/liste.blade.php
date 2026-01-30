@php
    use Carbon\Carbon;
    use App\Models\Vente;
@endphp
@extends('layouts.main')
@section('document-title', 'Paiements')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <style>
        .last-col {
            width: 1%;
            white-space: nowrap;
        }
    </style>
@endpush
@section('page')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <!-- #####--Card Title--##### -->
                    <div class="card-title">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="m-0">
                                <i class="mdi mdi-chart-bell-curve-cumulative me-2 text-success"></i> Paiements
                            </h5>
                            <div class="page-title-right">
                                <button class="btn btn-soft-info text-nowrap mx-2 " data-bs-target="#paiement-modal"
                                        data-bs-toggle="modal"><i class="mdi mdi-repeat"></i> Transfert
                                </button>
                                <button class="filter-btn btn btn-soft-info"><i class="fa fa-filter"></i> Filtrer
                                </button>
                            </div>
                        </div>
                        <hr class="border">
                    </div>
                    <!-- #####--Filters--##### -->
                    <div class="switch-filter row px-3 d-none mt-2 mb-4">
                        <div class="col-sm-3 col-12 mb-3">
                            <label for="i_date" class="form-label">Date d'opération</label>
                            <div class="input-group  border-1 border border-light rounded" id="datepicker1">
                                <input type="text" class="form-control datepicker text-primary ps-2 " id="i_date"
                                    placeholder="mm/dd/yyyy" readonly>
                                <span class="input-group-text text-primary"><i class="mdi mdi-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="montant-input">Montant</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="montant-input">
                                <span class="input-group-text">MAD</span>
                            </div>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="compte-select">Compte</label>
                            <select class="select2 form-control mb-3 custom-select" id="compte-select">
                                <option value="">Sélectionnez un compte</option>
                                @foreach ($comptes as $compte)
                                    <option value="{{ $compte->id }}">{{ $compte->nom }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="methode-select">Méthode de paiement</label>
                            <select class="select2 form-control mb-3 custom-select" id="methode-select">
                                <option value="">Sélectionnez une méthode</option>
                                @foreach ($methodes as $methode)
                                    <option value="{{ $methode->key }}">{{ $methode->nom }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="compte-select">Type d'opération</label>
                            <select class="form-select mb-3" id="type-select">
                                <option value="">Tous</option>
                                <option value="vente">Vente</option>
                                <option value="achat">Achat</option>
                                <option value="depense">Dépense</option>
                                <option value="banque">Opération bancaire</option>
                            </select>
                        </div>

                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="client-select">Client</label>
                            <select class="select2 form-control mb-3 custom-select" id="client-select">
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="fournisseur-select">Fournisseur</label>
                            <select class="select2 form-control mb-3 custom-select" id="fournisseur-select">
                            </select>
                        </div>
                        <div class="col-sm-3 col-12 mb-3">
                            <label class="form-label" for="fournisseur-select">Ajouter par</label>
                            <select class="select2 form-control mb-3 custom-select" id="user-select">
                                <option value="">Tous</option>
                            @foreach ($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 d-flex justify-content-end">
                            <button id="search-btn" class="btn btn-primary"><i class="mdi mdi-magnify"></i> Rechercher
                            </button>
                        </div>
                    </div> <!-- #####--DataTable--##### -->
                    <div class="row px-3">
                        <div class="card-title switch-filter d-none col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="m-0">Liste de paiements</h5>
                            </div>
                            <hr class="border">
                        </div>
                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="width: 20px">
                                                <input type="checkbox" class="form-check-input" id="select-all-row">
                                            </th>
                                            <th>Date</th>
                                            <th>Element payé</th>
                                            <th>Débit</th>
                                            <th>Crédit</th>
                                            <th>Compte</th>
                                            <th style="max-width: 10%">Méthode de paiement</th>
                                            <th>Ajouter par</th>
                                            <th style="min-width: 15%">Objet</th>
                                            <th style="max-width: 180px">Actions</th>
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
    <div class="modal fade" id="paiement-edit-modal" tabindex="-1" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
            </div>
        </div>
    </div>
    <div class="modal fade" id="paiement-modal" tabindex="-1" aria-labelledby="paiement-modal-title" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title align-self-center" id="edit-cat-modal-title">
                        Ajouter un transfert de caisse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="post" id="paiement_form" action="{{ route('transferts_caisse.sauvegarder') }}"
                      autocomplete="off">
                    @csrf
                    <div class="modal-body">
                        <!-- Compte source -->
                        <div class="col-12 mt-3">
                            <label class="form-label required" for="compte-source">Compte source</label>
                            <select class="select2 form-control mb-3 @error('compte_source') is-invalid @enderror" required
                                    id="compte-source" name="compte_source">
                                <option value="">Sélectionnez un compte</option>
                                @foreach ($comptes as $compte1)
                                    <option value="{{ $compte1->id}}">{{ $compte1->nom}}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Compte destination -->
                        <div class="col-12 mt-3">
                            <label class="form-label required" for="compte_destination">Compte destination</label>

                            <select class="select2 form-control mb-3 compte-destination @error('compte_destination') is-invalid @enderror" required
                                    id="compte-destination" name="compte_destination" >
                                <option value="">Sélectionnez un compte</option>
                                @foreach ($comptes as $compte2)
                                    <option value="{{$compte2->id}}">{{$compte2->nom}}</option>
                                @endforeach
                            </select>
                            @error('compte_destination')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date d'émission -->
                        <div class="col-12 mt-3">
                            <label for="date_emission" class="form-label required">Date d'émission</label>
                            <div class="input-group">
                                <input class="form-control datupickeru @error('date_emission') is-invalid @enderror"
                                       data-provide="datepicker"
                                       data-date-autoclose="true"
                                       type="text"
                                       name="date_emission"
                                       value="{{ old('date_emission', now()->format('d/m/Y')) }}"
                                       id="date_emission" required>
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                            @error('date_emission')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Date de réception -->
                        <div class="col-12 mt-3">
                            <label for="date_reception" class="form-label required">Date de réception</label>
                            <div class="input-group">
                                <input class="form-control datupickeru @error('date_reception') is-invalid @enderror"
                                       data-provide="datepicker"
                                       data-date-autoclose="true"
                                       type="text"
                                       name="date_reception"
                                       value="{{ old('date_reception', now()->format('d/m/Y')) }}"
                                       id="date_reception" required>
                                <span class="input-group-text"><i class="fa fa-calendar-alt"></i></span>
                            </div>
                            @error('date_reception')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 mt-3">
                            <label for="method-input" class="form-label required">Méthode de paiement</label>
                            <select required name="i_method_key" class="form-select " style="width: 100%"
                                    id="method-input">
                                <option value="">Sélectionnez une méthode</option>
                            @foreach ($methodes as $methode)
                                        <option value="{{ $methode->key }} " {{ old('i_method_key') == $methode->key ? 'selected' : '' }}>{{ $methode->nom }}</option>
                                @endforeach
                            </select>
                            @error('i_method_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Montant de paiement -->
                        <div class="col-12 mt-3">
                            <label for="montant" class="form-label required">Montant de paiement</label>
                            <div class="input-group">
                                <input class="form-control @error('i_montant') is-invalid @enderror"
                                       step="0.001" min="0.001"
                                       type="number" name="i_montant"
                                       id="montant"
                                       value="{{ old('i_montant') }}"  required>
                                <span class="input-group-text">MAD</span>
                            </div>
                            @error('i_montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-12 mt-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" cols="30" rows="3" class="form-control">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fermer</button>
                        <button class="btn btn-info">Payer</button>
                    </div>
                </form>
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
        // Get the select elements
        const sourceSelect = document.getElementById('compte-source');
        const destinationSelect = document.getElementById('compte-destination');

        // Function to update destination options
        function updateDestinationOptions() {
            const sourceValue = sourceSelect.value;

            // Enable all options in destination first
            Array.from(destinationSelect.options).forEach(option => {
                option.disabled = false;
            });

            // Disable the selected source option in destination
            if (sourceValue) {
                const destinationOption = destinationSelect.querySelector(`option[value="${sourceValue}"]`);
                if (destinationOption) {
                    destinationOption.disabled = true;
                }
            }

            // If the currently selected destination is now disabled, clear it
            if (destinationSelect.value === sourceValue) {
                destinationSelect.value = '';
            }

            // Refresh Select2
            $(destinationSelect).select2(
                {
                    width: '100%',
                    placeholder: 'Sélectionnez un compte',
                    minimumInputLength: -1,
                    minimumResultsForSearch: -1,
                    allowClear: true
                }
            );
        }

        // Function to update source options
        function updateSourceOptions() {
            const destinationValue = destinationSelect.value;

            // Enable all options in source first
            Array.from(sourceSelect.options).forEach(option => {
                option.disabled = false;
            });

            // Disable the selected destination option in source
            if (destinationValue) {
                const sourceOption = sourceSelect.querySelector(`option[value="${destinationValue}"]`);
                if (sourceOption) {
                    sourceOption.disabled = true;
                }
            }

            // If the currently selected source is now disabled, clear it
            if (sourceSelect.value === destinationValue) {
                sourceSelect.value = '';
            }

            // Refresh Select2
            $(sourceSelect).select2(
                {
                    width: '100%',
                    placeholder: 'Sélectionnez un compte',
                    minimumInputLength: -1,
                    minimumResultsForSearch: -1,
                    allowClear: true
                }
            );
        }


        // Add event listeners
        $(sourceSelect).on('change', updateDestinationOptions);
        $(destinationSelect).on('change', updateSourceOptions);

        // Initial update
        updateDestinationOptions();
        updateSourceOptions();
    </script>
    <script>
        const __dataTable_columns = [{
                data: 'selectable_td',
                orderable: false,
                searchable: false,
                class: 'check_sell'
            },
            {
                data: 'date_paiement',
                name: 'date_paiement'
            },
            {
                data: 'payable_id',
                name: 'payable_id'
            },
            {
                data: 'decaisser',
                name: 'decaisser'
            },
            {
                data: 'encaisser',
                name: 'encaisser'
            },
            {
                data: 'compte_id',
                name: 'compte_id'
            },
            {
                data: 'methode_paiement_key',
                name: 'methode_paiement_key'
            },
            {
                data:'created_by',
                name:'created_by',
                visible: false
            },
            {
                data:'objet',
                name:'objet'
            },
            {
                data: 'actions',
                name: 'actions',
                orderable: false,
            },
        ];
        const __dataTable_ajax_link = "{{ route('paiement.liste') }}";
        const __dataTable_id = "#datatable";
        const __dataTable_filter_inputs_id = {
            client_id: '#client-select',
            date: '#i_date',
            montant: '#montant-input',
            compte_id: '#compte-select',
            methode_paiement_key: '#methode-select',
            type_op: '#type-select',
            fournisseur_id: '#fournisseur-select',
            created_by: '#user-select',
        }
        const __dataTable_filter_trigger_button_id = '#search-btn';
    </script>
    <script src="{{ asset('js/dataTable_init.js') }}"></script>
    <script>
        $('.filter-btn').click(e => {
            $('.switch-filter').toggleClass('d-none')
        })
        $('#client-select').select2({
            width: '100%',
            placeholder: 'Sélectionnez un client',
            minimumInputLength: 3, // Specify the ajax options for loading the product data
            ajax: {
                // The URL of your server endpoint that returns the product data
                url: __client_select2_route,
                cache: true, // The type of request, GET or POST
                type: 'GET',
                processResults: function(data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                },
            },
            allowClear: true
        })
        $('#fournisseur-select').select2({
            width: '100%',
            placeholder: 'Sélectionnez un fournisseur',
            minimumInputLength: 3, // Specify the ajax options for loading the product data
            ajax: {
                // The URL of your server endpoint that returns the product data
                url: __fournisseur_select2_route,
                cache: true, // The type of request, GET or POST
                type: 'GET',
                processResults: function(data) {
                    // Transforms the top-level key of the response object from 'items' to 'results'
                    return {
                        results: data
                    };
                },
            },
            allowClear: true
        });
        $('#compte-select, #compte-source, #compte-destination, #method-input').select2({
            width: '100%',
            placeholder: 'Sélectionnez un compte',
            minimumInputLength: -1,
            minimumResultsForSearch: -1,
            allowClear: true
        });
        $('#methode-select').select2({
            width: '100%',
            placeholder: 'Sélectionnez une méthode',
            minimumInputLength: -1,
            minimumResultsForSearch: -1,
            allowClear: true
        });
        $('#user-select').select2({
            width: '100%',
            placeholder: 'Sélectionnez un utilisateur',
            minimumInputLength: -1,
            minimumResultsForSearch: -1,
            allowClear: true
        });
    </script>
    <script>
        @php
            $exercice = session()->get('exercice');
        @endphp
        const __datepicker_dates = {
            "Aujourd'hui": ['{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Hier': ['{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::yesterday()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 7 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(6)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Les 30 derniers jours': ['{{Carbon::today()->setYear($exercice)->subDays(29)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->format('d/m/Y')}}'],
            'Ce mois-ci': ['{{Carbon::today()->firstOfMonth()->setYear($exercice)->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfMonth()->format('d/m/Y')}}'],
            'Le mois dernier': ['{{Carbon::today()->setYear($exercice)->subMonths(1)->firstOfMonth()->format('d/m/Y')}}', '{{Carbon::today()->subMonths(1)->lastOfMonth()->format('d/m/Y')}}'],
            'Trimestre 1':['{{Carbon::today()->firstOfYear()->format('d/m/Y')}}','{{Carbon::today()->setMonth(3)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 2':['{{Carbon::today()->setMonth(4)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(6)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 3':['{{Carbon::today()->setMonth(7)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(9)->endOfMonth()->format('d/m/Y')}}'],
            'Trimestre 4':['{{Carbon::today()->setMonth(10)->firstOfMonth()->format('d/m/Y')}}','{{Carbon::today()->setMonth(12)->endOfMonth()->format('d/m/Y')}}'],
            'Cette année': ['{{Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y')}}', '{{Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y')}}'],
        };
        const __datepicker_start_date = '{{ Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y') }}';
        const __datepicker_end_date = '{{ Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y') }}';
        const __datepicker_min_date = '{{ Carbon::today()->setYear($exercice)->firstOfYear()->format('d/m/Y') }}';
        const __datepicker_max_date = '{{ Carbon::today()->setYear($exercice)->lastOfYear()->format('d/m/Y') }}';
        $('#i_date').daterangepicker({
            ranges: __datepicker_dates,
            locale: {
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
            },
            startDate: __datepicker_start_date,
            endDate: __datepicker_end_date,
            minDate: __datepicker_min_date,
            maxDate: __datepicker_max_date
        })
    </script>

    <script>
        var submit_paiement = !1;
        $(document).on('submit', '#paiement_form_edit', function(e) {
            e.preventDefault();
            if (!submit_paiement) {
                let spinner = $(__spinner_element);
                let btn = $('#paiement_form_edit').find('.btn-info');
                btn.attr('disabled', '').prepend(spinner)
                submit_paiement = !0;
                $.ajax({
                    url: $('#paiement_form_edit').attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    headers: {
                        'X-CSRF-Token': __csrf_token
                    },
                    success: function(response) {
                        btn.removeAttr('disabled');
                        submit_paiement = 0;
                        spinner.remove();
                        toastr.success(response);
                        $('#paiement-edit-modal').modal('hide');
                        location.reload()
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        btn.removeAttr('disabled');
                        submit_paiement = !1;
                        spinner.remove();
                        toastr.error(xhr.responseText);
                    }
                })
            }
        })
        $(document).on('change', '#method-input', function() {
            let value = $(this).val();
            if (value == 'cheque' || value == 'lcn') {
                $('.__variable').removeClass('d-none').find('input').attr('required', '')
            } else {
                $('.__variable').addClass('d-none').find('input').removeAttr('required')
            }
        })
        check()

        function check() {
            let methods = ['cheque', 'lcn'];
            if (methods.indexOf($('#method-input').find('option:selected').val()) !== -1) {
                $('.__variable').removeClass('d-none').find('input').attr('required', '')
            } else {
                $('.__variable').addClass('d-none').find('input').removeAttr('required')
            }
        }

        function checkModal() {
            let methods = ['cheque', 'lcn'];
            if (methods.indexOf($('#paiement_form_edit #method-input').find('option:selected').val()) !== -1) {
                $('.__variable').removeClass('d-none').find('input').attr('required', '')
            } else {
                $('.__variable').addClass('d-none').find('input').removeAttr('required')
            }
        }
    </script>
@endpush
