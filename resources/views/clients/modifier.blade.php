@extends('layouts.main')
@section('document-title', 'Clients')
@push('styles')
    @include('layouts.partials.css.__datatable_css')
    <link rel="stylesheet" href="{{ asset('libs/select2/css/select2.min.css') }}">
    <link href="{{ asset('libs/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('libs/daterangepicker/css/daterangepicker.min.css') }}" rel="stylesheet">
    <style>
        .table.table-head-bg thead th,
        .table.table-head-bg thead tr {
            background-color: #f3f6f9;
            border-bottom: 0;
            letter-spacing: 1px;
        }

        .centered-cell {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .radio {
            width: 20px !important;
            height: 20px !important;
        }
    </style>
@endpush
@section('page')
    <form action="{{ route('clients.mettre_a_jour', $o_client->id) }}" method="POST" class="needs-validation">
        @csrf
        <input type="hidden" id="contacts_principal" name="contacts_principal">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div class="d-flex switch-filter justify-content-between align-items-center" id="__fixed">
                                <div>
                                    <a href="{{ route('clients.liste') }}"><i class="fa fa-arrow-left"></i></a>
                                    <h5 class="m-0 float-end ms-3"><i class="mdi mdi-contacts  me-2 text-success"></i>
                                        Modifier un client
                                    </h5>
                                </div>

                                <div class="pull-right">
                                    <button class="btn btn-soft-info"><i class="fa fa-save"></i> <span class="d-none d-sm-inline" >Sauvegarder</span></button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>


                        @method('PUT')
                        <div class="row px-3 align-items-start ">
                            <div class="row col-md-6 ">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">
                                        Informations juridiques</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="reference " class="form-label required">
                                        Référence
                                    </label>
                                    <input type="text" class="form-control @error('reference') is-invalid @enderror"
                                           id="reference" @if(!$modifier_reference) readonly @endif
                                           value="{{ old('reference', $o_client->reference) }}" name="reference"
                                           maxlength="20"
                                           required >
                                    @error('reference')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="formJuridique"
                                           class="form-label required">Forme juridique</label>
                                    <select class="form-control @error('forme_juridique') is-invalid @enderror"
                                            id="formJuridique" name="forme_juridique">

                                        @foreach ($form_juridique_types as $form_juridique)
                                            <option value="{{ $form_juridique->id }}"
                                                    id="{{ $form_juridique->nom_sur_facture }}"
                                                {{old('forme_juridique', $o_client->forme_juridique_id) == $form_juridique->id ? 'selected' : '' }}>
                                                {{ $form_juridique->nom }}</option>
                                        @endforeach
                                    </select>
                                    @error('forme_juridique')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="nom" class="form-label required" id="dynamic_label">
                                        Dénomination
                                    </label>
                                    <input type="text" class="form-control @error('nom')is-invalid @enderror"
                                           value="{{ old('name', $o_client->nom) }}"
                                           id="nom" name="nom" required>
                                    @error('nom')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="ville" class="form-label">
                                        Ville
                                    </label>
                                    <input type="text" class="form-control @error('ville') is-invalid @enderror"
                                           value="{{ old('ville', $o_client->ville) }}"
                                           id="ville" name="ville">
                                    @error('ville')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="ice" class="form-label">
                                        ICE
                                    </label>
                                    <input type="text" class="form-control @error('ice') is-invalid @enderror"
                                           value="{{ old('ice', $o_client->ice) }}"
                                           id="ice" name="ice">
                                    @error('ice')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="email" class="form-label">
                                        Email
                                    </label>
                                    <input class="form-control @error('email') is-invalid @enderror" type="email"
                                           value="{{ old('email', $o_client->email) }}"
                                           name="email" id="example-email-input1">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-12 col-lg-6 mb-3">
                                    <label for="telephone" class="form-label">
                                        Téléphone
                                    </label>
                                    <input type="tel" class="form-control @error('telephone') is-invalid @enderror"
                                           id="telephone"
                                           value="{{ old('telephone', $o_client->telephone) }}" name="telephone">
                                    @error('telephone')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                {{-- </div> --}}
                               <div class="col-12 mx-0 row">
                                   <div class="col-12 px-0 col-lg-6 mb-3">
                                       <label for="note" class="form-label ">
                                           Note
                                       </label>
                                       <textarea class="form-control @error('note') is-invalid @enderror"
                                                 style="resize: vertical" placeholder="Ajouter note ici ....." id="note"
                                                 name="note" cols="30"
                                                 rows="5">{{ old('note', $o_client->note) }}</textarea>
                                       @error('note')
                                       <div class="invalid-feedback">
                                           {{ $message }}
                                       </div>
                                       @enderror
                                   </div>
                                   <div class="col-12 px-0 col-lg-6 mb-3">
                                       <label for="note" class="form-label">
                                           Adress
                                       </label>
                                       <textarea class="form-control @error('adresse') is-invalid @enderror"
                                                 style="resize: vertical" placeholder="Ajouter adress ici ....."
                                                 id="adresse"
                                                 name="adresse" cols="30"
                                                 rows="5">{{ old('adresse', $o_client->adresse) }}</textarea>
                                       @error('adresse')
                                       <div class="invalid-feedback">
                                           {{ $message }}
                                       </div>
                                       @enderror
                                   </div>
                               </div>
                               </div>
                            <div class="col-sm-6 row mx-0 a col-12 align-items-start">
                                <div class="col-12 mt-2">
                                    <h5 class="text-muted">Gestion</h5>
                                    <hr class="border border-success">
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label " for="limite_de_credit-input">Limite de crédit
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                               class="form-control @error('limite_de_credit') is-invalid @enderror"
                                               id="limite_de_credit" name="limite_de_credit"
                                               value="{{old('limite_de_credit',$o_client->limite_de_credit) }}">
                                            <span class="input-group-text" id="basic-addon1">MAD</span>
                                        @error('limite_de_credit')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label " for="limite_ventes_impayees-input">Limite de ventes impayées
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="1" min="0"
                                               class="form-control @error('limite_ventes_impayees') is-invalid @enderror"
                                               id="limite_ventes_impayees" name="limite_ventes_impayees"
                                               value="{{old('limite_ventes_impayees',$o_client->limite_ventes_impayees) }}">
                                            <span class="input-group-text" id="basic-addon1">ventes</span>
                                        @error('limite_ventes_impayees')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-lg-6 mb-3 ">
                                    <label class="form-label " for="remise_par_defaut-input">Remise par défaut
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01"
                                               class="form-control @error('remise_par_defaut') is-invalid @enderror"
                                               id="remise_par_defaut" name="remise_par_defaut"
                                               value="{{old('remise_par_defaut',$o_client->remise_par_defaut) }}">
                                        <span class="input-group-text" id="basic-addon1">%</span>
                                        @error('remise_par_defaut')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- #####--Card Title--##### -->
                        <div class="card-title">
                            <div
                                class="d-flex switch-filter justify-content-between align-items-center">
                                <h5 class="m-0"><i class="mdi mdi-contacts me-2 text-success"></i>
                                    Contacts
                                </h5>
                                <div class="pull-right">
                                    <button id="addContact" type="button" class="btn btn-soft-success"><i
                                            class="fa fa-plus me-2 "></i>
                                        Ajouter un
                                        contact
                                    </button>
                                </div>
                            </div>
                            <hr class="border">
                        </div>
                        <div class="table-responsive">
                            <table id="table-contacts" data-repeater-list="contacts"
                                   class="table table-head-custom table-head-bg table-borderless table-vertical-center">
                                <thead>
                                <tr>
                                    <th>
                                        <span class="form-label">Nom</span>
                                    </th>
                                    <th><span class="form-label">Prénom</span></th>
                                    <th><span class="form-label">E-mail</span></th>
                                    <th><span class="form-label">Téléphone</span></th>
                                    <th><span class="form-label">Contact principal</span></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(old('contacts_nom'))
                                    @foreach(old('contacts_nom') as $key => $c)
                                        <tr>
                                            <input type="hidden" name="contacts_id[]" value="{{old('contacts_id[]')? old('contacts_id[]')[$key] ?? '' :''}}">
                                            <td>
                                                <input type="text" class="form-control {{$errors->has('contacts_nom.'.$key) ? 'is-invalid' : null}}" name="contacts_nom[]" value="{{old('contacts_nom')[$key]}}">
                                                @error('contacts_nom.'.$key)
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control {{$errors->has('contacts_prenom.'.$key) ? 'is-invalid' : null}}" name="contacts_prenom[]" value="{{old('contacts_prenom')[$key]}}">
                                                @error('contacts_prenom.'.$key)
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control {{$errors->has('contacts_email.'.$key) ? 'is-invalid' : null}}" name="contacts_email[]" value="{{old('contacts_email')[$key]}}">
                                                @error('contacts_email.'.$key)
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control {{$errors->has('contacts_telephone.'.$key) ? 'is-invalid' : null}}" name="contacts_telephone[]" value="{{old('contacts_telephone')[$key]}}">
                                                @error('contacts_telephone.'.$key)
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </td>
                                            <td class="centered-cell">
                                                <input type="radio" class="radio form-check-input"
                                                                             onchange="handleRadioChange(this)"
                                                                             name="contacts_principal" value="{{$key}}"
                                                    {{ (int) old('contacts_principal') === $key ? 'checked' : '' }}>
                                                <span></span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="deleteContactRow(this)"><i class="fa fa-trash-alt" ></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    @foreach ($o_client->contacts as $key => $contact)
                                        <tr>
                                            <input type="hidden" name="contacts_id[]" value="{{ $contact->id }}">
                                            <td><input type="text" class="form-control" name="contacts_nom[]"
                                                       value="{{ $contact->nom }}"></td>
                                            <td><input type="text" class="form-control" name="contacts_prenom[]"
                                                       value="{{ $contact->prenom }}"></td>
                                            <td><input type="text" class="form-control" name="contacts_email[]"
                                                       value="{{ $contact->email }}"></td>
                                            <td><input type="text" class="form-control"
                                                       name="contacts_telephone[]" value="{{ $contact->telephone }}">
                                            </td>
                                            <td class="centered-cell"><input type="radio"
                                                                             class="radio form-check-input"
                                                                             onchange="handleRadioChange(this)"
                                                                             name="contacts_principal" value="{{$key}}"
                                                    {{ $contact->is_principal ? 'checked' : '' }}>
                                                <span></span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-soft-danger" onclick="deleteContactRow(this)"><i class="fa fa-trash-alt" ></i></button>
                                            </td>
                                        </tr>
                                    @endforeach

                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('scripts')

    <script>
        var table = document.getElementById("table-contacts").getElementsByTagName('tbody')[0];
        let rowIndexCounter = table.rows.length + 1;
        document.addEventListener('DOMContentLoaded', function () {
            $('#contacts_principal').val(getSelectedRow());
            $('#formJuridique').select2({
                width: '100%',
                placeholder: 'Selectioner un type'
            })
            $('#addContact').click(function () {
                addContactRow();
            });
        });
        function addContactRow(e) {
            var table = document.getElementById("table-contacts").getElementsByTagName('tbody')[0];
            var newRow = table.insertRow(table.rows.length);
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            var cell5 = newRow.insertCell(4);
            var cell6 = newRow.insertCell(5);
            cell5.classList.add("centered-cell")
            // console.log('table.rows.length is ',table.rows.length)
            var rowIndex = rowIndexCounter++;
            cell1.innerHTML = '<input type="text" class="form-control required" name="contacts_nom[]" required>';
            cell2.innerHTML = '<input type="text" class="form-control required" name="contacts_prenom[]" required>';
            cell3.innerHTML = '<input type="text" class="form-control" name="contacts_email[]">';
            cell4.innerHTML = '<input type="text" class="form-control" name="contacts_telephone[]">';
            cell5.innerHTML =
                '<input type="radio" class="radio form-check-input" onchange="handleRadioChange(this)" name="contacts_principal[]" value=' +
                rowIndex +
                '><span></span><input type="hidden" class="form-control" name="contacts_nouvelle[]" value = ' + rowIndex +
                ' >';

            cell6.innerHTML =
                '<button type="button" class="btn btn-sm btn-soft-danger" onclick="deleteContactRow(this)"><i class="fa fa-trash-alt" ></i></button>'
        }
        function deleteContactRow(btn) {
            var row = btn.parentNode.parentNode;
            row.parentNode.removeChild(row);
            updateSelelcted();
        }

        function getSelectedRow() {
            var selectedRow = $('#table-contacts tbody tr').index($('#table-contacts tbody tr:has(.radio:checked)'));
            return selectedRow;
        }

        function updateSelelcted() {
            $('#contacts_principal').val(getSelectedRow());
        }

        function handleRadioChange(radioButton) {
            // Get the selected value
            updateSelelcted();
        }
    </script>
@endpush
